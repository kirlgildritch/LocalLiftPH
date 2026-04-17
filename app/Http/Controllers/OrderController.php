<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderCancellation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $currentStatus = trim((string) $request->get('status', 'all'));
        $allowedStatuses = [
            'all',
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
        ];

        if (!in_array($currentStatus, $allowedStatuses, true)) {
            $currentStatus = 'all';
        }

        $orders = Order::with(['items.product.user', 'cancellation'])
            ->where('user_id', Auth::id())
            ->when($currentStatus !== 'all', function ($query) use ($currentStatus) {
                $query->where('status', $currentStatus);
            })
            ->latest()
            ->get();

        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as count')
            ->where('user_id', Auth::id())
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('buyer.orders', compact('orders', 'currentStatus', 'statusCounts'));
    }

    public function show(Order $order)
    {
        abort_unless((int) $order->user_id === (int) Auth::id(), 403);

        $order->load(['items.product.user', 'cancellation']);

        return view('buyer.order-show', compact('order'));
    }

    public function buyAgain(Order $order)
    {
        abort_unless((int) $order->user_id === (int) Auth::id(), 403);

        $order->load(['items.product']);

        foreach ($order->items as $item) {
            if (!$item->product || !$item->product->is_active) {
                continue;
            }

            $cartItem = Cart::firstOrNew([
                'user_id' => Auth::id(),
                'product_id' => $item->product_id,
            ]);

            $cartItem->quantity = (int) ($cartItem->exists ? $cartItem->quantity : 0) + (int) $item->quantity;
            $cartItem->save();
        }

        return redirect()->route('cart.index')->with('success', 'Items added to cart again.');
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless((int) $order->user_id === (int) Auth::id(), 403);

        if (!$order->canBeCancelled()) {
            return redirect()
                ->route('buyer.orders.show', $order)
                ->with('error', 'Only orders before shipment can be cancelled.');
        }

        $validated = $request->validate([
            'reasons' => ['required', 'array', 'min:1'],
            'reasons.*' => ['string', 'in:Changed my mind,Item price too high,Found better price elsewhere,Item damaged / defective,Delivery delay,Other'],
            'other_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $selectedReasons = collect($validated['reasons'] ?? [])
            ->filter()
            ->unique()
            ->values();

        $otherReason = trim((string) ($validated['other_reason'] ?? ''));

        if ($selectedReasons->contains('Other') && $otherReason === '') {
            return redirect()
                ->route('buyer.orders.show', $order)
                ->withErrors(['other_reason' => 'Provide a custom reason when selecting Other.'])
                ->withInput();
        }

        if (!$selectedReasons->contains('Other')) {
            $otherReason = null;
        }

        OrderCancellation::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => Auth::id(),
                'reasons' => $selectedReasons->all(),
                'other_reason' => $otherReason,
                'status_before_cancellation' => $order->status,
            ]
        );

        $order->update([
            'status' => Order::STATUS_CANCELLED,
        ]);

        return redirect()
            ->route('buyer.orders.show', $order)
            ->with('success', 'Order cancelled successfully.');
    }
}
