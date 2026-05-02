<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SellerOrderController extends Controller
{
    public function index(): View
    {
        $seller = Auth::guard('seller')->user();

        $orders = Order::with(['user', 'seller.sellerProfile', 'items.product'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

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

        $shippingStatus = $validated['shipping_status'];

        $updates = [
            'shipping_status' => $shippingStatus,
            'status' => Order::legacyStatusForShipping($shippingStatus),
        ];

        if ($shippingStatus === Order::SHIPPING_SHIPPED) {
            $updates['seller_earning_status'] = Order::EARNING_ON_HOLD;
        }

        if ($shippingStatus === Order::SHIPPING_CANCELLED) {
            $updates['payment_status'] = Order::PAYMENT_CANCELLED;
            $updates['seller_earning_status'] = Order::EARNING_REVERSED;
        }

        $order->update($updates);

        return redirect()
            ->route('seller.orders')
            ->with('success', 'Shipping status updated successfully.');
    }
}