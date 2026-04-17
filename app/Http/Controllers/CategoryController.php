<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount([
            'products' => function ($query) {
                $query->where('is_active', 1);
            }
        ])
            ->whereHas('products', function ($query) {
                $query->where('is_active', 1);
            })
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return (object) [
                    'name' => $category->name,
                    'icon' => $category->icon ?? 'fa-box',
                    'count' => $category->products_count,
                    'slug' => $category->slug,
                ];
            });

        return view('categories.index', compact('categories'));
    }
}
