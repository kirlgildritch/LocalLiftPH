<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    public function create()
    {
        return redirect()->route('seller.dashboard', ['register' => 1]);
    }

    public function store(Request $request)
    {
        return redirect()->route('seller.dashboard', ['register' => 1]);
    }

    public function preview()
    {
        $seller = \App\Models\Seller::where('user_id', auth()->id())->first();

        $products = collect();

        if ($seller) {
            $products = \App\Models\Product::with('category')
                ->where('user_id', auth()->id())
                ->latest()
                ->get();
        }

        return view('seller.shop-preview', compact('seller', 'products'));
    }
}
