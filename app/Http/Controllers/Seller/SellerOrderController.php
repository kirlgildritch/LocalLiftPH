<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class SellerOrderController extends Controller
{
    public function index(): View
    {
        $seller = Auth::guard('seller')->user();

        $orders = Order::with(['user', 'items.product'])
            ->whereHas('items.product', function ($query) use ($seller) {
                $query->where('user_id', $seller->id);
            })
            ->latest()
            ->get();

        return view('seller.orders', compact('orders'));
    }

    public function updateShippingStatus(Request $request, Order $order): RedirectResponse
    {
        $order->loadMissing(['items.product', 'user']);

        $this->authorize('updateShippingStatus', $order);

        $allowedStatuses = $order->nextShippingStatuses();

        if ($allowedStatuses === []) {
            return redirect()
                ->route('seller.orders')
                ->with('error', 'This order can no longer be updated.');
        }

        $validated = $request->validate([
            'shipping_status' => ['required', 'string', Rule::in($allowedStatuses)],
        ], [
            'shipping_status.in' => 'Invalid shipping status transition.',
        ]);

        $order->update([
            'shipping_status' => $validated['shipping_status'],
            'status' => Order::legacyStatusForShipping($validated['shipping_status']),
        ]);

        return redirect()
            ->route('seller.orders')
            ->with('success', 'Shipping status updated successfully.');
    }
}
