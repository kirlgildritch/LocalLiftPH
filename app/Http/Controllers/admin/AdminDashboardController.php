<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['user', 'seller.sellerProfile', 'items.product.user.sellerProfile'])
            ->latest()
            ->get();

        $completedOrders = $orders->filter(fn (Order $order) => $order->isCompleted() && ! $order->isCancelled());
        $todayCompletedOrders = $completedOrders->filter(fn (Order $order) => $order->updated_at?->isToday());
        $monthlyCompletedOrders = $completedOrders->filter(
            fn (Order $order) => $order->updated_at?->isSameMonth(now())
        );

        $reports = Report::with(['product', 'seller.sellerProfile', 'user'])
            ->latest()
            ->get();

        $pendingSellers = Seller::with('user')
            ->where('application_status', Seller::STATUS_PENDING)
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $pendingProducts = Product::with(['user', 'category'])
            ->withCount([
                'reports as pending_reports_count' => fn ($query) => $query->where('status', Report::STATUS_PENDING),
            ])
            ->where('status', Product::STATUS_PENDING)
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = $orders->take(6);

        $salesOverview = [
            ['label' => 'Total Sales', 'value' => $completedOrders->sum('total_price'), 'note' => 'Completed only', 'tone' => 'primary', 'currency' => true],
            ['label' => 'Today Sales', 'value' => $todayCompletedOrders->sum('total_price'), 'note' => 'Completed today', 'tone' => 'success', 'currency' => true],
            ['label' => 'Monthly Sales', 'value' => $monthlyCompletedOrders->sum('total_price'), 'note' => now()->format('F'), 'tone' => 'warning', 'currency' => true],
            ['label' => 'Completed Orders', 'value' => $completedOrders->count(), 'note' => 'Fulfilled orders', 'tone' => 'danger', 'currency' => false],
        ];

        $orderMonitoring = [
            ['label' => 'Pending', 'value' => $orders->where('shipping_status', Order::SHIPPING_PENDING)->count(), 'tone' => 'warning'],
            ['label' => 'To Ship', 'value' => $orders->where('shipping_status', Order::SHIPPING_TO_SHIP)->count(), 'tone' => 'primary'],
            ['label' => 'Completed', 'value' => $orders->filter(fn (Order $order) => $order->isCompleted() && ! $order->isCancelled())->count(), 'tone' => 'success'],
            ['label' => 'Cancelled', 'value' => $orders->filter(fn (Order $order) => $order->isCancelled())->count(), 'tone' => 'danger'],
        ];

        $flaggedUserIds = $reports
            ->where('status', Report::STATUS_PENDING)
            ->map(fn (Report $report) => $report->seller_id ?: $report->product?->user_id)
            ->filter()
            ->unique()
            ->count();

        $userManagement = [
            ['label' => 'Total Buyers', 'value' => User::query()->where('is_admin', false)->where('is_seller', false)->count(), 'tone' => 'primary'],
            ['label' => 'Approved Sellers', 'value' => Seller::query()->where('application_status', Seller::STATUS_APPROVED)->count(), 'tone' => 'success'],
            ['label' => 'Pending Sellers', 'value' => Seller::query()->where('application_status', Seller::STATUS_PENDING)->count(), 'tone' => 'warning'],
            ['label' => 'Flagged Users', 'value' => $flaggedUserIds, 'tone' => 'danger'],
        ];

        $productModeration = [
            ['label' => 'Pending', 'value' => Product::query()->where('status', Product::STATUS_PENDING)->count(), 'tone' => 'warning'],
            ['label' => 'Approved', 'value' => Product::query()->where('status', Product::STATUS_APPROVED)->count(), 'tone' => 'success'],
            ['label' => 'Rejected', 'value' => Product::query()->where('status', Product::STATUS_REJECTED)->count(), 'tone' => 'danger'],
            ['label' => 'Reported', 'value' => $reports->where('status', Report::STATUS_PENDING)->pluck('product_id')->filter()->unique()->count(), 'tone' => 'primary'],
        ];

        $recentActivity = $this->buildRecentActivity(
            Seller::with('user')->latest('submitted_at')->take(4)->get(),
            Product::with('user')->latest()->take(4)->get(),
            $completedOrders->sortByDesc('updated_at')->take(4)->values(),
            $reports->whereNotNull('product_id')->take(4)->values()
        )->take(10)->values();

        $stats = [
            ['label' => 'Pending Products', 'value' => $productModeration[0]['value'], 'note' => 'Awaiting approval', 'tone' => 'primary'],
            ['label' => 'Pending Sellers', 'value' => $userManagement[2]['value'], 'note' => 'Verification queue', 'tone' => 'warning'],
            ['label' => 'Reported Products', 'value' => $productModeration[3]['value'], 'note' => 'Need moderation', 'tone' => 'danger'],
            ['label' => 'Today Sales', 'value' => $salesOverview[1]['value'], 'note' => 'Completed today', 'tone' => 'success', 'currency' => true],
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'salesOverview' => $salesOverview,
            'orderMonitoring' => $orderMonitoring,
            'userManagement' => $userManagement,
            'productModeration' => $productModeration,
            'pendingProducts' => $pendingProducts,
            'pendingSellers' => $pendingSellers,
            'recentOrders' => $recentOrders,
            'recentActivity' => $recentActivity,
        ]);
    }

    protected function buildRecentActivity(Collection $sellers, Collection $products, Collection $completedOrders, Collection $reports): Collection
    {
        $sellerActivity = $sellers->map(function (Seller $seller) {
            $name = $seller->store_name ?: ($seller->full_name ?? $seller->user?->name ?? 'Seller');

            return [
                'type' => 'New seller',
                'title' => $name,
                'meta' => 'Shop verification submitted',
                'time' => $seller->submitted_at ?? $seller->created_at,
                'tone' => 'warning',
                'action_label' => 'Review',
                'action_url' => route('admin.sellers'),
            ];
        });

        $productActivity = $products->map(function (Product $product) {
            return [
                'type' => 'Product submitted',
                'title' => $product->name,
                'meta' => $product->user?->name ?? 'Seller',
                'time' => $product->created_at,
                'tone' => 'primary',
                'action_label' => 'Moderate',
                'action_url' => route('admin.products'),
            ];
        });

        $orderActivity = $completedOrders->map(function (Order $order) {
            return [
                'type' => 'Order completed',
                'title' => 'Order #' . $order->id,
                'meta' => $order->user?->name ?? 'Buyer',
                'time' => $order->updated_at ?? $order->created_at,
                'tone' => 'success',
                'action_label' => 'View Orders',
                'action_url' => route('admin.orders'),
            ];
        });

        $reportActivity = $reports->map(function (Report $report) {
            return [
                'type' => 'Product reported',
                'title' => $report->product?->name ?? 'Reported product',
                'meta' => $report->reasonLabel(),
                'time' => $report->created_at,
                'tone' => 'danger',
                'action_label' => 'Inspect',
                'action_url' => route('admin.reports'),
            ];
        });

        return $sellerActivity
            ->concat($productActivity)
            ->concat($orderActivity)
            ->concat($reportActivity)
            ->sortByDesc('time')
            ->values();
    }
}
