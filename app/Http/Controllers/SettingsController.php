<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seller;

class SettingsController extends Controller
{
    public function index()
    {
        $seller = Seller::where('user_id', auth()->id())->first();

        return view('seller.settings', compact('seller'));
    }

    public function update(Request $request)
{
    $request->validate([
        'store_name' => 'required|string|max:255',
        'store_description' => 'nullable|string',
        'contact_number' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'shop_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $seller = Seller::where('user_id', auth()->id())->first();

    if (!$seller) {
        return back()->with('error', 'Seller record not found.');
    }

    $seller->store_name = $request->store_name;
    $seller->store_description = $request->store_description;
    $seller->contact_number = $request->contact_number;
    $seller->address = $request->address;

    if ($request->hasFile('shop_logo')) {
        $path = $request->file('shop_logo')->store('shop_logos', 'public');
        $seller->shop_logo = $path;
    }

    $seller->save();

    return back()->with('success', 'Shop updated successfully.');
}
   public function preview()
    {
        $seller = \App\Models\Seller::where('user_id', auth()->id())->first();
        $products = \App\Models\Product::where('user_id', auth()->id())->latest()->get();

        return view('seller.shop-preview', compact('seller', 'products'));
    }
}