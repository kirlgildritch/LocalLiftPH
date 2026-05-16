<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\RespondToOrderReturnRequest;
use App\Models\Order;
use App\Models\OrderReturnRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrderReturnRequestController extends Controller
{
    public function update(RespondToOrderReturnRequest $request, OrderReturnRequest $returnRequest): RedirectResponse
    {
        $returnRequest->loadMissing('order');
        $seller = Auth::guard('seller')->user();

        abort_if((int) $returnRequest->seller_id !== (int) $seller?->id, 403);

        if ($returnRequest->status !== OrderReturnRequest::STATUS_PENDING) {
            return redirect()
                ->route('seller.orders')
                ->with('error', 'This return/refund request has already been reviewed.');
        }

        $validated = $request->validated();

        $updates = [
            'status' => $validated['status'],
            'seller_response' => $validated['seller_response'],
            'reviewed_at' => now(),
        ];

        $returnRequest->update($updates);

        if ($validated['status'] === OrderReturnRequest::STATUS_APPROVED) {
            $returnRequest->order?->update([
                'seller_earning_status' => Order::EARNING_ON_HOLD,
            ]);
        }

        return redirect()
            ->route('seller.orders')
            ->with('success', 'Return/refund request updated.');
    }
}
