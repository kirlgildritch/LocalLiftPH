<?php

namespace App\Http\Controllers\Seller;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    private function calculateShippingFee(float $weight, float $widthCm, float $lengthCm, float $heightCm): float
    {
        $volumetricWeight = ($widthCm * $lengthCm * $heightCm) / 5000;
        $billableWeight = max($weight, $volumetricWeight);

        return round(60 + ($billableWeight * 35), 2);
    }

    public function index()
    {
        $currentTab = request('status', 'live');
        $allowedTabs = ['live', 'sold_out', 'reviewing', 'violation', 'delisted'];

        if (!in_array($currentTab, $allowedTabs, true)) {
            $currentTab = 'live';
        }

        $baseQuery = Product::where('user_id', Auth::id());

        $statusCounts = [
            'live' => (clone $baseQuery)
                ->where('status', 'approved')
                ->where('is_active', 1)
                ->where('stock', '>', 0)
                ->count(),
            'sold_out' => (clone $baseQuery)
                ->where('status', 'approved')
                ->where('is_active', 1)
                ->where('stock', '<=', 0)
                ->count(),
            'reviewing' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),
            'violation' => (clone $baseQuery)
                ->where('status', 'rejected')
                ->count(),
            'delisted' => (clone $baseQuery)
                ->where('is_active', 0)
                ->whereNotIn('status', ['pending', 'rejected'])
                ->count(),
        ];

        $productsQuery = Product::with([
            'category',
            'reviews' => function ($query) {
                $query->with('user')->latest();
            },
        ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('user_id', Auth::id());

        switch ($currentTab) {
            case 'sold_out':
                $productsQuery
                    ->where('status', 'approved')
                    ->where('is_active', 1)
                    ->where('stock', '<=', 0);
                break;
            case 'reviewing':
                $productsQuery->where('status', 'pending');
                break;
            case 'violation':
                $productsQuery->where('status', 'rejected');
                break;
            case 'delisted':
                $productsQuery
                    ->where('is_active', 0)
                    ->whereNotIn('status', ['pending', 'rejected']);
                break;
            case 'live':
            default:
                $productsQuery
                    ->where('status', 'approved')
                    ->where('is_active', 1)
                    ->where('stock', '>', 0);
                break;
        }

        $products = $productsQuery->latest()->get();

        return view('seller.manage_products', compact('products', 'statusCounts', 'currentTab'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('seller.add_product', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'condition' => 'required|in:new,used',
            'description' => 'required|string',
            'weight' => 'required|numeric|min:0.01',
            'width_cm' => 'required|numeric|min:0.01',
            'length_cm' => 'required|numeric|min:0.01',
            'height_cm' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $shippingFee = $this->calculateShippingFee(
            (float) $request->weight,
            (float) $request->width_cm,
            (float) $request->length_cm,
            (float) $request->height_cm
        );

        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'condition' => $request->condition,
            'description' => $request->description,
            'weight' => $request->weight,
            'width_cm' => $request->width_cm,
            'length_cm' => $request->length_cm,
            'height_cm' => $request->height_cm,
            'shipping_fee' => $shippingFee,
            'image' => $imagePath,
            'user_id' => auth()->id(),
            'is_active' => 0, // hidden by default
            'status' => 'pending', // for admin approval
        ]);

        return redirect()->back()->with('success', 'Product submitted for approval.');
    }
}
