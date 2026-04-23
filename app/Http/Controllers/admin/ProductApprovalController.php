<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductApprovalController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['user.sellerProfile', 'category'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.products', compact('products'));
    }

    public function approve(Product $product): RedirectResponse
    {
        if ($product->status !== Product::STATUS_PENDING) {
            return back()->with('error', 'Only pending products can be approved.');
        }

        $product->update([
            'status' => Product::STATUS_APPROVED,
            'is_active' => 1,
        ]);

        return back()->with('success', $product->name . ' approved successfully.');
    }

    public function reject(Product $product): RedirectResponse
    {
        if ($product->status !== Product::STATUS_PENDING) {
            return back()->with('error', 'Only pending products can be rejected.');
        }

        $product->update([
            'status' => Product::STATUS_REJECTED,
            'is_active' => 0,
        ]);

        return back()->with('success', 'Product rejected.');
    }
}
