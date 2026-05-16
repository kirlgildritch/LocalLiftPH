<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\RecentlyViewedProduct;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with(['user.sellerProfile', 'category'])
            ->withRatings()
            ->visibleToBuyers()
            ->latest()
            ->take(12)
            ->get();

        $featuredCategories = Category::withCount([
            'products' => function ($query) {
                $query->visibleToBuyers();
            },
        ])
            ->whereHas('products', function ($query) {
                $query->visibleToBuyers();
            })
            ->orderByDesc('products_count')
            ->take(5)
            ->get()
            ->map(function ($category) {
                return (object) [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'count' => (int) $category->products_count,
                    'icon' => $category->icon ?? 'fa-grid-2',
                ];
            });

        $recentlyViewedProducts = collect();

        if (Auth::guard('web')->check()) {
            $recentlyViewedProducts = RecentlyViewedProduct::query()
                ->with([
                    'product' => function ($query) {
                        $query
                            ->with(['user.sellerProfile', 'category'])
                            ->withRatings();
                    },
                ])
                ->where('user_id', Auth::id())
                ->whereHas('product', function ($query) {
                    $query->visibleToBuyers();
                })
                ->latest('updated_at')
                ->take(6)
                ->get()
                ->pluck('product')
                ->filter()
                ->values();
        }

        return view('home', compact('featuredProducts', 'featuredCategories', 'recentlyViewedProducts'));
    }
}
