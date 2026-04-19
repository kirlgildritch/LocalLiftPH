<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApprovalController extends Controller
{
    public function index()
    {
        $products = Product::with(['user', 'category'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.products.pending', compact('products'));
    }

    public function approve(Product $product)
    {
        $product->update([
            'status' => 'approved',
            'is_active' => 1,
        ]);

        return back()->with('approved_product', $product->name);
    }

    public function reject(Product $product)
    {
        $product->update([
            'status' => 'rejected',
            'is_active' => 0,
        ]);

        return back()->with('success', 'Product rejected.');
    }
}