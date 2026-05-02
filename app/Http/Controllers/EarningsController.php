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
        if (!filled($month)) {
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
        $earningStatus = (string) $request->string('status', 'all');
        $from = $request->input('from');
        $to = $request->input('to');
        $month = $request->input('month');

        $allowedStatuses = [
            'all',
            Order::EARNING_PENDING,
            Order::EARNING_ON_HOLD,
            Order::EARNING_AVAILABLE,
            Order::EARNING_PAID_OUT,
            Order::EARNING_REVERSED,
        ];

        if (!in_array($earningStatus, $allowedStatuses, true)) {
            $earningStatus = 'all';
        }

        $baseOrderQuery = Order::with(['user', 'items.product'])
            ->where('seller_id', $sellerId)
            ->latest();

        $allOrders = (clone $baseOrderQuery)->get();

        $pendingOrders = $allOrders->filter(fn(Order $order) => in_array($order->seller_earning_status, [
            Order::EARNING_PENDING,
            Order::EARNING_ON_HOLD,
        ], true));

        $availableOrders = $allOrders->filter(fn(Order $order) => $order->seller_earning_status === Order::EARNING_AVAILABLE);
        $paidOutOrders = $allOrders->filter(fn(Order $order) => $order->seller_earning_status === Order::EARNING_PAID_OUT);
        $reversedOrders = $allOrders->filter(fn(Order $order) => $order->seller_earning_status === Order::EARNING_REVERSED);

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $availableWithinRange = fn(Carbon $start, Carbon $end) => $availableOrders
            ->filter(fn(Order $order) => $order->paid_at && $order->paid_at->betweenIncluded($start, $end))
            ->sum(fn(Order $order) => $this->sellerOrderTotal($order));

        $monthRange = $this->parseMonthRange($month);

        $historyOrders = (clone $baseOrderQuery)
            ->when($earningStatus !== 'all', function ($query) use ($earningStatus) {
                $query->where('seller_earning_status', $earningStatus);
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
                    'shipping_status_label' => $order->shippingStatusLabel(),
                    'shipping_status_tone' => $order->shippingToneClass(),
                    'payment_status_label' => $order->paymentStatusLabel(),
                    'payment_status_tone' => $order->paymentToneClass(),
                    'earning_status_label' => $order->earningStatusLabel(),
                    'earning_status_tone' => $order->earningToneClass(),
                    'date_label' => $order->created_at?->format('M d, Y h:i A') ?? 'N/A',
                    'is_reversed' => $order->seller_earning_status === Order::EARNING_REVERSED,
                ];
            });

        $stats = [
            'pending_earnings' => (float) $pendingOrders->sum(fn(Order $order) => $this->sellerOrderTotal($order)),
            'available_earnings' => (float) $availableOrders->sum(fn(Order $order) => $this->sellerOrderTotal($order)),
            'paid_out_earnings' => (float) $paidOutOrders->sum(fn(Order $order) => $this->sellerOrderTotal($order)),
            'reversed_earnings' => (float) $reversedOrders->sum(fn(Order $order) => $this->sellerOrderTotal($order)),
            'today_earnings' => (float) $availableWithinRange($todayStart, $todayEnd),
            'weekly_earnings' => (float) $availableWithinRange($weekStart, $weekEnd),
            'monthly_earnings' => (float) $availableWithinRange($monthStart, $monthEnd),
            'overall_earnings' => (float) $allOrders
                ->filter(fn(Order $order) => in_array($order->seller_earning_status, [
                    Order::EARNING_AVAILABLE,
                    Order::EARNING_PAID_OUT,
                ], true))
                ->sum(fn(Order $order) => $this->sellerOrderTotal($order)),
        ];

        return view('seller.earnings', [
            'stats' => $stats,
            'historyOrders' => $historyOrders,
            'filters' => [
                'status' => $earningStatus,
                'from' => $from,
                'to' => $to,
                'month' => $month,
            ],
            'statusOptions' => [
                'all' => 'All Earning Statuses',
                Order::EARNING_PENDING => 'Pending',
                Order::EARNING_ON_HOLD => 'On Hold',
                Order::EARNING_AVAILABLE => 'Available',
                Order::EARNING_PAID_OUT => 'Paid Out',
                Order::EARNING_REVERSED => 'Reversed',
            ],
        ]);
    }
}