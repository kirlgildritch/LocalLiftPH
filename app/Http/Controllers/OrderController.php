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
            Order::SHIPPING_PENDING,
            Order::SHIPPING_TO_SHIP,
            Order::SHIPPING_SHIPPED,
            Order::SHIPPING_COMPLETED,
            Order::SHIPPING_CANCELLED,
        ];

        if (!in_array($currentStatus, $allowedStatuses, true)) {
            $currentStatus = 'all';
        }

        $orders = Order::with(['seller.sellerProfile', 'items.product.user.sellerProfile', 'items.review', 'cancellation'])
            ->where('user_id', Auth::id())
            ->when($currentStatus !== 'all', function ($query) use ($currentStatus) {
                $query->where('shipping_status', $currentStatus);
            })
            ->latest()
            ->get();

        $statusCounts = Order::query()
            ->selectRaw('shipping_status, COUNT(*) as count')
            ->where('user_id', Auth::id())
            ->groupBy('shipping_status')
            ->pluck('count', 'shipping_status');

        $checkoutGroupCounts = Order::query()
            ->where('user_id', Auth::id())
            ->get(['id', 'checkout_group'])
            ->groupBy(fn(Order $order) => $order->checkoutGroupKey())
            ->map(fn($group) => $group->count());

        return view('buyer.orders', compact('orders', 'currentStatus', 'statusCounts', 'checkoutGroupCounts'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $groupOrdersQuery = Order::with(['seller.sellerProfile', 'items.product.user.sellerProfile', 'items.review', 'cancellation'])
            ->where('user_id', Auth::id());

        if ($order->checkout_group) {
            $groupOrdersQuery->where('checkout_group', $order->checkout_group);
        } else {
            $groupOrdersQuery->where('id', $order->id);
        }

        $groupOrders = $groupOrdersQuery
            ->latest('id')
            ->get();

        $groupSummary = [
            'items' => (int) $groupOrders->sum(fn(Order $groupOrder) => $groupOrder->itemCount()),
            'subtotal' => (float) $groupOrders->sum(fn(Order $groupOrder) => $groupOrder->subtotalAmount()),
            'shipping' => (float) $groupOrders->sum(fn(Order $groupOrder) => (float) $groupOrder->shipping_fee),
            'total' => (float) $groupOrders->sum(fn(Order $groupOrder) => (float) $groupOrder->total_price),
            'shops' => (int) $groupOrders->count(),
        ];

        return view('buyer.order-show', [
            'order' => $order,
            'groupOrders' => $groupOrders,
            'groupSummary' => $groupSummary,
        ]);
    }

    public function buyAgain(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['items.product']);

        foreach ($order->items as $item) {
            if (!$item->product || !$item->product->is_active) {
                continue;
            }

            if ((int) $item->product->user_id === (int) Auth::id()) {
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
        $this->authorize('view', $order);

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
                'status_before_cancellation' => $order->shippingStatus(),
            ]
        );

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'shipping_status' => Order::SHIPPING_CANCELLED,
            'payment_status' => Order::PAYMENT_CANCELLED,
            'seller_earning_status' => Order::EARNING_REVERSED,
        ]);

        return redirect()
            ->route('buyer.orders.show', $order)
            ->with('success', 'Order cancelled successfully.');
    }

    public function confirmReceived(Order $order)
    {
        $this->authorize('view', $order);

        if (!$order->canConfirmReceipt()) {
            return redirect()
                ->route('buyer.orders.show', $order)
                ->with('error', 'This order is not ready for receipt confirmation.');
        }

        $order->update([
            'status' => Order::STATUS_COMPLETED,
            'shipping_status' => Order::SHIPPING_COMPLETED,
            'payment_status' => Order::PAYMENT_PAID,
            'paid_at' => now(),
            'seller_earning_status' => Order::EARNING_AVAILABLE,
        ]);

        return redirect()
            ->route('buyer.orders.show', $order)
            ->with('success', 'Order marked as received successfully.');
    }
}