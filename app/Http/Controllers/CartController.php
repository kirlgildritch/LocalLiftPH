<?php

namespace App\Http\Controllers;

use App\Http\Requests\Seller\UpdateCartQuantityRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected function miniCartPayload(): array
    {
        $previewItems = Cart::with(['product.user', 'variant'])
            ->where('user_id', Auth::id())
            ->latest()
            ->take(4)
            ->get();

        $miniCartCount = Cart::where('user_id', Auth::id())->count();
        $cartCount = $miniCartCount;
        $extraCount = max($miniCartCount - $previewItems->count(), 0);

        return [
            'cart_count' => (int) $cartCount,
            'mini_cart_count' => $miniCartCount,
            'extra_count' => $extraCount,
            'preview_items' => $previewItems->map(function ($item) {
                $variant = $item->variant;
                $basePrice = (float) ($variant?->price ?? $item->product->price ?? 0);
                $price = $item->product?->discountedPrice($basePrice) ?? $basePrice;

                return [
                    'id' => $item->id,
                    'name' => $item->product->name ?? 'Product',
                    'variant_name' => $variant?->displayName(),
                    'seller_name' => $item->product->user->name ?? 'LocalLift Seller',
                    'price' => number_format($price, 2),
                    'image_url' => !empty($variant?->image)
                        ? asset('storage/' . $variant->image)
                        : (!empty($item->product?->image)
                        ? asset('storage/' . $item->product->image)
                        : asset('assets/images/default-product.png')),
                ];
            })->values(),
        ];
    }

    public function index()
    {
        $cartItems = Cart::with(['product.user.sellerProfile', 'variant'])
            ->where('user_id', Auth::id())
            ->get();

        $hasSavedAddress = Auth::user()?->addresses()->exists() ?? false;

        return view('cart.index', compact('cartItems', 'hasSavedAddress'));
    }

    public function store(Request $request, $productId)
    {
        $product = Product::with('activeVariants')->findOrFail($productId);
        $requestedQuantity = max(1, (int) $request->input('quantity', 1));
        $buyNow = $request->boolean('buy_now');
        $variant = null;

        if ((int) $product->user_id === (int) Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You cannot add your own product to the cart.',
                ], 422);
            }

            return redirect()
                ->back()
                ->with('error', 'You cannot add your own product to the cart.');
        }

        if ($product->activeVariants->isNotEmpty()) {
            $variantId = (int) $request->input('product_variant_id', 0);

            $variant = ProductVariant::query()
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->find($variantId);

            if (! $variant) {
                $message = 'Please choose a product variant before adding this item to your cart.';

                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 422);
                }

                return redirect()
                    ->back()
                    ->with('error', $message);
            }
        }

        $availableStock = max(0, (int) ($variant?->stock ?? $product->stock));

        if ($availableStock <= 0) {
            $message = 'This product is out of stock.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return redirect()
                ->back()
                ->with('error', $message);
        }

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->when($variant, fn ($query) => $query->where('product_variant_id', $variant->id), fn ($query) => $query->whereNull('product_variant_id'))
            ->first();

        if ($cartItem) {
            $nextQuantity = (int) $cartItem->quantity + $requestedQuantity;

            if ($nextQuantity > $availableStock) {
                $message = 'Max stock reached.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $message,
                        'max_stock' => $availableStock,
                    ], 422);
                }

                return redirect()
                    ->back()
                    ->with('error', $message);
            }

            $cartItem->increment('quantity', $requestedQuantity);
            $cartItem->refresh();
        } else {
            $quantity = min($requestedQuantity, $availableStock);

            $cartItem = Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
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

    public function update(UpdateCartQuantityRequest $request, $id)
    {
        $cartItem = Cart::with(['product', 'variant'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $product = $cartItem->product;
        $variant = $cartItem->variant;
        $availableStock = max(0, (int) ($variant?->stock ?? $product?->stock ?? 0));
        $requestedQuantity = (int) $request->validated('quantity');

        if ($availableStock <= 0) {
            $message = 'This product is out of stock.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'max_stock' => 0,
                ], 422);
            }

            return redirect()
                ->route('cart.index')
                ->with('error', $message);
        }

        if ($requestedQuantity > $availableStock) {
            $message = 'Max stock reached.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'max_stock' => $availableStock,
                ], 422);
            }

            return redirect()
                ->route('cart.index')
                ->with('error', $message);
        }

        $cartItem->update([
            'quantity' => $requestedQuantity,
        ]);

        if ($request->expectsJson()) {
            $basePrice = (float) ($cartItem->variant?->price ?? $cartItem->product->price ?? 0);
            $price = $cartItem->product?->discountedPrice($basePrice) ?? $basePrice;
            $shippingFee = (float) ($cartItem->product->shipping_fee ?? 0);
            $quantity = (int) $cartItem->quantity;
            $subtotal = $price * $quantity;
            $shipping = $shippingFee * $quantity;

            return response()->json([
                'message' => 'Cart updated successfully.',
                'cart_item' => [
                    'id' => $cartItem->id,
                    'quantity' => $quantity,
                    'max_stock' => $availableStock,
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'subtotal_formatted' => number_format($subtotal, 2),
                ],
            ]);
        }

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
