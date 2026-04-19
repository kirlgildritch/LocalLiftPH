<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

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
            ->where('is_active', 1)
            ->where('status', 'approved'); // only approved products visible

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
                $query->where('is_active', 1)->where('status', 'approved');
            }
        ])->orderBy('name')->get();

        $shops = User::withCount([
            'products' => function ($query) {
                $query->where('is_active', 1)->where('status', 'approved');
            }
        ])
            ->where('is_seller', 1)
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
        abort_if($product->status !== 'approved', 404);

        $product->load(['user', 'category']);

        $relatedProducts = Product::with(['user', 'category'])
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->where('is_active', 1)
            ->where('status', 'approved')
            ->latest()
            ->take(3)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}