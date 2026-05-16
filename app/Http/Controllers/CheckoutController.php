<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\CheckoutIndexRequest;
use App\Http\Requests\Checkout\CheckoutStoreRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Seller;
use App\Support\DeliveryEstimate;
use App\Notifications\SellerNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected function ensureHasDeliveryAddress(Collection $selectedCartItemIds)
    {
        $hasSavedAddress = Auth::user()?->addresses()->exists() ?? false;

        if ($hasSavedAddress) {
            return null;
        }

        $checkoutReturnUrl = route('checkout.index', [
            'selected_cart_items' => $selectedCartItemIds->all(),
        ]);

        return redirect()
            ->route('buyer.addresses', ['return_to' => $checkoutReturnUrl])
            ->with('error', 'Please add a delivery address before placing an order.');
    }

    protected function containsOwnedProducts($cartItems): bool
    {
        return $cartItems->contains(function ($item) {
            return (int) ($item->product->user_id ?? 0) === (int) Auth::id();
        });
    }

    protected function unavailableProducts($cartItems)
    {
        return $cartItems->filter(function ($item) {
            $product = $item->product;

            return !$product
                || $product->status !== \App\Models\Product::STATUS_APPROVED
                || !$product->is_active
                || $product->user?->sellerProfile?->application_status !== \App\Models\Seller::STATUS_APPROVED
                || ($product->activeVariants->isNotEmpty() && ! $item->variant)
                || (int) ($item->variant?->stock ?? $product->stock) < (int) $item->quantity;
        });
    }

    protected function calculateCartTotals($cartItems): array
    {
        $subtotal = 0;
        $shippingFee = 0;

        foreach ($cartItems as $item) {
            $price = (float) ($item->variant?->price ?? $item->product->price ?? 0);
            $itemShipping = (float) ($item->product->shipping_fee ?? 0);
            $quantity = (int) $item->quantity;

            $subtotal += $price * $quantity;
            $shippingFee += $itemShipping * $quantity;
        }

        return [
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'total' => $subtotal + $shippingFee,
        ];
    }

    protected function groupedCartItemsBySeller(Collection $cartItems): Collection
    {
        return $cartItems
            ->filter(fn($item) => $item->product && $item->product->user_id)
            ->groupBy(fn($item) => (int) $item->product->user_id)
            ->sortKeys();
    }

    public function index(CheckoutIndexRequest $request)
    {
        $validated = $request->validated();

        $selectedCartItemIds = collect($validated['selected_cart_items'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $cartQuery = Cart::with(['product.user.sellerProfile', 'product.activeVariants', 'variant'])
            ->where('user_id', Auth::id())
            ->when($selectedCartItemIds->isNotEmpty(), function ($query) use ($selectedCartItemIds) {
                $query->whereIn('id', $selectedCartItemIds);
            });

        $cartItems = $cartQuery->get();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Select at least one cart item before checkout.')
                ->with('selected_cart_item_ids', $selectedCartItemIds->all());
        }

        if ($this->containsOwnedProducts($cartItems)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'You cannot checkout your own products.')
                ->with('selected_cart_item_ids', $selectedCartItemIds->all());
        }

        if ($this->unavailableProducts($cartItems)->isNotEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'One or more selected products are no longer available in the requested quantity.')
                ->with('selected_cart_item_ids', $selectedCartItemIds->all());
        }

        if ($addressRedirect = $this->ensureHasDeliveryAddress($selectedCartItemIds)) {
            return $addressRedirect;
        }

        $totals = $this->calculateCartTotals($cartItems);
        $groupedCartItems = $this->groupedCartItemsBySeller($cartItems);

        $defaultAddress = Auth::user()->addresses()
            ->orderByDesc('is_default')
            ->latest('id')
            ->first();

        $deliveryEstimates = $groupedCartItems
            ->map(fn (Collection $sellerCartItems) => DeliveryEstimate::forSellerCartItems($sellerCartItems, $defaultAddress));
        $overallDeliveryEstimate = DeliveryEstimate::combined($deliveryEstimates);

        $selectedCartItemIds = $cartItems->pluck('id')->values();

        $paymentMethods = Order::paymentMethods();
        $selectedPaymentMethod = old('payment_method', Order::PAYMENT_METHOD_COD);

        return view('checkout.index', [
            'cartItems' => $cartItems,
            'groupedCartItems' => $groupedCartItems,
            'deliveryEstimates' => $deliveryEstimates,
            'overallDeliveryEstimate' => $overallDeliveryEstimate,
            'subtotal' => $totals['subtotal'],
            'shippingFee' => $totals['shippingFee'],
            'total' => $totals['total'],
            'defaultAddress' => $defaultAddress,
            'hasSavedAddress' => true,
            'selectedCartItemIds' => $selectedCartItemIds,
            'paymentMethods' => $paymentMethods,
            'selectedPaymentMethod' => $selectedPaymentMethod,
            'selectedPayment' => $paymentMethods[$selectedPaymentMethod] ?? reset($paymentMethods),
        ]);
    }

    public function store(CheckoutStoreRequest $request, SellerNotificationService $sellerNotifications)
    {
        $validated = $request->validated();

        $selectedCartItemIds = collect($validated['selected_cart_items'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $cartItems = Cart::with(['product.user.sellerProfile', 'product.activeVariants', 'variant'])
            ->where('user_id', Auth::id())
            ->when($selectedCartItemIds->isNotEmpty(), function ($query) use ($selectedCartItemIds) {
                $query->whereIn('id', $selectedCartItemIds);
            })
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Select at least one cart item to place an order.')
                ->with('selected_cart_item_ids', $selectedCartItemIds->all());
        }

        if ($this->containsOwnedProducts($cartItems)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'You cannot order your own products.')
                ->with('selected_cart_item_ids', $selectedCartItemIds->all());
        }

        if ($this->unavailableProducts($cartItems)->isNotEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'One or more selected products are no longer available in the requested quantity.')
                ->with('selected_cart_item_ids', $selectedCartItemIds->all());
        }

        if ($addressRedirect = $this->ensureHasDeliveryAddress($selectedCartItemIds)) {
            return $addressRedirect;
        }

        $groupedCartItems = $this->groupedCartItemsBySeller($cartItems);
        $checkoutGroup = (string) Str::uuid();
        $createdOrders = collect();
        $stockChecks = collect();

        try {
            DB::transaction(function () use ($cartItems, $groupedCartItems, $checkoutGroup, $validated, &$createdOrders, &$stockChecks) {
                $lockedProducts = Product::query()
                    ->with(['user.sellerProfile', 'activeVariants'])
                    ->whereIn('id', $cartItems->pluck('product_id')->filter()->unique()->values())
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $lockedVariants = ProductVariant::query()
                    ->whereIn('id', $cartItems->pluck('product_variant_id')->filter()->unique()->values())
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($groupedCartItems as $sellerId => $sellerCartItems) {
                    $totals = $this->calculateCartTotals($sellerCartItems);

                    $order = Order::create([
                        'user_id' => Auth::id(),
                        'seller_id' => (int) $sellerId,
                        'checkout_group' => $checkoutGroup,
                        'shipping_fee' => $totals['shippingFee'],
                        'total_price' => $totals['total'],
                        'status' => Order::STATUS_PENDING,
                        'shipping_status' => Order::SHIPPING_PENDING,
                        'payment_method' => $validated['payment_method'],
                        'payment_status' => Order::PAYMENT_PENDING,
                        'seller_earning_status' => Order::EARNING_PENDING,
                    ]);

                    foreach ($sellerCartItems as $item) {
                        $product = $lockedProducts->get($item->product_id);
                        $variant = $item->product_variant_id
                            ? $lockedVariants->get($item->product_variant_id)
                            : null;

                        if (
                            ! $product
                            || $product->status !== Product::STATUS_APPROVED
                            || ! $product->is_active
                            || $product->user?->sellerProfile?->application_status !== Seller::STATUS_APPROVED
                            || ($product->activeVariants->isNotEmpty() && ! $variant)
                            || ($variant && ((int) $variant->product_id !== (int) $product->id || ! $variant->is_active))
                            || (int) ($variant?->stock ?? $product->stock) < (int) $item->quantity
                        ) {
                            throw new \RuntimeException('One or more selected products are no longer available in the requested quantity.');
                        }

                        $previousStock = (int) $product->stock;

                        $order->items()->create([
                            'product_id' => $product->id,
                            'product_variant_id' => $variant?->id,
                            'variant_name' => $variant?->displayName(),
                            'variant_options' => $variant?->option_values,
                            'quantity' => $item->quantity,
                            'price' => $variant?->price ?? $product->price,
                            'shipping_fee' => $product->shipping_fee ?? 0,
                        ]);

                        if ($variant) {
                            $variant->decrement('stock', (int) $item->quantity);
                        }

                        $product->decrement('stock', (int) $item->quantity);

                        $stockChecks->push([
                            'product_id' => $product->id,
                            'previous_stock' => $previousStock,
                        ]);
                    }

                    $createdOrders->push($order);
                }

                Cart::query()
                    ->where('user_id', Auth::id())
                    ->whereIn('id', $cartItems->pluck('id'))
                    ->delete();
            });
        } catch (\RuntimeException $exception) {
            return redirect()
                ->route('cart.index')
                ->with('error', $exception->getMessage())
                ->with('selected_cart_item_ids', $selectedCartItemIds->all());
        }

        Order::with(['seller.sellerProfile', 'user', 'items'])
            ->whereIn('id', $createdOrders->pluck('id'))
            ->get()
            ->each(function (Order $order) use ($sellerNotifications): void {
                $sellerNotifications->newOrder($order);
            });

        $uniqueStockChecks = $stockChecks->unique('product_id')->values();

        Product::with('user.sellerProfile')
            ->whereIn('id', $uniqueStockChecks->pluck('product_id'))
            ->get()
            ->keyBy('id')
            ->each(function (Product $product) use ($uniqueStockChecks, $sellerNotifications): void {
                $stockCheck = $uniqueStockChecks->firstWhere('product_id', $product->id);

                if ($stockCheck) {
                    $sellerNotifications->checkProductStock($product, (int) $stockCheck['previous_stock']);
                }
            });

        $primaryOrder = $createdOrders->sortBy('id')->first();

        return redirect()
            ->route('buyer.orders.show', $primaryOrder)
            ->with('success', 'Order placed successfully using ' . $primaryOrder->paymentMethodLabel() . '.');
    }
}
