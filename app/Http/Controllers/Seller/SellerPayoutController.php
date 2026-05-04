<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SellerPayout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerPayoutController extends Controller
{
    public function store(): RedirectResponse
    {
        $sellerUser = Auth::guard('seller')->user() ?? Auth::user();
        $seller = $sellerUser?->sellerProfile;

        if (! $seller) {
            return back()->with('error', 'Seller record not found.');
        }

        if (! filled($seller->payout_method) || ! filled($seller->payout_account_name) || ! filled($seller->payout_account_number)) {
            return back()->with('error', 'Save payout details first.');
        }

        $hasPendingPayout = SellerPayout::query()
            ->where('seller_id', $seller->id)
            ->where('status', SellerPayout::STATUS_PENDING)
            ->exists();

        if ($hasPendingPayout) {
            return back()->with('warning', 'You already have a pending payout request.');
        }

        $eligibleOrders = Order::query()
            ->where('seller_id', $seller->user_id)
            ->where('seller_earning_status', Order::EARNING_AVAILABLE)
            ->whereNull('seller_payout_id')
            ->get();

        if ($eligibleOrders->isEmpty()) {
            return back()->with('warning', 'No available earnings to request.');
        }

        $amount = (float) $eligibleOrders->sum(fn (Order $order) => (float) ($order->total_price ?? 0));

        DB::transaction(function () use ($seller, $eligibleOrders, $amount): void {
            $payout = SellerPayout::create([
                'seller_id' => $seller->id,
                'amount' => $amount,
                'method' => (string) $seller->payout_method,
                'account_name' => (string) $seller->payout_account_name,
                'account_number' => (string) $seller->payout_account_number,
                'status' => SellerPayout::STATUS_PENDING,
                'requested_at' => now(),
            ]);

            Order::query()
                ->whereIn('id', $eligibleOrders->pluck('id'))
                ->update([
                    'seller_payout_id' => $payout->id,
                ]);
        });

        return back()->with('success', 'Payout requested.');
    }
}
