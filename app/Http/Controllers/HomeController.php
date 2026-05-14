<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::query()
            ->select(['products.id', 'products.user_id', 'products.category_id', 'products.name', 'products.price', 'products.image', 'products.created_at'])
            ->with([
                'user:id,name',
                'user.sellerProfile:id,user_id,store_name,city,province,region,application_status,suspended_at,shop_status,shop_status_until',
                'category:id,name',
            ])
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

        return view('home', compact('featuredProducts', 'featuredCategories'));
    }
}
