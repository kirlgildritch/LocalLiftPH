<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use App\Support\LocationBrowsing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = trim((string) $request->get('category'));
        $sort = $request->get('sort', 'newest');
        $province = LocationBrowsing::normalized($request->get('province'));
        $city = LocationBrowsing::normalized($request->get('city'));
        $nearMe = $request->boolean('near_me');
        $defaultAddress = Auth::guard('web')->check()
            ? Auth::user()?->addresses()->orderByDesc('is_default')->latest('id')->first()
            : null;

        if ($nearMe && $defaultAddress) {
            $sort = 'nearest';
        } elseif ($nearMe) {
            $nearMe = false;
        }

        $categories = Category::withCount([
            'products' => function ($query) {
                $query->visibleToBuyers();
            },
        ])
            ->whereHas('products', function ($query) {
                $query->visibleToBuyers();
            })
            ->orderBy('name')
            ->get();

        $shopsQuery = User::query()
            ->select(['users.id', 'users.name', 'users.created_at'])
            ->with([
                'sellerProfile:id,user_id,store_name,shop_logo,city,province,region,application_status,suspended_at,shop_status,shop_status_until',
            ])
            ->withCount([
                'products' => function ($query) {
                    $query->visibleToBuyers();
                }
            ])
            ->visibleSellerShops()
            ->whereHas('products', function ($query) use ($categorySlug) {
                $query->visibleToBuyers()
                    ->when($categorySlug, function ($categoryQuery) use ($categorySlug) {
                        $categoryQuery->whereHas('category', function ($nestedCategoryQuery) use ($categorySlug) {
                            $nestedCategoryQuery->where('slug', $categorySlug);
                        });
                    });
            });

        LocationBrowsing::applyShopLocationFilter($shopsQuery, $province, $city);

        if ($sort === 'nearest' && $defaultAddress) {
            $shopsQuery->leftJoin('sellers as seller_locations', 'seller_locations.user_id', '=', 'users.id');
        }

        $shops = match ($sort) {
            'most_products' => $shopsQuery->orderByDesc('products_count')->get(),
            'name_asc' => $shopsQuery->orderBy('name')->get(),
            'name_desc' => $shopsQuery->orderByDesc('name')->get(),
            'nearest' => $defaultAddress
                ? LocationBrowsing::orderByNearest($shopsQuery, 'seller_locations', $defaultAddress)->latest('users.created_at')->get()
                : $shopsQuery->latest()->get(),
            default => $shopsQuery->latest()->get(), 
        };

        $locationOptions = LocationBrowsing::locationOptionsForVisibleSellerProducts();

        $buyerLocation = $defaultAddress;

        return view('shops.index', compact(
            'shops',
            'categories',
            'categorySlug',
            'sort',
            'province',
            'city',
            'nearMe',
            'buyerLocation',
            'locationOptions'
        ));
    }

    public function show(Request $request, User $user)
    {
        $user->loadMissing('sellerProfile');

        if (! $user->isSeller()
            || ! $user->sellerProfile?->isMarketplaceVisible()) {
            abort(404);
        }

        $products = $user->products()
            ->select(['products.id', 'products.user_id', 'products.category_id', 'products.name', 'products.price', 'products.image', 'products.created_at'])
            ->with([
                'user:id,name',
                'user.sellerProfile:id,user_id,store_name,city,province,region,application_status,suspended_at,shop_status,shop_status_until',
                'category:id,name',
            ])
            ->withRatings()
            ->visibleToBuyers()
            ->latest()
            ->get();

        $initialReviewsLimit = 3;
        $showAllReviews = $request->query('show_reviews') === 'all';

        $sellerReviewsBaseQuery = Review::query()
            ->whereHas('product', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->visibleToBuyers();
            });

        $sellerReviews = (clone $sellerReviewsBaseQuery)
            ->with(['user', 'media', 'product.category'])
            ->latest()
            ->when(! $showAllReviews, function ($query) use ($initialReviewsLimit) {
                $query->limit($initialReviewsLimit);
            })
            ->get();

        $sellerReviewCount = (clone $sellerReviewsBaseQuery)->count();
        $sellerReviewAverage = round((float) ((clone $sellerReviewsBaseQuery)->avg('rating') ?? 0), 1);

        return view('shops.show', compact(
            'user',
            'products',
            'sellerReviews',
            'sellerReviewCount',
            'sellerReviewAverage',
            'showAllReviews',
            'initialReviewsLimit'
        ));
    }
}
