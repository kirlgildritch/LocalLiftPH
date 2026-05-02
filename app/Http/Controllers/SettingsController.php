<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminActivityNotification;
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

        $changedFields = [];
        $originalValues = [
            'store_name' => $seller->store_name,
            'store_description' => $seller->store_description,
            'contact_number' => $seller->contact_number,
            'address' => $seller->address,
        ];

        if ($request->hasFile('shop_logo')) {
            if (! $request->file('shop_logo')->isValid()) {
                return back()->withErrors(['shop_logo' => 'The shop logo failed to upload.'])->withInput();
            }

            $oldLogo = $seller->shop_logo;
            $validated['shop_logo'] = $request->file('shop_logo')->store('shop_logos', 'public');

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $changedFields[] = 'shop logo';
        }

        foreach ([
            'store_name' => 'store name',
            'store_description' => 'store description',
            'contact_number' => 'contact number',
            'address' => 'address',
        ] as $field => $label) {
            if (($validated[$field] ?? null) !== $originalValues[$field]) {
                $changedFields[] = $label;
            }
        }

        $seller->update($validated);

        if ($changedFields !== []) {
            $this->notifyAdmins(
                new AdminActivityNotification(
                    'seller_review',
                    'Seller shop settings updated',
                    ($seller->store_name ?: (Auth::user()?->name ?? 'A seller')) . ' updated shop settings: ' . $this->formatFieldList($changedFields) . '.',
                    'admin.sellers',
                )
            );
        }

        return back()->with('success', 'Shop updated successfully.');
    }

    public function preview()
    {
        $seller = $this->currentSeller();
        $products = \App\Models\Product::where('user_id', Auth::id())->latest()->get();

        return view('seller.shop-preview', compact('seller', 'products'));
    }

    private function notifyAdmins(AdminActivityNotification $notification): void
    {
        User::query()
            ->where(function ($query) {
                $query->where('is_admin', true)
                    ->orWhere('role', 'admin');
            })
            ->get()
            ->each
            ->notify($notification);
    }

    private function formatFieldList(array $fields): string
    {
        $fields = array_values(array_unique($fields));
        $count = count($fields);

        if ($count === 0) {
            return 'details';
        }

        if ($count === 1) {
            return $fields[0];
        }

        if ($count === 2) {
            return $fields[0] . ' and ' . $fields[1];
        }

        $lastField = array_pop($fields);

        return implode(', ', $fields) . ', and ' . $lastField;
    }
}
