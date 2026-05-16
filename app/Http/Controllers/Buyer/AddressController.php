<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buyer\SaveAddressRequest;
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

    public function store(SaveAddressRequest $request)
    {
        $validated = $request->validated();

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

    public function update(SaveAddressRequest $request, Address $address)
    {
        $address = $this->ownedAddressOrFail($address);
        $validated = $request->validated();

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

    public function destroy(Request $request, Address $address)
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

    public function setDefault(Request $request, Address $address)
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
