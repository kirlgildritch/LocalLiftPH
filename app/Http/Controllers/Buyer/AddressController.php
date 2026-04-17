<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('buyer.addresses', compact('addresses'));
    }

    public function create()
    {
        return view('buyer.add_address');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'region' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'label' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        Address::create([
            'user_id' => Auth::id(),
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'region' => $request->region,
            'province' => $request->province,
            'city' => $request->city,
            'barangay' => $request->barangay,
            'street_address' => $request->street_address,
            'postal_code' => $request->postal_code,
            'label' => $request->label,
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('address_success', 'Address added successfully.');
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'region' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'label' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        $address->update([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'region' => $request->region,
            'province' => $request->province,
            'city' => $request->city,
            'barangay' => $request->barangay,
            'street_address' => $request->street_address,
            'postal_code' => $request->postal_code,
            'label' => $request->label,
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('address_success', 'Address updated successfully.');
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return back()->with('address_success', 'Address deleted successfully.');
    }

    public function setDefault(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Address::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('address_success', 'Default address updated.');
    }
}