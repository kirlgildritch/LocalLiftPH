<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $category = trim((string) $request->get('category'));
        $sort = $request->get('sort', 'newest');

        $categories = Product::query()
            ->where('is_active', 1)
            ->selectRaw('category, COUNT(*) as product_count')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $shopsQuery = User::withCount(['products' => function ($query) {
                $query->where('is_active', 1);
            }])
            ->where('role', 'seller')
            ->whereHas('products', function ($query) use ($category) {
                $query->where('is_active', 1)
                    ->when($category, function ($categoryQuery) use ($category) {
                        $categoryQuery->where('category', $category);
                    });
            });

        $shops = match ($sort) {
            'most_products' => $shopsQuery->orderByDesc('products_count')->get(),
            'name_asc' => $shopsQuery->orderBy('name')->get(),
            'name_desc' => $shopsQuery->orderByDesc('name')->get(),
            default => $shopsQuery->latest()->get(),
        };

        return view('shops.index', compact('shops', 'categories', 'category', 'sort'));
    }

    public function show(User $user)
    {
        if ($user->role !== 'seller') {
            abort(404);
        }

        $products = $user->products()
            ->where('is_active', 1)
            ->latest()
            ->get();

        return view('shops.show', compact('user', 'products'));
    }
}
