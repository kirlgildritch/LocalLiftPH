<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductBrowseController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->search);
        $categorySlug = trim((string) $request->get('category'));
        $minPrice = $request->filled('min_price') ? (float) $request->get('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->get('max_price') : null;
        $sort = $request->get('sort', 'newest');

        $productsQuery = Product::with(['user', 'category'])
            ->visibleToBuyers();

        if ($search) {
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($sellerQuery) use ($search) {
                        $sellerQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($categorySlug) {
            $productsQuery->whereHas('category', function ($categoryQuery) use ($categorySlug) {
                $categoryQuery->where('slug', $categorySlug);
            });
        }

        if ($minPrice !== null) {
            $productsQuery->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $productsQuery->where('price', '<=', $maxPrice);
        }

        $products = match ($sort) {
            'price_asc' => $productsQuery->orderBy('price')->get(),
            'price_desc' => $productsQuery->orderByDesc('price')->get(),
            'oldest' => $productsQuery->oldest()->get(),
            default => $productsQuery->latest()->get(),
        };

        $categories = Category::withCount([
            'products' => function ($query) {
                $query->visibleToBuyers();
            }
        ])->orderBy('name')->get();

        $shops = User::withCount([
            'products' => function ($query) {
                $query->visibleToBuyers();
            }
        ])
            ->where('is_seller', 1)
            ->whereHas('sellerProfile', function ($query) {
                $query->where('application_status', \App\Models\Seller::STATUS_APPROVED);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->take(6)
            ->get();

        return view('products.index', compact(
            'products',
            'shops',
            'search',
            'categories',
            'categorySlug',
            'minPrice',
            'maxPrice',
            'sort'
        ));
    }

    public function show(Product $product)
    {
        abort_if(
            $product->status !== Product::STATUS_APPROVED
            || ! $product->is_active
            || $product->user?->sellerProfile?->application_status !== \App\Models\Seller::STATUS_APPROVED,
            404
        );

        $product->load([
            'user',
            'category',
            'reviews' => function ($query) {
                $query->with('user')->latest();
            },
        ])->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        $reviewableOrderItems = collect();

        if (Auth::guard('web')->check()) {
            $reviewableOrderItems = OrderItem::with('order')
                ->where('product_id', $product->id)
                ->whereDoesntHave('review')
                ->whereHas('order', function ($query) {
                    $query->where('user_id', Auth::id())
                        ->where('shipping_status', Order::SHIPPING_DELIVERED);
                })
                ->latest()
                ->get();
        }

        $relatedProducts = Product::with(['user', 'category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->visibleToBuyers()
            ->latest()
            ->take(3)
            ->get();

        return view('products.show', compact('product', 'relatedProducts', 'reviewableOrderItems'));
    }
}
