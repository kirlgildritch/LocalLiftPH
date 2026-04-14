<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seller;

class SellerController extends Controller
{
    public function create()
    {
        if (auth()->user()->is_seller) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.setup');
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'required|string|max:1000',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'agree' => 'required',
        ]);

        $user = auth()->user();

        Seller::create([
            'user_id' => $user->id,
            'store_name' => $request->store_name,
            'store_description' => $request->store_description,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
        ]);

        $user->is_seller = 1;
        $user->save();

        return redirect()->route('seller.dashboard')->with('success', 'You are now registered as a seller.');
    }
    public function preview()
    {
        $seller = \App\Models\Seller::where('user_id', auth()->id())->first();

    $products = collect();

    if ($seller) {
        $products = \App\Models\Product::where('seller_id', $seller->id)->get();
    }

    return view('seller.shop-preview', compact('seller', 'products'));
    }
}