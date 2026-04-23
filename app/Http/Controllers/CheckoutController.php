<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $cartQuery = Cart::with(['product.user'])
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

        $defaultAddress = Auth::user()->addresses()
            ->where('is_default', 1)
            ->first();

        $selectedCartItemIds = $cartItems->pluck('id')->values();

        return view('checkout.index', [
            'cartItems' => $cartItems,
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

        $totals = $this->calculateCartTotals($cartItems);

        $order = null;

        DB::transaction(function () use ($cartItems, $totals, &$order) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_fee' => $totals['shippingFee'],
                'total_price' => $totals['total'],
                'status' => Order::STATUS_PENDING,
                'shipping_status' => Order::SHIPPING_PENDING,
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'shipping_fee' => $item->product->shipping_fee ?? 0,
                ]);
            }

            Cart::where('user_id', Auth::id())
                ->whereIn('id', $cartItems->pluck('id'))
                ->delete();
        });

        return redirect()->route('buyer.orders')->with('success', 'Order placed successfully!');
    }
}
