<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminActivityNotification;
use Illuminate\Http\Request;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
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

    public function updatePayout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payout_method' => ['required', 'string', 'max:50'],
            'payout_account_name' => ['required', 'string', 'max:255'],
            'payout_account_number' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return redirect()->to(route('seller.settings') . '#payout')->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

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
            $this->notifyAdmins(
                new AdminActivityNotification(
                    'seller_review',
                    'Seller payout details updated',
                    ($seller->store_name ?: (Auth::user()?->name ?? 'A seller')) . ' updated payout details: ' . $this->formatFieldList($changedFields) . '.',
                    'admin.payouts',
                )
            );
        }

        return redirect()->to(route('seller.settings') . '#payout')->with('success', 'Payout details saved.');
    }

    public function updateInventory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:9999'],
            'hide_out_of_stock' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->to(route('seller.settings') . '#inventory')->withErrors($validator)->withInput();
        }

        $seller = $this->currentSeller();

        if (! $seller) {
            return redirect()->to(route('seller.settings') . '#inventory')->with('error', 'Seller record not found.');
        }

        $validated = $validator->validated();
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
            $this->notifyAdmins(
                new AdminActivityNotification(
                    'seller_review',
                    'Seller inventory settings updated',
                    ($seller->store_name ?: (Auth::user()?->name ?? 'A seller')) . ' updated inventory settings: ' . $this->formatFieldList($changedFields) . '.',
                    'admin.sellers',
                )
            );
        }

        return redirect()->to(route('seller.settings') . '#inventory')->with('success', 'Inventory settings saved.');
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_status' => ['required', 'string', 'in:open,temporarily_closed,vacation'],
            'shop_status_until' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        if ($validator->fails()) {
            return redirect()->to(route('seller.settings') . '#status')->withErrors($validator)->withInput();
        }

        $seller = $this->currentSeller();

        if (! $seller) {
            return redirect()->to(route('seller.settings') . '#status')->with('error', 'Seller record not found.');
        }

        $validated = $validator->validated();
        if (($validated['shop_status'] ?? null) === Seller::SHOP_STATUS_TEMPORARILY_CLOSED && empty($validated['shop_status_until'])) {
            return redirect()
                ->to(route('seller.settings') . '#status')
                ->withErrors(['shop_status_until' => 'Select an until date.'])
                ->withInput();
        }

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

            $this->notifyAdmins(
                new AdminActivityNotification(
                    'seller_review',
                    'Seller shop status updated',
                    ($seller->store_name ?: (Auth::user()?->name ?? 'A seller')) . ' changed shop status to ' . $statusMessage . '.',
                    'admin.sellers',
                )
            );
        }

        return redirect()->to(route('seller.settings') . '#status')->with('success', 'Shop status saved.');
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
