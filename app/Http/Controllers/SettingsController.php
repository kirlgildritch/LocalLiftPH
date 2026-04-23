<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    protected function currentSeller(): ?Seller
    {
        return Seller::where('user_id', Auth::id())->first();
    }

    public function index()
    {
        $seller = $this->currentSeller();

        return view('seller.settings', compact('seller'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string|max:2000',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'shop_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $seller = $this->currentSeller();

        if (! $seller) {
            return back()->with('error', 'Seller record not found.');
        }

        if ($request->hasFile('shop_logo')) {
            if (! $request->file('shop_logo')->isValid()) {
                return back()->withErrors(['shop_logo' => 'The shop logo failed to upload.'])->withInput();
            }

            $oldLogo = $seller->shop_logo;
            $validated['shop_logo'] = $request->file('shop_logo')->store('shop_logos', 'public');

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        $seller->update($validated);

        return back()->with('success', 'Shop updated successfully.');
    }

    public function preview()
    {
        $seller = $this->currentSeller();
        $products = \App\Models\Product::where('user_id', Auth::id())->latest()->get();

        return view('seller.shop-preview', compact('seller', 'products'));
    }
}
