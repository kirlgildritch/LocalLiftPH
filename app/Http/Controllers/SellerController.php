<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    public function create()
    {
        if (!Auth::guard('seller')->check()) {
            return redirect()->route('seller.login');
        }

        if (Seller::where('user_id', Auth::guard('seller')->id())->exists()) {
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

        Seller::updateOrCreate([
            'user_id' => Auth::guard('seller')->id(),
        ], [
            'store_name' => $request->store_name,
            'store_description' => $request->store_description,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
        ]);

        return redirect()->route('seller.dashboard')->with('success', 'Your seller center setup is complete.');
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
