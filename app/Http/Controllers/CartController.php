<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected function miniCartPayload(): array
    {
        $previewItems = Cart::with(['product.user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->take(4)
            ->get();

        $miniCartCount = Cart::where('user_id', Auth::id())->count();
        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
        $extraCount = max($miniCartCount - $previewItems->count(), 0);

        return [
            'cart_count' => (int) $cartCount,
            'mini_cart_count' => $miniCartCount,
            'extra_count' => $extraCount,
            'preview_items' => $previewItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->product->name ?? 'Product',
                    'seller_name' => $item->product->user->name ?? 'LocalLift Seller',
                    'price' => number_format($item->product->price ?? 0, 2),
                    'image_url' => !empty($item->product?->image)
                        ? asset('storage/' . $item->product->image)
                        : asset('assets/images/default-product.png'),
                ];
            })->values(),
        ];
    }

    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return view('cart.index', compact('cartItems'));
    }

    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $quantity = max(1, (int) $request->input('quantity', 1));
        $buyNow = $request->boolean('buy_now');

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            $cartItem->refresh();
        } else {
            $cartItem = Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        if (request()->expectsJson()) {
            return response()->json(array_merge([
                'message' => 'Product added to cart successfully.',
                'cart_item_id' => $cartItem->id,
            ], $this->miniCartPayload()));
        }

        if ($buyNow) {
            return redirect()
                ->route('cart.index')
                ->with('selected_cart_item_id', $cartItem->id)
                ->with('success', 'Product added to cart. Review it below before checkout.');
        }

        return redirect()->back()->with('success', 'Product added to cart successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }

    public function destroy($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }
}
