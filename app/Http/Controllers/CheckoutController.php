<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Notifications\SellerNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
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
                || (int) $product->stock < (int) $item->quantity;
        });
    }

    protected function calculateCartTotals($cartItems): array
    {
        $subtotal = 0;
        $shippingFee = 0;

        foreach ($cartItems as $item) {
            $price = (float) ($item->product->price ?? 0);
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

    public function index(Request $request)
    {
        $validated = $request->validate([
            'selected_cart_items' => ['nullable', 'array'],
            'selected_cart_items.*' => ['integer'],
        ]);

        $selectedCartItemIds = collect($validated['selected_cart_items'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $cartQuery = Cart::with(['product.user.sellerProfile'])
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

        $totals = $this->calculateCartTotals($cartItems);
        $groupedCartItems = $this->groupedCartItemsBySeller($cartItems);

        $defaultAddress = Auth::user()->addresses()
            ->where('is_default', 1)
            ->first();

        $selectedCartItemIds = $cartItems->pluck('id')->values();

        return view('checkout.index', [
            'cartItems' => $cartItems,
            'groupedCartItems' => $groupedCartItems,
            'subtotal' => $totals['subtotal'],
            'shippingFee' => $totals['shippingFee'],
            'total' => $totals['total'],
            'defaultAddress' => $defaultAddress,
            'selectedCartItemIds' => $selectedCartItemIds,
        ]);
    }

    public function store(Request $request, SellerNotificationService $sellerNotifications)
    {
        $validated = $request->validate([
            'selected_cart_items' => ['nullable', 'array'],
            'selected_cart_items.*' => ['integer'],
        ]);

        $selectedCartItemIds = collect($validated['selected_cart_items'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $cartItems = Cart::with('product.user')
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

        $groupedCartItems = $this->groupedCartItemsBySeller($cartItems);
        $checkoutGroup = (string) Str::uuid();
        $createdOrders = collect();
        $stockChecks = collect();

        try {
            DB::transaction(function () use ($cartItems, $groupedCartItems, $checkoutGroup, &$createdOrders, &$stockChecks) {
                $lockedProducts = Product::query()
                    ->with('user.sellerProfile')
                    ->whereIn('id', $cartItems->pluck('product_id')->filter()->unique()->values())
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
                        'payment_method' => Order::PAYMENT_METHOD_COD,
                        'payment_status' => Order::PAYMENT_PENDING,
                        'seller_earning_status' => Order::EARNING_PENDING,
                    ]);

                    foreach ($sellerCartItems as $item) {
                        $product = $lockedProducts->get($item->product_id);

                        if (
                            ! $product
                            || $product->status !== Product::STATUS_APPROVED
                            || ! $product->is_active
                            || $product->user?->sellerProfile?->application_status !== Seller::STATUS_APPROVED
                            || (int) $product->stock < (int) $item->quantity
                        ) {
                            throw new \RuntimeException('One or more selected products are no longer available in the requested quantity.');
                        }

                        $previousStock = (int) $product->stock;

                        $order->items()->create([
                            'product_id' => $product->id,
                            'quantity' => $item->quantity,
                            'price' => $product->price,
                            'shipping_fee' => $product->shipping_fee ?? 0,
                        ]);

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

        $createdOrders->each(function (Order $order) use ($sellerNotifications): void {
            $sellerNotifications->newOrder($order->fresh(['seller.sellerProfile', 'user', 'items']));
        });

        $stockChecks
            ->unique('product_id')
            ->each(function (array $stockCheck) use ($sellerNotifications): void {
                $product = Product::with('user.sellerProfile')->find($stockCheck['product_id']);

                if ($product) {
                    $sellerNotifications->checkProductStock($product, (int) $stockCheck['previous_stock']);
                }
            });

        $primaryOrder = $createdOrders->sortBy('id')->first();

        return redirect()
            ->route('buyer.orders.show', $primaryOrder)
            ->with('success', 'Order placed successfully!');
    }
}
