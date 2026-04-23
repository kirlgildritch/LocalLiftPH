<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'order_item_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1500'],
        ]);

        $orderItem = OrderItem::with('order')
            ->where('id', $validated['order_item_id'])
            ->where('product_id', $product->id)
            ->whereDoesntHave('review')
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id())
                    ->where('shipping_status', Order::SHIPPING_DELIVERED);
            })
            ->first();

        if (! $orderItem) {
            return redirect()
                ->route('products.show', $product)
                ->with('error', 'You can only review products from your delivered purchases, once per order item.');
        }

        Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'order_item_id' => $orderItem->id,
            'rating' => $validated['rating'],
            'comment' => trim((string) ($validated['comment'] ?? '')) ?: null,
        ]);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Review submitted successfully.');
    }
}
