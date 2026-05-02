<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EarningsController extends Controller
{
    protected function sellerOrderTotal(Order $order): float
    {
        return (float) ($order->total_price ?? ($order->subtotalAmount() + (float) $order->shipping_fee));
    }

    protected function orderProductSummary(Order $order): string
    {
        $items = $order->relationLoaded('items') ? $order->items : $order->items()->with('product')->get();
        $names = $items->pluck('product.name')->filter()->values();

        if ($names->isEmpty()) {
            return 'Product unavailable';
        }

        if ($names->count() === 1) {
            return (string) $names->first();
        }

        return $names->first() . ' +' . ($names->count() - 1) . ' more';
    }

    protected function parseMonthRange(?string $month): ?array
    {
        if (! filled($month)) {
            return null;
        }

        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            return null;
        }

        return [$start, (clone $start)->endOfMonth()];
    }

    public function index(Request $request): View
    {
        $sellerId = (int) (Auth::guard('seller')->id() ?? Auth::id());
        $status = (string) $request->string('status', 'all');
        $from = $request->input('from');
        $to = $request->input('to');
        $month = $request->input('month');

        $allowedStatuses = [
            'all',
            Order::SHIPPING_PENDING,
            Order::SHIPPING_TO_SHIP,
            Order::SHIPPING_SHIPPED,
            Order::SHIPPING_COMPLETED,
            Order::SHIPPING_CANCELLED,
        ];

        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $baseOrderQuery = Order::with(['user', 'items.product'])
            ->where('seller_id', $sellerId)
            ->latest();

        $allOrders = (clone $baseOrderQuery)->get();
        $completedOrders = $allOrders->filter(fn (Order $order) => $order->isCompleted() && ! $order->isCancelled());
        $pendingOrders = $allOrders->filter(fn (Order $order) => ! $order->isCompleted() && ! $order->isCancelled());

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $completedWithinRange = fn (Carbon $start, Carbon $end) => $completedOrders
            ->filter(fn (Order $order) => $order->updated_at && $order->updated_at->betweenIncluded($start, $end))
            ->sum(fn (Order $order) => $this->sellerOrderTotal($order));

        $monthRange = $this->parseMonthRange($month);

        $historyOrders = (clone $baseOrderQuery)
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('shipping_status', $status);
            })
            ->when($monthRange, function ($query) use ($monthRange) {
                $query->whereBetween('created_at', $monthRange);
            })
            ->when(filled($from), function ($query) use ($from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when(filled($to), function ($query) use ($to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->get()
            ->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'buyer_name' => $order->user?->name ?? 'Buyer',
                    'product_summary' => $this->orderProductSummary($order),
                    'quantity' => $order->itemCount(),
                    'total' => $this->sellerOrderTotal($order),
                    'status_label' => $order->shippingStatusLabel(),
                    'status_tone' => $order->shippingToneClass(),
                    'date_label' => $order->created_at?->format('M d, Y h:i A') ?? 'N/A',
                    'is_cancelled' => $order->isCancelled(),
                ];
            });

        $stats = [
            'total_earnings' => (float) $completedOrders->sum(fn (Order $order) => $this->sellerOrderTotal($order)),
            'pending_earnings' => (float) $pendingOrders->sum(fn (Order $order) => $this->sellerOrderTotal($order)),
            'today_earnings' => (float) $completedWithinRange($todayStart, $todayEnd),
            'weekly_earnings' => (float) $completedWithinRange($weekStart, $weekEnd),
            'monthly_earnings' => (float) $completedWithinRange($monthStart, $monthEnd),
            'overall_earnings' => (float) $allOrders
                ->reject(fn (Order $order) => $order->isCancelled())
                ->sum(fn (Order $order) => $this->sellerOrderTotal($order)),
        ];

        return view('seller.earnings', [
            'stats' => $stats,
            'historyOrders' => $historyOrders,
            'filters' => [
                'status' => $status,
                'from' => $from,
                'to' => $to,
                'month' => $month,
            ],
            'statusOptions' => [
                'all' => 'All Statuses',
                Order::SHIPPING_PENDING => 'Pending',
                Order::SHIPPING_TO_SHIP => 'To Ship',
                Order::SHIPPING_SHIPPED => 'Shipped',
                Order::SHIPPING_COMPLETED => 'Completed',
                Order::SHIPPING_CANCELLED => 'Cancelled',
            ],
        ]);
    }
}
