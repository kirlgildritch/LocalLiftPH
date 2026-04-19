<?php

namespace App\Http\Controllers;

use App\Models\Cart;
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
        $selectedCartItemIds = collect($request->input('selected_cart_items', []))
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
        $selectedCartItemIds = collect($request->input('selected_cart_items', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $cartItems = Cart::with('product')
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

        $totals = $this->calculateCartTotals($cartItems);

        $order = \App\Models\Order::create([
            'user_id' => Auth::id(),
            'shipping_fee' => $totals['shippingFee'],
            'total_price' => $totals['total'],
            'status' => 'pending',
        ]);

        foreach ($cartItems as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product->id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
                'shipping_fee' => $item->product->shipping_fee ?? 0,
            ]);
        }

        Cart::where('user_id', Auth::id())
            ->whereIn('id', $cartItems->pluck('id'))
            ->delete();

        return redirect()->route('buyer.orders')->with('success', 'Order placed successfully!');
    }
}
