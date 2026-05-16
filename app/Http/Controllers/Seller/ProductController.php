<?php

namespace App\Http\Controllers\Seller;

use App\Http\Requests\Seller\DestroyProductMediaRequest;
use App\Http\Requests\Seller\ReplyToProductReviewRequest;
use App\Http\Requests\Seller\StoreProductRequest;
use App\Http\Requests\Seller\UpdateProductRequest;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Notifications\SellerNotificationService;
use App\Services\AdminActivityService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private function ownedProductQuery()
    {
        return Product::query()->where('user_id', Auth::guard('seller')->id());
    }

    private function ownedProductOrFail(int|string $id, array $relations = []): Product
    {
        return $this->ownedProductQuery()
            ->with($relations)
            ->findOrFail($id);
    }

    private function ensureOwnedProduct(Product $product): void
    {
        abort_unless((int) $product->user_id === (int) Auth::guard('seller')->id(), 404);
    }

    private function calculateShippingFee(float $weight, float $widthCm, float $lengthCm, float $heightCm): float
    {
        $volumetricWeight = ($widthCm * $lengthCm * $heightCm) / 5000;
        $billableWeight = max($weight, $volumetricWeight);

        return round(60 + ($billableWeight * 35), 2);
    }

    private function productMediaFiles(Request $request): array
    {
        $files = [];

        if ($request->hasFile('image')) {
            $legacyImage = $request->file('image');

            if ($legacyImage instanceof UploadedFile) {
                $files[] = $legacyImage;
            }
        }

        if ($request->hasFile('media')) {
            $mediaFiles = $request->file('media');

            if ($mediaFiles instanceof UploadedFile) {
                $mediaFiles = [$mediaFiles];
            }

            foreach ((array) $mediaFiles as $file) {
                if ($file instanceof UploadedFile) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    private function storeProductMedia(Product $product, array $files): ?string
    {
        $coverImagePath = null;
        $startingOrder = (int) ($product->media()->max('sort_order') ?? -1) + 1;

        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $mimeType = (string) $file->getMimeType();
            $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
            $path = $file->store($type === 'video' ? 'products/videos' : 'products/images', 'public');

            $product->media()->create([
                'type' => $type,
                'path' => $path,
                'sort_order' => $startingOrder + $index,
            ]);

            if ($coverImagePath === null && $type === 'image') {
                $coverImagePath = $path;
            }
        }

        return $coverImagePath;
    }

    private function deleteProductFiles(Product $product): void
    {
        $paths = collect([$product->image])
            ->filter()
            ->merge($product->media->pluck('path'))
            ->merge($product->variants->pluck('image')->filter())
            ->unique()
            ->values();

        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function removeMarkedProductMedia(Product $product, array $paths): bool
    {
        $paths = collect($paths)
            ->map(fn ($path) => trim((string) $path))
            ->filter()
            ->unique()
            ->values();

        if ($paths->isEmpty()) {
            return false;
        }

        $product->loadMissing(['media', 'variants']);

        $product->media()
            ->whereIn('path', $paths->all())
            ->delete();

        if (filled($product->image) && $paths->contains($product->image)) {
            $product->image = $product->media()
                ->where('type', 'image')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->value('path');
            $product->save();
        }

        $product->refresh()->loadMissing(['media', 'variants']);

        foreach ($paths as $path) {
            $isStillUsed = $product->image === $path
                || $product->media->contains(fn ($media) => $media->path === $path)
                || $product->variants->contains(fn ($variant) => $variant->image === $path);

            if (! $isStillUsed) {
                Storage::disk('public')->delete($path);
            }
        }

        return true;
    }

    private function normalizedVariantRows(Request $request): array
    {
        if (! $request->boolean('has_variants')) {
            return [];
        }

        $rows = collect($request->input('variants', []))
            ->map(function (array $row, int $index): array {
                return [
                    'index' => $index,
                    'id' => filled($row['id'] ?? null) ? (int) $row['id'] : null,
                    'name' => trim((string) ($row['name'] ?? '')),
                    'sku' => filled($row['sku'] ?? null) ? trim((string) $row['sku']) : null,
                    'price' => $row['price'] ?? null,
                    'stock' => $row['stock'] ?? null,
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ];
            })
            ->filter(fn (array $row): bool => $row['name'] !== '' || filled($row['price']) || filled($row['stock']) || filled($row['sku']))
            ->values();

        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'variants' => 'Add at least one product variant, or turn variants off.',
            ]);
        }

        $errors = [];

        foreach ($rows as $row) {
            $prefix = 'variants.' . $row['index'];

            if ($row['name'] === '') {
                $errors[$prefix . '.name'] = 'Variant name is required.';
            }

            if (! filled($row['price'])) {
                $errors[$prefix . '.price'] = 'Variant price is required.';
            }

            if (! filled($row['stock'])) {
                $errors[$prefix . '.stock'] = 'Variant stock is required.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $rows->all();
    }

    private function syncProductVariants(Product $product, Request $request): bool
    {
        $rows = $this->normalizedVariantRows($request);

        if ($rows === []) {
            $product->variants()->update(['is_active' => false]);
            return false;
        }

        $keptVariantIds = [];
        $existingVariantIds = $product->variants()->pluck('id')->map(fn ($id) => (int) $id)->all();

        foreach ($rows as $row) {
            $imagePath = null;
            $variantImage = $request->file('variants.' . $row['index'] . '.image');

            if ($variantImage instanceof UploadedFile) {
                $imagePath = $variantImage->store('products/variants', 'public');
            }

            $payload = [
                'name' => $row['name'],
                'option_values' => ['Option' => $row['name']],
                'sku' => $row['sku'],
                'price' => (float) $row['price'],
                'stock' => (int) $row['stock'],
                'is_active' => $row['is_active'],
            ];

            if ($imagePath !== null) {
                $payload['image'] = $imagePath;
            }

            if ($row['id'] && in_array($row['id'], $existingVariantIds, true)) {
                $variant = $product->variants()->whereKey($row['id'])->first();

                if ($variant) {
                    if ($imagePath !== null && filled($variant->image)) {
                        Storage::disk('public')->delete($variant->image);
                    }

                    $variant->update($payload);
                    $keptVariantIds[] = (int) $variant->id;
                }
            } else {
                $variant = $product->variants()->create($payload);
                $keptVariantIds[] = (int) $variant->id;
            }
        }

        $product->variants()
            ->whereNotIn('id', $keptVariantIds)
            ->update(['is_active' => false]);

        $activeVariants = $product->variants()->where('is_active', true)->get();

        if ($activeVariants->isNotEmpty()) {
            $product->update([
                'price' => $activeVariants->min('price'),
                'stock' => $activeVariants->sum('stock'),
            ]);
        }

        return true;
    }

    public function index()
    {
        $currentTab = request('status', 'live');
        $allowedTabs = ['live', 'sold_out', 'reviewing', 'violation', 'delisted'];
        $sellerSettings = (Auth::guard('seller')->user() ?? Auth::user())?->sellerProfile;

        if (!in_array($currentTab, $allowedTabs, true)) {
            $currentTab = 'live';
        }

        $baseQuery = Product::where('user_id', Auth::guard('seller')->id());

        $statusCounts = [
            'live' => (clone $baseQuery)
                ->where('status', 'approved')
                ->where('is_active', 1)
                ->where('stock', '>', 0)
                ->count(),
            'sold_out' => (clone $baseQuery)
                ->where('status', 'approved')
                ->where('is_active', 1)
                ->where('stock', '<=', 0)
                ->count(),
            'reviewing' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),
            'violation' => (clone $baseQuery)
                ->where('status', 'rejected')
                ->count(),
            'delisted' => (clone $baseQuery)
                ->where('is_active', 0)
                ->whereNotIn('status', ['pending', 'rejected'])
                ->count(),
        ];

        $productsQuery = Product::with([
            'category',
            'reviews' => function ($query) {
                $query->with('user')->latest();
            },
        ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('user_id', Auth::guard('seller')->id());

        switch ($currentTab) {
            case 'sold_out':
                $productsQuery
                    ->where('status', 'approved')
                    ->where('is_active', 1)
                    ->where('stock', '<=', 0);
                break;
            case 'reviewing':
                $productsQuery->where('status', 'pending');
                break;
            case 'violation':
                $productsQuery->where('status', 'rejected');
                break;
            case 'delisted':
                $productsQuery
                    ->where('is_active', 0)
                    ->whereNotIn('status', ['pending', 'rejected']);
                break;
            case 'live':
            default:
                $productsQuery
                    ->where('status', 'approved')
                    ->where('is_active', 1)
                    ->where('stock', '>', 0);
                break;
        }

        $products = $productsQuery->latest()->get();

        return view('seller.manage_products', compact('products', 'statusCounts', 'currentTab', 'sellerSettings'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        $categories = Category::orderBy('name')->get();

        return view('seller.add_product', compact('categories'));
    }

    public function edit($id)
    {
        $product = $this->ownedProductOrFail($id, ['media', 'variants']);
        $this->authorize('update', $product);
        $categories = Category::orderBy('name')->get();

        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function store(StoreProductRequest $request, AdminActivityService $adminActivity)
    {
        $this->authorize('create', Product::class);
        $shippingFee = $this->calculateShippingFee(
            (float) $request->weight,
            (float) $request->width_cm,
            (float) $request->length_cm,
            (float) $request->height_cm
        );

        $product = Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'condition' => $request->condition,
            'description' => $request->description,
            'weight' => $request->weight,
            'width_cm' => $request->width_cm,
            'length_cm' => $request->length_cm,
            'height_cm' => $request->height_cm,
            'shipping_fee' => $shippingFee,
            'user_id' => auth()->id(),
            'is_active' => 0, // hidden by default
            'status' => 'pending', // for admin approval
        ]);

        $mediaFiles = $this->productMediaFiles($request);
        $coverImagePath = $this->storeProductMedia($product, $mediaFiles);

        if (! empty($coverImagePath)) {
            $product->update(['image' => $coverImagePath]);
        }

        $this->syncProductVariants($product, $request);

        $adminActivity->productSubmitted($request->name, auth()->user()?->name ?? 'a seller');

        return redirect()->back()->with('success', 'Product submitted for approval.');
    }

    public function update(UpdateProductRequest $request, $id, SellerNotificationService $sellerNotifications, AdminActivityService $adminActivity)
    {
        $product = $this->ownedProductOrFail($id);
        $this->authorize('update', $product);
        $originalName = $product->name;
        $originalStock = (int) $product->stock;
        $sellerName = auth()->user()?->name ?? 'a seller';
        $changedFields = [];
        $originalValues = [
            'name' => $product->name,
            'category_id' => $product->category_id,
            'price' => (string) $product->price,
            'stock' => (string) $product->stock,
            'condition' => $product->condition,
            'description' => $product->description,
            'weight' => (string) $product->weight,
            'width_cm' => (string) $product->width_cm,
            'length_cm' => (string) $product->length_cm,
            'height_cm' => (string) $product->height_cm,
        ];

        $validated = $request->validated();

        $shippingFee = $this->calculateShippingFee(
            (float) $validated['weight'],
            (float) $validated['width_cm'],
            (float) $validated['length_cm'],
            (float) $validated['height_cm']
        );

        $legacyCoverUpload = $request->hasFile('image');
        $mediaFiles = $this->productMediaFiles($request);
        $coverImagePath = $this->storeProductMedia($product, $mediaFiles);

        if (! empty($coverImagePath) && ($legacyCoverUpload || empty($product->image))) {
            if ($legacyCoverUpload && ! empty($product->image) && $product->image !== $coverImagePath) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $coverImagePath;

            if (! in_array('image', $changedFields, true)) {
                $changedFields[] = 'image';
            }

            if (! in_array('media', $changedFields, true)) {
                $changedFields[] = 'media';
            }
        } elseif ($mediaFiles !== [] && ! in_array('media', $changedFields, true)) {
            $changedFields[] = 'media';
        }

        foreach ([
            'name' => 'name',
            'category_id' => 'category',
            'price' => 'price',
            'stock' => 'stock',
            'condition' => 'condition',
            'description' => 'description',
            'weight' => 'weight',
            'width_cm' => 'width',
            'length_cm' => 'length',
            'height_cm' => 'height',
        ] as $field => $label) {
            if ((string) ($validated[$field] ?? '') !== (string) $originalValues[$field]) {
                $changedFields[] = $label;
            }
        }

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'condition' => $validated['condition'],
            'description' => $validated['description'],
            'weight' => $validated['weight'],
            'width_cm' => $validated['width_cm'],
            'length_cm' => $validated['length_cm'],
            'height_cm' => $validated['height_cm'],
            'shipping_fee' => $shippingFee,
            'image' => $validated['image'] ?? $product->image,
        ]);

        if ($this->syncProductVariants($product, $request) && ! in_array('variants', $changedFields, true)) {
            $changedFields[] = 'variants';
        }

        $product->refresh();

        $sellerNotifications->productEdited($product, $changedFields);
        $sellerNotifications->checkProductStock($product, $originalStock);
        $updatedProductName = $validated['name'];
        if ($changedFields !== []) {
            $adminActivity->productUpdated($originalName, $updatedProductName, $sellerName, $changedFields);
        }

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroyMedia(DestroyProductMediaRequest $request, Product $product)
    {
        $this->ensureOwnedProduct($product);
        $this->authorize('manageMedia', $product);

        $validated = $request->validated();

        if (! $this->removeMarkedProductMedia($product, [$validated['path']])) {
            return response()->json([
                'message' => 'Saved media could not be removed.',
            ], 404);
        }

        return response()->json([
            'message' => 'Saved media removed.',
        ]);
    }

    public function destroy($id, AdminActivityService $adminActivity)
    {
        $product = $this->ownedProductOrFail($id);
        $this->authorize('delete', $product);
        $productName = $product->name;
        $sellerName = auth()->user()?->name ?? 'a seller';

        $hasExistingOrders = $product->orderItems()
            ->whereHas('order', function ($query) {
                $query->whereNotIn('shipping_status', [
                    \App\Models\Order::SHIPPING_COMPLETED,
                    \App\Models\Order::SHIPPING_CANCELLED,
                ]);
            })
            ->exists();

        if ($hasExistingOrders) {
            return redirect()
                ->route('seller.products.index')
                ->with('error', 'This product cannot be deleted because it is still part of an existing order.');
        }

        $product->carts()->delete();
        $product->reviews()->get()->each->delete();
        $product->reports()->delete();

        $this->deleteProductFiles($product);

        $product->delete();

        $adminActivity->productDeleted($productName, $sellerName);

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function reviews($id)
    {
        $product = $this->ownedProductQuery()
            ->with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($id);
        $this->authorize('viewSellerReviews', $product);

        $reviews = $product->reviews()
            ->with(['user', 'media'])
            ->latest()
            ->paginate(10);

        return view('seller.products.reviews', compact('product', 'reviews'));
    }

    public function replyToReview(ReplyToProductReviewRequest $request, Product $product, Review $review)
    {
        $this->ensureOwnedProduct($product);
        $this->authorize('reply', $review);
        abort_unless((int) $review->product_id === (int) $product->id, 404);

        $validated = $request->validated();

        $review->update([
            'seller_reply' => trim($validated['seller_reply']),
            'seller_replied_at' => now(),
        ]);

        return redirect()
            ->route('seller.products.reviews', $product)
            ->with('success', 'Reply posted under the buyer review.');
    }
}
