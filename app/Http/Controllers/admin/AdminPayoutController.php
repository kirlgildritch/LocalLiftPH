<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MarkPayoutPaidRequest;
use App\Models\Order;
use App\Models\SellerPayout;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AdminPayoutController extends Controller
{
    public function index(): View
    {
        $payouts = SellerPayout::with(['seller.user', 'orders'])
            ->latest('requested_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.payouts', compact('payouts'));
    }

    public function markPaid(MarkPayoutPaidRequest $request, SellerPayout $payout): RedirectResponse
    {
        if ($payout->status !== SellerPayout::STATUS_PENDING) {
            return back()->with('warning', 'This payout is already processed.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($payout, $validated): void {
            $payout->update([
                'status' => SellerPayout::STATUS_PAID,
                'reference_number' => $validated['reference_number'] ?? null,
                'processed_at' => now(),
            ]);

            $payout->orders()->update([
                'seller_earning_status' => Order::EARNING_PAID_OUT,
                'seller_released_at' => now(),
            ]);
        });

        return back()->with('success', 'Payout marked paid.');
    }

    public function reject(SellerPayout $payout): RedirectResponse
    {
        if ($payout->status !== SellerPayout::STATUS_PENDING) {
            return back()->with('warning', 'This payout is already processed.');
        }

        DB::transaction(function () use ($payout): void {
            $payout->update([
                'status' => SellerPayout::STATUS_REJECTED,
                'processed_at' => now(),
            ]);

            $payout->orders()->update([
                'seller_payout_id' => null,
                'seller_earning_status' => Order::EARNING_AVAILABLE,
            ]);
        });

        return back()->with('success', 'Payout rejected.');
    }
}
