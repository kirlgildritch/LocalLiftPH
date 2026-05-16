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
use App\Services\CheckoutSummaryService;
use App\Services\VoucherService;
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

    protected function groupedCartItemsBySeller(Collection $cartItems): Collection
    {
        return $cartItems
            ->filter(fn($item) => $item->product && $item->product->user_id)
            ->groupBy(fn($item) => (int) $item->product->user_id)
            ->sortKeys();
    }

    public function index(CheckoutIndexRequest $request, CheckoutSummaryService $checkoutSummaryService, VoucherService $voucherService)
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

        $groupedCartItems = $this->groupedCartItemsBySeller($cartItems);

        $defaultAddress = Auth::user()->addresses()
            ->orderByDesc('is_default')
            ->latest('id')
            ->first();

        $deliveryEstimates = $groupedCartItems
            ->map(fn (Collection $sellerCartItems) => DeliveryEstimate::forSellerCartItems($sellerCartItems, $defaultAddress));
        $overallDeliveryEstimate = DeliveryEstimate::combined($deliveryEstimates);
        $totals = $checkoutSummaryService->totals($cartItems);
        $voucherCode = $validated['voucher_code'] ?? old('voucher_code');
        $voucherApplication = $voucherService->emptyApplication();

        if (filled($voucherCode)) {
            $voucherApplication = $voucherService->evaluateForCart($voucherCode, Auth::user(), $cartItems, $checkoutSummaryService);
        }

        $checkoutSummary = $checkoutSummaryService->build($cartItems, $deliveryEstimates, $voucherApplication);
        $selectedCartItemIds = $cartItems->pluck('id')->values();
        $availableSellerVouchers = $voucherService
            ->activeSellerVouchersForSellers($groupedCartItems->keys())
            ->map(function (Collection $sellerVouchers) use ($selectedCartItemIds) {
                return $sellerVouchers->map(function (array $voucher) use ($selectedCartItemIds) {
                    $voucher['apply_url'] = route('checkout.index', [
                        'selected_cart_items' => $selectedCartItemIds->all(),
                        'voucher_code' => $voucher['code'],
                    ]);

                    return $voucher;
                });
            });

        $paymentMethods = Order::paymentMethods();
        $selectedPaymentMethod = old('payment_method', Order::PAYMENT_METHOD_COD);

        return view('checkout.index', [
            'cartItems' => $cartItems,
            'groupedCartItems' => $groupedCartItems,
            'deliveryEstimates' => $deliveryEstimates,
            'overallDeliveryEstimate' => $overallDeliveryEstimate,
            'subtotal' => $totals['subtotal'],
            'shippingFee' => $totals['shippingFee'],
            'total' => $checkoutSummary['total'],
            'checkoutSummary' => $checkoutSummary,
            'voucherCode' => $voucherCode,
            'availableSellerVouchers' => $availableSellerVouchers,
            'defaultAddress' => $defaultAddress,
            'hasSavedAddress' => true,
            'selectedCartItemIds' => $selectedCartItemIds,
            'paymentMethods' => $paymentMethods,
            'selectedPaymentMethod' => $selectedPaymentMethod,
            'selectedPayment' => $paymentMethods[$selectedPaymentMethod] ?? reset($paymentMethods),
        ]);
    }

    public function store(CheckoutStoreRequest $request, SellerNotificationService $sellerNotifications, CheckoutSummaryService $checkoutSummaryService, VoucherService $voucherService)
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

        $allTotals = $checkoutSummaryService->totals($cartItems);
        $voucher = $voucherService->evaluateForCart($validated['voucher_code'] ?? null, Auth::user(), $cartItems, $checkoutSummaryService);
        $checkoutGroup = (string) Str::uuid();
        $createdOrders = collect();
        $stockChecks = collect();
        $totalVoucherDiscount = 0.0;

        try {
            DB::transaction(function () use ($cartItems, $checkoutGroup, $validated, $checkoutSummaryService, $voucherService, &$voucher, &$createdOrders, &$stockChecks, &$totalVoucherDiscount) {
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

                $cartItems->each(function (Cart $item) use ($lockedProducts, $lockedVariants): void {
                    if ($product = $lockedProducts->get($item->product_id)) {
                        $item->setRelation('product', $product);
                    }

                    if ($item->product_variant_id && $variant = $lockedVariants->get($item->product_variant_id)) {
                        $item->setRelation('variant', $variant);
                    }
                });

                $groupedCartItems = $this->groupedCartItemsBySeller($cartItems);
                $lockedAllTotals = $checkoutSummaryService->totals($cartItems);
                $voucher = $voucherService->evaluateForCart($validated['voucher_code'] ?? null, Auth::user(), $cartItems, $checkoutSummaryService, true);
                $remainingVoucherDiscount = (float) $voucher['discount'];
                $lastSellerId = $groupedCartItems->keys()->last();

                foreach ($groupedCartItems as $sellerId => $sellerCartItems) {
                    $totals = $checkoutSummaryService->totals($sellerCartItems);
                    $sellerVoucherDiscount = 0;

                    if ($voucher['discount'] > 0 && $lockedAllTotals['subtotal'] > 0) {
                        $lockedVoucherDiscount = min($voucher['discount'], $lockedAllTotals['subtotal']);

                        if ($voucher['seller_id'] !== null) {
                            $sellerVoucherDiscount = (int) $sellerId === (int) $voucher['seller_id']
                                ? round($lockedVoucherDiscount, 2)
                                : 0;
                        } else {
                            $sellerVoucherDiscount = round($lockedVoucherDiscount * ($totals['subtotal'] / $lockedAllTotals['subtotal']), 2);

                            if ((int) $sellerId === (int) $lastSellerId) {
                                $sellerVoucherDiscount = round($remainingVoucherDiscount, 2);
                            }
                        }

                        $remainingVoucherDiscount = round($remainingVoucherDiscount - $sellerVoucherDiscount, 2);
                        $totalVoucherDiscount = round($totalVoucherDiscount + $sellerVoucherDiscount, 2);
                    }

                    $order = Order::create([
                        'user_id' => Auth::id(),
                        'seller_id' => (int) $sellerId,
                        'checkout_group' => $checkoutGroup,
                        'shipping_fee' => $totals['shippingFee'],
                        'voucher_code' => $voucher['code'],
                        'voucher_discount' => $sellerVoucherDiscount,
                        'total_price' => max(0, $totals['total'] - $sellerVoucherDiscount),
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
                        $basePrice = (float) ($variant?->price ?? $product->price);
                        $unitPrice = $product->discountedPrice($basePrice);

                        $order->items()->create([
                            'product_id' => $product->id,
                            'product_variant_id' => $variant?->id,
                            'variant_name' => $variant?->displayName(),
                            'variant_options' => $variant?->option_values,
                            'quantity' => $item->quantity,
                            'price' => $unitPrice,
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

                if ($createdOrders->isNotEmpty()) {
                    $redemptionOrder = $createdOrders
                        ->first(fn (Order $order) => (float) $order->voucher_discount > 0)
                        ?? $createdOrders->sortBy('id')->first();

                    $voucherService->redeem($voucher, Auth::user(), $redemptionOrder, $totalVoucherDiscount);
                }
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
