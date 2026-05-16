<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Category;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\RecentlyViewedProduct;
use App\Support\LocationBrowsing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ProductBrowseController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->search);
        $categorySlug = trim((string) $request->get('category'));
        $minPrice = $request->filled('min_price') ? (float) $request->get('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->get('max_price') : null;
        $province = LocationBrowsing::normalized($request->get('province'));
        $city = LocationBrowsing::normalized($request->get('city'));
        $nearMe = $request->boolean('near_me');
        $sort = $request->get('sort', 'newest');
        $defaultAddress = Auth::guard('web')->check()
            ? Auth::user()?->addresses()->orderByDesc('is_default')->latest('id')->first()
            : null;

        if ($nearMe && $defaultAddress) {
            $sort = 'nearest';
        } elseif ($nearMe) {
            $nearMe = false;
        }

        $productsQuery = Product::query()
            ->select('products.*')
            ->with(['user.sellerProfile', 'category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->visibleToBuyers();

        if ($search) {
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($sellerQuery) use ($search) {
                        $sellerQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user.sellerProfile', function ($sellerProfileQuery) use ($search) {
                        $sellerProfileQuery->where('store_name', 'like', "%{$search}%")
                            ->orWhere('store_description', 'like', "%{$search}%");
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

        LocationBrowsing::applySellerLocationFilter($productsQuery, $province, $city);

        if ($sort === 'nearest' && $defaultAddress) {
            $productsQuery->leftJoin('sellers as seller_locations', 'seller_locations.user_id', '=', 'products.user_id');
        }

        $sortedProductsQuery = match ($sort) {
            'price_asc' => $productsQuery->orderBy('price'),
            'price_desc' => $productsQuery->orderByDesc('price'),
            'oldest' => $productsQuery->oldest(),
            'nearest' => $defaultAddress
                ? LocationBrowsing::orderByNearest($productsQuery, 'seller_locations', $defaultAddress)->latest('products.created_at')
                : $productsQuery->latest(),
            default => $productsQuery->latest(),
        };

        $products = $sortedProductsQuery
            ->paginate(12)
            ->withQueryString();

        $categories = Category::withCount([
            'products' => function ($query) {
                $query->visibleToBuyers();
            }
        ])->orderBy('name')->get();

        $locationOptions = User::query()
            ->visibleSellerShops()
            ->whereHas('products', fn ($query) => $query->visibleToBuyers())
            ->whereHas('sellerProfile', fn ($query) => $query->whereNotNull('province'))
            ->with('sellerProfile:id,user_id,province,city')
            ->get()
            ->pluck('sellerProfile')
            ->filter()
            ->groupBy('province')
            ->map(fn ($profiles) => $profiles->pluck('city')->filter()->unique()->sort()->values())
            ->sortKeys();

        $shops = User::withCount([
            'products' => function ($query) {
                $query->visibleToBuyers();
            }
        ])
            ->visibleSellerShops()
            ->with('sellerProfile')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('sellerProfile', function ($sellerProfileQuery) use ($search) {
                            $sellerProfileQuery->where('store_name', 'like', '%' . $search . '%')
                                ->orWhere('store_description', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($province || $city, fn ($query) => LocationBrowsing::applyShopLocationFilter($query, $province, $city))
            ->latest()
            ->take(6)
            ->get();

        $buyerLocation = $defaultAddress;

        return view('products.index', compact(
            'products',
            'shops',
            'search',
            'categories',
            'categorySlug',
            'minPrice',
            'maxPrice',
            'sort',
            'province',
            'city',
            'nearMe',
            'buyerLocation',
            'locationOptions'
        ));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('q', ''));

        if (mb_strlen($search) < 1) {
            return response()->json([]);
        }

        $products = Product::query()
            ->visibleToBuyers()
            ->where('name', 'like', "%{$search}%")
            ->orderBy('name')
            ->limit(5)
            ->pluck('name');

        $shops = User::query()
            ->visibleSellerShops()
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('sellerProfile', function ($sellerProfileQuery) use ($search) {
                        $sellerProfileQuery->where('store_name', 'like', "%{$search}%")
                            ->orWhere('store_description', 'like', "%{$search}%");
                    });
            })
            ->orderBy('name')
            ->limit(3)
            ->get()
            ->map(function ($shop) {
                return $shop->sellerProfile?->store_name ?: $shop->name;
            });

        $categories = Category::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy('name')
            ->limit(2)
            ->pluck('name');

        $suggestions = $products
            ->concat($shops)
            ->concat($categories)
            ->map(fn($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values()
            ->take(8)
            ->map(fn($label) => [
                'label' => $label,
                'selectable' => true,
            ])
            ->values();

        if ($suggestions->isEmpty()) {
            return response()->json([
                [
                    'label' => 'Product not found.',
                    'selectable' => false,
                ],
            ]);
        }

        return response()->json($suggestions);
    }

    public function show(Request $request, Product $product)
    {
        abort_if(
            $product->status !== Product::STATUS_APPROVED
                || !$product->is_active
                || ! $product->user?->sellerProfile?->isMarketplaceVisible()
                || ((int) $product->stock <= 0 && ! ($product->user?->sellerProfile?->showsOutOfStockProducts() ?? false)),
            404
        );

        $product->load([
            'user.sellerProfile',
            'category',
            'media',
            'variants',
        ])->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        $initialReviewsLimit = 3;
        $showAllReviews = $request->query('show_reviews') === 'all';

        $reviewsQuery = Review::query()
            ->where('product_id', $product->id)
            ->with(['user', 'media'])
            ->latest();

        $reviews = $showAllReviews
            ? $reviewsQuery->get()
            : (clone $reviewsQuery)->limit($initialReviewsLimit)->get();

        $reviewableOrderItems = collect();

        if (Auth::guard('web')->check()) {
            $reviewableOrderItems = OrderItem::with('order')
                ->where('product_id', $product->id)
                ->whereDoesntHave('review')
                ->whereHas('order', function ($query) {
                    $query->where('user_id', Auth::id())
                        ->where('shipping_status', Order::SHIPPING_COMPLETED);
                })
                ->latest()
                ->get();
        }

        $isWishlisted = Auth::guard('web')->check()
            ? Wishlist::query()
                ->where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists()
            : false;

        $recentlyViewedProducts = collect();

        if (Auth::guard('web')->check()) {
            RecentlyViewedProduct::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                ],
                ['updated_at' => now()]
            );

            $recentIdsToKeep = RecentlyViewedProduct::query()
                ->where('user_id', Auth::id())
                ->latest('updated_at')
                ->limit(12)
                ->pluck('id');

            RecentlyViewedProduct::query()
                ->where('user_id', Auth::id())
                ->whereNotIn('id', $recentIdsToKeep)
                ->delete();

            $recentlyViewedProducts = RecentlyViewedProduct::query()
                ->with([
                    'product' => function ($query) {
                        $query
                            ->with(['user.sellerProfile', 'category'])
                            ->withAvg('reviews', 'rating')
                            ->withCount('reviews');
                    },
                ])
                ->where('user_id', Auth::id())
                ->where('product_id', '!=', $product->id)
                ->whereHas('product', function ($query) {
                    $query->visibleToBuyers();
                })
                ->latest('updated_at')
                ->take(4)
                ->get()
                ->pluck('product')
                ->filter()
                ->values();
        }

        $relatedProducts = Product::with(['user.sellerProfile', 'category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->visibleToBuyers()
            ->latest()
            ->take(3)
            ->get();

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'reviewableOrderItems',
            'reviews',
            'showAllReviews',
            'initialReviewsLimit',
            'isWishlisted',
            'recentlyViewedProducts'
        ));
    }
}
