<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkUpdateProductsRequest;
use App\Http\Requests\Admin\RejectProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Report;
use App\Models\Seller;
use App\Models\User;
use App\Notifications\SellerNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductApprovalController extends Controller
{
    protected function rejectionReasons(): array
    {
        return [
            'invalid_image' => 'Invalid image',
            'wrong_category' => 'Wrong category',
            'prohibited_item' => 'Prohibited item',
            'incomplete_details' => 'Incomplete details',
        ];
    }

    protected function availableTabs(): array
    {
        return ['pending', 'approved', 'rejected', 'reported', 'delisted'];
    }

    protected function applyTabScope(Builder $query, string $tab): Builder
    {
        return match ($tab) {
            'approved' => $query->where('status', Product::STATUS_APPROVED)->where('is_active', 1),
            'rejected' => $query->where('status', Product::STATUS_REJECTED),
            'reported' => $query->whereHas('reports', fn (Builder $reportQuery) => $reportQuery->where('status', Report::STATUS_PENDING)),
            'delisted' => $query->where('status', Product::STATUS_APPROVED)->where('is_active', 0),
            default => $query->where('status', Product::STATUS_PENDING),
        };
    }

    protected function resolveRejectionReason(?string $key, string $custom = ''): ?string
    {
        $reason = $this->rejectionReasons()[$key ?? ''] ?? null;

        if (! $reason && $custom === '') {
            return null;
        }

        if ($reason && $custom !== '') {
            return $reason . ': ' . $custom;
        }

        return $reason ?: $custom;
    }

    public function index(Request $request): View
    {
        $this->authorize('moderate', Product::class);
        $currentTab = (string) $request->string('status', 'pending');
        if (! in_array($currentTab, $this->availableTabs(), true)) {
            $currentTab = 'pending';
        }

        $baseQuery = Product::with([
            'user.sellerProfile',
            'category',
            'media',
            'user.products' => fn ($query) => $query->with('category')->latest(),
        ])
            ->withCount([
                'reports as pending_reports_count' => fn (Builder $query) => $query->where('status', Report::STATUS_PENDING),
            ]);

        $productsQuery = $this->applyTabScope(clone $baseQuery, $currentTab)
            ->when($request->filled('category_id'), function (Builder $query) use ($request) {
                $query->where('category_id', (int) $request->input('category_id'));
            })
            ->when($request->filled('seller_id'), function (Builder $query) use ($request) {
                $query->where('user_id', (int) $request->input('seller_id'));
            })
            ->when($request->filled('price_min'), function (Builder $query) use ($request) {
                $query->where('price', '>=', (float) $request->input('price_min'));
            })
            ->when($request->filled('price_max'), function (Builder $query) use ($request) {
                $query->where('price', '<=', (float) $request->input('price_max'));
            });

        $sort = (string) $request->string('sort', 'newest');
        if ($sort === 'oldest') {
            $productsQuery->oldest();
        } else {
            $sort = 'newest';
            $productsQuery->latest();
        }

        $products = $productsQuery->get();

        $countQuery = Product::query();
        $statusCounts = [
            'pending' => (clone $countQuery)->where('status', Product::STATUS_PENDING)->count(),
            'approved' => (clone $countQuery)->where('status', Product::STATUS_APPROVED)->where('is_active', 1)->count(),
            'rejected' => (clone $countQuery)->where('status', Product::STATUS_REJECTED)->count(),
            'reported' => (clone $countQuery)->whereHas('reports', fn (Builder $query) => $query->where('status', Report::STATUS_PENDING))->count(),
            'delisted' => (clone $countQuery)->where('status', Product::STATUS_APPROVED)->where('is_active', 0)->count(),
        ];

        $categories = Category::orderBy('name')->get();
        $sellers = User::with('sellerProfile')
            ->whereHas('products')
            ->orderBy('name')
            ->get();

        return view('admin.products', [
            'products' => $products,
            'currentTab' => $currentTab,
            'statusCounts' => $statusCounts,
            'categories' => $categories,
            'sellers' => $sellers,
            'filters' => [
                'category_id' => $request->input('category_id'),
                'seller_id' => $request->input('seller_id'),
                'price_min' => $request->input('price_min'),
                'price_max' => $request->input('price_max'),
                'sort' => $sort,
            ],
            'rejectionReasons' => $this->rejectionReasons(),
        ]);
    }

    public function approve(Product $product, SellerNotificationService $sellerNotifications): RedirectResponse
    {
        $this->authorize('approve', $product);
        $product->update([
            'status' => Product::STATUS_APPROVED,
            'is_active' => 1,
            'rejection_reason' => null,
        ]);

        $product->loadMissing('user.sellerProfile');
        $sellerNotifications->productApproved($product);

        return back()->with('success', $product->name . ' approved successfully.');
    }

    public function reject(RejectProductRequest $request, Product $product, SellerNotificationService $sellerNotifications): RedirectResponse
    {
        $this->authorize('reject', $product);
        $validated = $request->validated();

        $rejectionReason = $this->resolveRejectionReason(
            (string) ($validated['rejection_reason_key'] ?? ''),
            trim((string) ($validated['rejection_reason_custom'] ?? ''))
        );
        if (! $rejectionReason) {
            return back()->with('error', 'Select a rejection reason.');
        }

        $product->update([
            'status' => Product::STATUS_REJECTED,
            'is_active' => 0,
            'rejection_reason' => $rejectionReason,
        ]);

        $product->loadMissing('user.sellerProfile');
        $sellerNotifications->productRejected($product);

        return back()->with('success', 'Product rejected.');
    }

    public function bulkUpdate(BulkUpdateProductsRequest $request, SellerNotificationService $sellerNotifications): RedirectResponse
    {
        $this->authorize('bulkModerate', Product::class);
        $validated = $request->validated();

        $products = Product::with('user.sellerProfile')->whereIn('id', $validated['product_ids'])->get();

        if ($validated['action'] === 'approve') {
            foreach ($products as $product) {
                $product->update([
                    'status' => Product::STATUS_APPROVED,
                    'is_active' => 1,
                    'rejection_reason' => null,
                ]);

                $sellerNotifications->productApproved($product);
            }

            return back()->with('success', 'Selected products approved.');
        }

        $rejectionReason = $this->resolveRejectionReason(
            (string) ($validated['rejection_reason_key'] ?? ''),
            trim((string) ($validated['rejection_reason_custom'] ?? ''))
        );
        if (! $rejectionReason) {
            return back()->with('error', 'Select a rejection reason.');
        }

        foreach ($products as $product) {
            $product->update([
                'status' => Product::STATUS_REJECTED,
                'is_active' => 0,
                'rejection_reason' => $rejectionReason,
            ]);

            $sellerNotifications->productRejected($product);
        }

        return back()->with('success', 'Selected products rejected.');
    }
}
