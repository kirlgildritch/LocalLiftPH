<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    protected function redirectTarget(Request $request): string
    {
        return $request->input('return_to') ?: route('buyer.addresses');
    }

    protected function validatedAddress(Request $request): array
    {
        return $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'region' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'landmark' => 'required|string|max:255',
            'label' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean',
        ], [
            'full_name.required' => 'Please enter the recipient name.',
            'phone.required' => 'Please enter the phone number.',
            'region.required' => 'Please select a region.',
            'province.required' => 'Please select a province.',
            'city.required' => 'Please select a city or municipality.',
            'barangay.required' => 'Please select a barangay.',
            'street_address.required' => 'Please enter the street address.',
            'postal_code.required' => 'Please enter the postal code.',
            'landmark.required' => 'Please enter a landmark.',
        ]);
    }

    protected function ownedAddressOrFail(Address $address): Address
    {
        abort_unless((int) $address->user_id === (int) Auth::id(), 403);

        return $address;
    }

    public function index(Request $request)
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $returnTo = $request->query('return_to');

        return view('buyer.addresses', compact('addresses', 'returnTo'));
    }

    public function create(Request $request)
    {
        $returnTo = $request->query('return_to');

        return view('buyer.add_address', compact('returnTo'));
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

        return redirect()->to($this->redirectTarget($request))
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

        return redirect()->to($this->redirectTarget($request))
            ->with('address_success', 'Address updated successfully.');
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

        return redirect()->to($this->redirectTarget($request))
            ->with('address_success', 'Address deleted successfully.');
    }

    public function setDefault(Address $address)
    {
        $address = $this->ownedAddressOrFail($address);

        DB::transaction(function () use ($address) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return redirect()->to($this->redirectTarget($request))
            ->with('address_success', 'Default address updated.');
    }
}
