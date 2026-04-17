<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = trim((string) $request->get('category'));
        $sort = $request->get('sort', 'newest');

        $categories = Category::withCount([
            'products' => function ($query) {
                $query->where('is_active', 1);
            },
        ])
            ->whereHas('products', function ($query) {
                $query->where('is_active', 1);
            })
            ->orderBy('name')
            ->get();

        $shopsQuery = User::withCount(['products' => function ($query) {
                $query->where('is_active', 1);
            }])
            ->where('is_seller', 1)
            ->whereHas('products', function ($query) use ($categorySlug) {
                $query->where('is_active', 1)
                    ->when($categorySlug, function ($categoryQuery) use ($categorySlug) {
                        $categoryQuery->whereHas('category', function ($nestedCategoryQuery) use ($categorySlug) {
                            $nestedCategoryQuery->where('slug', $categorySlug);
                        });
                    });
            });

        $shops = match ($sort) {
            'most_products' => $shopsQuery->orderByDesc('products_count')->get(),
            'name_asc' => $shopsQuery->orderBy('name')->get(),
            'name_desc' => $shopsQuery->orderByDesc('name')->get(),
            default => $shopsQuery->latest()->get(),
        };

        return view('shops.index', compact('shops', 'categories', 'categorySlug', 'sort'));
    }

    public function show(User $user)
    {
        if (! $user->isSeller()) {
            abort(404);
        }

        $products = $user->products()
            ->with('category')
            ->where('is_active', 1)
            ->latest()
            ->get();

        return view('shops.show', compact('user', 'products'));
    }
}
