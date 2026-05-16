<?php

namespace App\Http\Controllers;

use App\Http\Requests\Seller\UpdateSellerInventoryRequest;
use App\Http\Requests\Seller\UpdateSellerPayoutRequest;
use App\Http\Requests\Seller\UpdateSellerSettingsRequest;
use App\Http\Requests\Seller\UpdateSellerStatusRequest;
use App\Models\Seller;
use App\Services\AdminActivityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    private function readableAddress(array $validated): string
    {
        return collect([
            $validated['street_address'] ?? null,
            filled($validated['landmark'] ?? null) ? 'Landmark: ' . $validated['landmark'] : null,
            $validated['barangay'] ?? null,
            $validated['city'] ?? null,
            $validated['province'] ?? null,
            $validated['region'] ?? null,
            $validated['postal_code'] ?? null,
        ])->filter()->implode(', ');
    }

    protected function currentSeller(): ?Seller
    {
        $sellerUserId = Auth::guard('seller')->id() ?? Auth::id();

        return Seller::where('user_id', $sellerUserId)->first();
    }

    public function index()
    {
        $seller = $this->currentSeller();

        return view('seller.settings', compact('seller'));
    }

    public function update(UpdateSellerSettingsRequest $request, AdminActivityService $adminActivity)
    {
        $validated = $request->validated();

        $seller = $this->currentSeller();

        if (! $seller) {
            return back()->with('error', 'Seller record not found.');
        }

        $changedFields = [];
        $originalValues = [
            'store_name' => $seller->store_name,
            'store_description' => $seller->store_description,
            'contact_number' => $seller->contact_number,
            'street_address' => $seller->street_address,
            'barangay' => $seller->barangay,
            'city' => $seller->city,
            'province' => $seller->province,
            'region' => $seller->region,
            'postal_code' => $seller->postal_code,
            'landmark' => $seller->landmark,
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
            'street_address' => 'street address',
            'barangay' => 'barangay',
            'city' => 'city',
            'province' => 'province',
            'region' => 'region',
            'postal_code' => 'postal code',
            'landmark' => 'landmark',
        ] as $field => $label) {
            if (($validated[$field] ?? null) !== $originalValues[$field]) {
                $changedFields[] = $label;
            }
        }

        $validated['address'] = $this->readableAddress($validated);
        $seller->update($validated);

        if ($changedFields !== []) {
            $adminActivity->sellerShopSettingsUpdated($seller->fresh('user'), $changedFields);
        }

        return back()->with('success', 'Shop updated successfully.');
    }

    public function preview()
    {
        $seller = $this->currentSeller();
        $products = \App\Models\Product::where('user_id', Auth::id())->latest()->get();

        return view('seller.shop-preview', compact('seller', 'products'));
    }

    public function updatePayout(UpdateSellerPayoutRequest $request, AdminActivityService $adminActivity)
    {
        $validated = $request->validated();

        $seller = $this->currentSeller();

        if (! $seller) {
            return redirect()->to(route('seller.settings') . '#payout')->with('error', 'Seller record not found.');
        }

        $changedFields = [];

        foreach ([
            'payout_method' => 'payout method',
            'payout_account_name' => 'account name',
            'payout_account_number' => 'account number',
        ] as $field => $label) {
            if (($seller->{$field} ?? null) !== ($validated[$field] ?? null)) {
                $changedFields[] = $label;
            }
        }

        $seller->update($validated);

        if ($changedFields !== []) {
            $adminActivity->sellerPayoutUpdated($seller->fresh('user'), $changedFields);
        }

        return redirect()->to(route('seller.settings') . '#payout')->with('success', 'Payout details saved.');
    }

    public function updateInventory(UpdateSellerInventoryRequest $request, AdminActivityService $adminActivity)
    {
        $seller = $this->currentSeller();

        if (! $seller) {
            return redirect()->to(route('seller.settings') . '#inventory')->with('error', 'Seller record not found.');
        }

        $validated = $request->validated();
        $updates = [
            'low_stock_threshold' => (int) $validated['low_stock_threshold'],
            'hide_out_of_stock' => $request->boolean('hide_out_of_stock'),
        ];

        $changedFields = [];

        if ((int) ($seller->low_stock_threshold ?? 0) !== $updates['low_stock_threshold']) {
            $changedFields[] = 'low stock alert';
        }

        if ((bool) ($seller->hide_out_of_stock ?? false) !== $updates['hide_out_of_stock']) {
            $changedFields[] = 'sold out visibility';
        }

        $seller->update($updates);

        if ($changedFields !== []) {
            $adminActivity->sellerInventoryUpdated($seller->fresh('user'), $changedFields);
        }

        return redirect()->to(route('seller.settings') . '#inventory')->with('success', 'Inventory settings saved.');
    }

    public function updateStatus(UpdateSellerStatusRequest $request, AdminActivityService $adminActivity)
    {
        $seller = $this->currentSeller();

        if (! $seller) {
            return redirect()->to(route('seller.settings') . '#status')->with('error', 'Seller record not found.');
        }

        $validated = $request->validated();

        $previousStatus = $seller->normalizedShopStatus();
        $previousUntil = $seller->shop_status_until?->toDateString();
        $shopStatusUntil = ($validated['shop_status'] ?? null) === Seller::SHOP_STATUS_TEMPORARILY_CLOSED
            ? ($validated['shop_status_until'] ?? null)
            : null;

        $seller->update([
            'shop_status' => $validated['shop_status'],
            'shop_status_until' => $shopStatusUntil,
        ]);

        if ($previousStatus !== $validated['shop_status'] || $previousUntil !== $shopStatusUntil) {
            $statusMessage = $seller->shopStatusLabel();

            if ($seller->normalizedShopStatus() === Seller::SHOP_STATUS_TEMPORARILY_CLOSED && $seller->shop_status_until) {
                $statusMessage .= ' until ' . $seller->shop_status_until->format('M d, Y');
            }

            $adminActivity->sellerStatusUpdated($seller->fresh('user'), $statusMessage);
        }

        return redirect()->to(route('seller.settings') . '#status')->with('success', 'Shop status saved.');
    }
}
