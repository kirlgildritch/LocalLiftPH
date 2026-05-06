<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $this->normalizeFileInput($request, 'review_media');
        $this->normalizeFileInput($request, 'review_image');
        $this->normalizeFileInput($request, 'review_video');

        $validated = $request->validate([
            'order_item_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1500'],
            'review_media' => ['nullable', 'array', 'max:5'],
            'review_media.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mkv,3gp,m4v', 'max:51200'],
            'review_image' => ['nullable', 'array', 'max:5'],
            'review_image.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mkv,3gp,m4v', 'max:51200'],
            'review_video' => ['nullable', 'array', 'max:5'],
            'review_video.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mkv,3gp,m4v', 'max:51200'],
        ]);

        $uploadedFiles = [
            ...$this->uploadedFiles($request, 'review_media'),
            ...$this->uploadedFiles($request, 'review_image'),
            ...$this->uploadedFiles($request, 'review_video'),
        ];

        if (count($uploadedFiles) > 5) {
            return back()
                ->withErrors(['review_image' => 'You may upload up to 5 review media files.'])
                ->withInput();
        }

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

    private function normalizeFileInput(Request $request, string $key): void
    {
        if (! $request->hasFile($key)) {
            return;
        }

        $files = $request->file($key);

        if ($files instanceof UploadedFile) {
            $request->files->set($key, [$files]);
        }
    }
}
