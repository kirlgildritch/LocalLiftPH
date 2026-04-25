<?php

namespace App\Http\Controllers\Seller;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function edit($id)
    {
        $product = Product::where('user_id', Auth::guard('seller')->id())->findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('seller.products.edit', compact('product', 'categories'));
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

    public function update(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::guard('seller')->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|in:new,used',
            'description' => 'required|string',
            'weight' => 'required|numeric|min:0.01',
            'width_cm' => 'required|numeric|min:0.01',
            'length_cm' => 'required|numeric|min:0.01',
            'height_cm' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|max:2048',
        ]);

        $shippingFee = $this->calculateShippingFee(
            (float) $validated['weight'],
            (float) $validated['width_cm'],
            (float) $validated['length_cm'],
            (float) $validated['height_cm']
        );

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('products', 'public');

            if (! empty($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $newImagePath;
        }

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'condition' => $validated['condition'],
            'description' => $validated['description'],
            'weight' => $validated['weight'],
            'width_cm' => $validated['width_cm'],
            'length_cm' => $validated['length_cm'],
            'height_cm' => $validated['height_cm'],
            'shipping_fee' => $shippingFee,
            'image' => $validated['image'] ?? $product->image,
        ]);

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function reviews($id)
    {
        $product = Product::with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('user_id', Auth::guard('seller')->id())
            ->findOrFail($id);

        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('seller.products.reviews', compact('product', 'reviews'));
    }
}
