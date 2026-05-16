<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductReview\StoreProductReviewRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Support\ReviewUploadLimit;
use Illuminate\Http\UploadedFile;
use App\Notifications\SellerNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(StoreProductReviewRequest $request, Product $product, SellerNotificationService $sellerNotifications): RedirectResponse|JsonResponse
    {
        $maxFiles = ReviewUploadLimit::maxFiles();
        $validated = $request->validated();

        $uploadedFiles = [
            ...$this->uploadedFiles($request, 'review_media'),
            ...$this->uploadedFiles($request, 'review_image'),
            ...$this->uploadedFiles($request, 'review_video'),
        ];

        if (count($uploadedFiles) > $maxFiles) {
            return back()
                ->withErrors(['review_image' => "You may upload up to {$maxFiles} review media files."])
                ->withInput();
        }

        $orderItem = OrderItem::with('order')
            ->whereKey($validated['order_item_id'])
            ->tap(fn ($query) => $this->applyReviewableOrderItemConstraints($query, $product))
            ->first();

        if (! $orderItem) {
            $message = 'You can only review products from your completed purchases, once per order item.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return redirect()
                ->route('products.show', $product)
                ->with('error', $message);
        }

        $storedMedia = [];

        foreach ($uploadedFiles as $file) {
            $type = str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image';
            $storedMedia[] = [
                'type' => $type,
                'path' => $file->store($type === 'video' ? 'reviews/videos' : 'reviews/images', 'public'),
            ];
        }

        $imagePath = collect($storedMedia)->firstWhere('type', 'image')['path'] ?? null;
        $videoPath = collect($storedMedia)->firstWhere('type', 'video')['path'] ?? null;
        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'order_item_id' => $orderItem->id,
            'rating' => $validated['rating'],
            'comment' => trim((string) ($validated['comment'] ?? '')) ?: null,
            'image_path' => $imagePath,
            'video_path' => $videoPath,
        ]);

        foreach ($storedMedia as $index => $media) {
            $review->media()->create([
                'type' => $media['type'],
                'path' => $media['path'],
                'sort_order' => $index,
            ]);
        }

        $freshReview = $review->fresh(['product.user.sellerProfile', 'user', 'media', 'orderItem.variant', 'orderItem.product:id,name']);

        $sellerNotifications->buyerLeftReview($freshReview);

        if ($request->expectsJson()) {
            $productSummary = Product::query()
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->findOrFail($product->getKey());

            $nextReviewableOrderItem = OrderItem::with('order')
                ->tap(fn ($query) => $this->applyReviewableOrderItemConstraints($query, $product))
                ->latest()
                ->first();

            return response()->json([
                'message' => 'Review submitted successfully.',
                'review_html' => view('products.partials.review-card', [
                    'review' => $freshReview,
                ])->render(),
                'reviews_count' => (int) $productSummary->reviews_count,
                'average_rating' => round((float) ($productSummary->reviews_avg_rating ?? 0), 1),
                'remaining_reviewable_count' => OrderItem::query()
                    ->tap(fn ($query) => $this->applyReviewableOrderItemConstraints($query, $product))
                    ->count(),
                'next_order_item_id' => $nextReviewableOrderItem?->id,
            ], 201);
        }

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Review submitted successfully.');
    }

    /**
     * @return array<int, UploadedFile>
     */
    private function uploadedFiles(Request $request, string $key): array
    {
        if (! $request->hasFile($key)) {
            return [];
        }

        $files = $request->file($key);

        return array_values(array_filter(
            is_array($files) ? $files : [$files],
            fn ($file) => $file instanceof UploadedFile
        ));
    }
    private function applyReviewableOrderItemConstraints($query, Product $product): void
    {
        $query->where('product_id', $product->id)
            ->whereDoesntHave('review')
            ->whereHas('order', function ($orderQuery) {
                $orderQuery->where('user_id', Auth::id())
                    ->where('shipping_status', Order::SHIPPING_COMPLETED);
            });
    }
}
