<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductBrowseController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->search);
        $category = trim((string) $request->get('category'));
        $minPrice = $request->filled('min_price') ? (float) $request->get('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->get('max_price') : null;
        $sort = $request->get('sort', 'newest');

        $productsQuery = Product::with('user')
            ->where('is_active', 1)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('category', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($sellerQuery) use ($search) {
                          $sellerQuery->where('name', 'like', '%' . $search . '%')
                                      ->where('role', 'seller');
                      });
                });
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($minPrice !== null, function ($query) use ($minPrice) {
                $query->where('price', '>=', $minPrice);
            })
            ->when($maxPrice !== null, function ($query) use ($maxPrice) {
                $query->where('price', '<=', $maxPrice);
            });

        $products = match ($sort) {
            'price_asc' => $productsQuery->orderBy('price')->get(),
            'price_desc' => $productsQuery->orderByDesc('price')->get(),
            'oldest' => $productsQuery->oldest()->get(),
            default => $productsQuery->latest()->get(),
        };

        $categories = Product::query()
            ->where('is_active', 1)
            ->selectRaw('category, COUNT(*) as product_count')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $shops = User::withCount(['products' => function ($query) {
                $query->where('is_active', 1);
            }])
            ->where('role', 'seller')
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
            'category',
            'minPrice',
            'maxPrice',
            'sort'
        ));
    }

    public function show(Product $product)
    {
        $product->load('user');

        $relatedProducts = Product::with('user')
            ->where('id', '!=', $product->id)
            ->where('category', $product->category)
            ->where('is_active', 1)
            ->latest()
            ->take(3)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function suggestions(Request $request)
    {
        $query = trim($request->get('q'));

        if (!$query) {
            return response()->json([]);
        }

        $productSuggestions = Product::where('is_active', 1)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('category', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->limit(5)
            ->pluck('name')
            ->toArray();

        $shopSuggestions = User::where('role', 'seller')
            ->where('name', 'like', '%' . $query . '%')
            ->limit(3)
            ->pluck('name')
            ->toArray();

        $suggestions = array_values(array_unique(array_merge($productSuggestions, $shopSuggestions)));

        return response()->json($suggestions);
    }
}
