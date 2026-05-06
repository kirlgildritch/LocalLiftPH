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
            'review_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'review_video' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm,mkv,3gp,m4v'],
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

        $imagePath = $request->hasFile('review_image')
            ? $request->file('review_image')->store('reviews/images', 'public')
            : null;
        $videoPath = $request->hasFile('review_video')
            ? $request->file('review_video')->store('reviews/videos', 'public')
            : null;

        Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'order_item_id' => $orderItem->id,
            'rating' => $validated['rating'],
            'comment' => trim((string) ($validated['comment'] ?? '')) ?: null,
            'image_path' => $imagePath,
            'video_path' => $videoPath,
        ]);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Review submitted successfully.');
    }
}
