<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreVoucherRequest;
use App\Http\Requests\Seller\UpdateVoucherRequest;
use App\Models\Voucher;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SellerVoucherController extends Controller
{
    public function index(): View
    {
        $vouchers = Voucher::query()
            ->withCount('redemptions')
            ->where('seller_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('seller.vouchers.index', [
            'vouchers' => $vouchers,
            'editingVoucher' => null,
            'voucherTimezone' => $this->voucherTimezone(),
        ]);
    }

    public function edit(Voucher $voucher): View
    {
        $this->ensureOwnsVoucher($voucher);

        $vouchers = Voucher::query()
            ->withCount('redemptions')
            ->where('seller_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('seller.vouchers.index', [
            'vouchers' => $vouchers,
            'editingVoucher' => $voucher,
            'voucherTimezone' => $this->voucherTimezone(),
        ]);
    }

    public function store(StoreVoucherRequest $request): RedirectResponse
    {
        Voucher::create($this->payload($request->validated()) + [
            'seller_id' => Auth::id(),
        ]);

        return redirect()
            ->route('seller.vouchers.index')
            ->with('success', 'Voucher created successfully.');
    }

    public function update(UpdateVoucherRequest $request, Voucher $voucher): RedirectResponse
    {
        $this->ensureOwnsVoucher($voucher);

        $voucher->update($this->payload($request->validated()));

        return redirect()
            ->route('seller.vouchers.index')
            ->with('success', 'Voucher updated successfully.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $this->ensureOwnsVoucher($voucher);

        $voucher->delete();

        return redirect()
            ->route('seller.vouchers.index')
            ->with('success', 'Voucher deleted successfully.');
    }

    protected function payload(array $validated): array
    {
        return [
            'code' => $validated['code'],
            'name' => $validated['name'] ?? null,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'minimum_subtotal' => $validated['minimum_subtotal'] ?? 0,
            'maximum_discount' => $validated['maximum_discount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'per_user_limit' => $validated['per_user_limit'] ?? null,
            'starts_at' => $this->parseLocalDateTime($validated['starts_at'] ?? null),
            'ends_at' => $this->parseLocalDateTime($validated['ends_at'] ?? null),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ];
    }

    protected function ensureOwnsVoucher(Voucher $voucher): void
    {
        abort_unless((int) $voucher->seller_id === (int) Auth::id(), 404);
    }

    protected function parseLocalDateTime(?string $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        return Carbon::parse($value, $this->voucherTimezone())
            ->timezone(config('app.timezone', 'UTC'));
    }

    protected function voucherTimezone(): string
    {
        return config('app.market_timezone', 'Asia/Manila');
    }
}
