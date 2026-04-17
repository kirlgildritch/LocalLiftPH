<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
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

        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }

        $defaultAddress = Auth::user()->addresses()
            ->where('is_default', 1)
            ->first();

        $selectedCartItemIds = $cartItems->pluck('id')->values();

        return view('checkout.index', compact('cartItems', 'total', 'defaultAddress', 'selectedCartItemIds'));
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

        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }

        $order = \App\Models\Order::create([
            'user_id' => Auth::id(),
            'total_price' => $total,
            'status' => 'pending',
        ]);

        foreach ($cartItems as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product->id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        Cart::where('user_id', Auth::id())
            ->whereIn('id', $cartItems->pluck('id'))
            ->delete();

        return redirect()->route('buyer.orders')->with('success', 'Order placed successfully!');
    }
}
