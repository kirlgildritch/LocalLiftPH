<?php

namespace App\Http\Controllers\Seller;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
     public function index()
    {
        $products = Product::where('user_id', Auth::id())->latest()->get();

        return view('seller.manage_products', compact('products'));
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
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

    Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'image' => $imagePath,
            'user_id' => auth()->id(),
            'is_active' => 1,
        ]);

        return redirect()->back()->with('success', 'Product added successfully.');
    }
}
