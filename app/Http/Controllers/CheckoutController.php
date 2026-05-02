<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
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

            return ! $product
                || $product->status !== \App\Models\Product::STATUS_APPROVED
                || ! $product->is_active
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
            ->filter(fn ($item) => $item->product && $item->product->user_id)
            ->groupBy(fn ($item) => (int) $item->product->user_id)
            ->sortKeys();
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'selected_cart_items' => ['nullable', 'array'],
            'selected_cart_items.*' => ['integer'],
        ]);

        $selectedCartItemIds = collect($validated['selected_cart_items'] ?? [])
            ->map(fn ($id) => (int) $id)
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'selected_cart_items' => ['nullable', 'array'],
            'selected_cart_items.*' => ['integer'],
        ]);

        $selectedCartItemIds = collect($validated['selected_cart_items'] ?? [])
            ->map(fn ($id) => (int) $id)
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

        DB::transaction(function () use ($cartItems, $groupedCartItems, $checkoutGroup, &$createdOrders) {
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
                ]);

                foreach ($sellerCartItems as $item) {
                    $order->items()->create([
                        'product_id' => $item->product->id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'shipping_fee' => $item->product->shipping_fee ?? 0,
                    ]);
                }

                $createdOrders->push($order);
            }

            Cart::where('user_id', Auth::id())
                ->whereIn('id', $cartItems->pluck('id'))
                ->delete();
        });

        $primaryOrder = $createdOrders->sortBy('id')->first();

        return redirect()
            ->route('buyer.orders.show', $primaryOrder)
            ->with('success', 'Order placed successfully!');
    }
}
