<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    protected function validatedAddress(Request $request): array
    {
        return $request->validate([
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
    }

    protected function ownedAddressOrFail(Address $address): Address
    {
        abort_unless((int) $address->user_id === (int) Auth::id(), 403);

        return $address;
    }

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
        $validated = $this->validatedAddress($request);

        DB::transaction(function () use ($validated) {
            if (($validated['is_default'] ?? false) || ! Address::where('user_id', Auth::id())->exists()) {
                Address::where('user_id', Auth::id())->update(['is_default' => false]);
                $validated['is_default'] = true;
            }

            Address::create(array_merge($validated, [
                'user_id' => Auth::id(),
            ]));
        });

        return redirect()->to(request('return_to') ?: route('buyer.addresses'))
            ->with('success', 'Address saved successfully.');
    }

    public function update(Request $request, Address $address)
    {
        $address = $this->ownedAddressOrFail($address);
        $validated = $this->validatedAddress($request);

        DB::transaction(function () use ($address, $validated) {
            if (($validated['is_default'] ?? false) || $address->is_default) {
                Address::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update($validated + [
                'is_default' => (bool) ($validated['is_default'] ?? $address->is_default),
            ]);
        });

        return back()->with('address_success', 'Address updated successfully.');
    }

    public function destroy(Address $address)
    {
        $address = $this->ownedAddressOrFail($address);
        $wasDefault = $address->is_default;

        DB::transaction(function () use ($address, $wasDefault) {
            $address->delete();

            if ($wasDefault) {
                Address::where('user_id', Auth::id())
                    ->oldest('id')
                    ->limit(1)
                    ->update(['is_default' => true]);
            }
        });

        return back()->with('address_success', 'Address deleted successfully.');
    }

    public function setDefault(Address $address)
    {
        $address = $this->ownedAddressOrFail($address);

        DB::transaction(function () use ($address) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return back()->with('address_success', 'Default address updated.');
    }
}
