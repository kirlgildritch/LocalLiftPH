<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $completedOrdersQuery = Order::query()
            ->where(function (Builder $query) {
                $this->applyCompletedOrderConstraints($query);
            })
            ->where(function (Builder $query) {
                $this->applyNotCancelledOrderConstraints($query);
            });

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

        $recentOrders = Order::with('user')
            ->latest()
            ->take(6)
            ->get();

        $totalSales = (clone $completedOrdersQuery)->sum('total_price');
        $todaySales = (clone $completedOrdersQuery)
            ->whereDate('updated_at', today())
            ->sum('total_price');
        $monthlySales = (clone $completedOrdersQuery)
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->sum('total_price');
        $completedOrdersCount = (clone $completedOrdersQuery)->count();
        $pendingReportsQuery = Report::query()->where('status', Report::STATUS_PENDING);
        $pendingReports = (clone $pendingReportsQuery)
            ->with('product:id,user_id')
            ->get(['id', 'seller_id', 'product_id']);
        $flaggedUserIds = $pendingReports
            ->map(fn (Report $report) => $report->seller_id ?: $report->product?->user_id)
            ->filter()
            ->unique()
            ->count();
        $reportedProductsCount = (clone $pendingReportsQuery)
            ->whereNotNull('product_id')
            ->distinct('product_id')
            ->count('product_id');

        $salesOverview = [
            ['label' => 'Total Sales', 'value' => $totalSales, 'note' => 'Completed only', 'tone' => 'primary', 'currency' => true],
            ['label' => 'Today Sales', 'value' => $todaySales, 'note' => 'Completed today', 'tone' => 'success', 'currency' => true],
            ['label' => 'Monthly Sales', 'value' => $monthlySales, 'note' => now()->format('F'), 'tone' => 'warning', 'currency' => true],
            ['label' => 'Completed Orders', 'value' => $completedOrdersCount, 'note' => 'Fulfilled orders', 'tone' => 'danger', 'currency' => false],
        ];

        $orderMonitoring = [
            ['label' => 'Pending', 'value' => Order::query()->where('shipping_status', Order::SHIPPING_PENDING)->count(), 'tone' => 'warning'],
            ['label' => 'To Ship', 'value' => Order::query()->where('shipping_status', Order::SHIPPING_TO_SHIP)->count(), 'tone' => 'primary'],
            ['label' => 'Completed', 'value' => $completedOrdersCount, 'tone' => 'success'],
            ['label' => 'Cancelled', 'value' => Order::query()->where(function (Builder $query) {
                $this->applyCancelledOrderConstraints($query);
            })->count(), 'tone' => 'danger'],
        ];

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
            ['label' => 'Reported', 'value' => $reportedProductsCount, 'tone' => 'primary'],
        ];

        $recentActivity = $this->buildRecentActivity(
            Seller::with('user')->latest('submitted_at')->take(4)->get(),
            Product::with('user')->latest()->take(4)->get(),
            (clone $completedOrdersQuery)->with('user')->latest('updated_at')->take(4)->get(),
            Report::with('product:id,name')->whereNotNull('product_id')->latest()->take(4)->get()
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

    protected function applyCompletedOrderConstraints(Builder $query): void
    {
        $query->where(function (Builder $completedQuery) {
            $completedQuery
                ->whereIn('shipping_status', [
                    Order::SHIPPING_COMPLETED,
                    Order::SHIPPING_OUT_FOR_DELIVERY,
                    Order::SHIPPING_DELIVERED,
                ])
                ->orWhere(function (Builder $legacyQuery) {
                    $legacyQuery
                        ->whereNull('shipping_status')
                        ->whereIn('status', [
                            Order::STATUS_COMPLETED,
                            Order::STATUS_DELIVERED,
                        ]);
                });
        });
    }

    protected function applyCancelledOrderConstraints(Builder $query): void
    {
        $query->where(function (Builder $cancelledQuery) {
            $cancelledQuery
                ->where('shipping_status', Order::SHIPPING_CANCELLED)
                ->orWhere(function (Builder $legacyQuery) {
                    $legacyQuery
                        ->whereNull('shipping_status')
                        ->where('status', Order::STATUS_CANCELLED);
                });
        });
    }

    protected function applyNotCancelledOrderConstraints(Builder $query): void
    {
        $query->where(function (Builder $notCancelledQuery) {
            $notCancelledQuery
                ->whereNull('shipping_status')
                ->where('status', '!=', Order::STATUS_CANCELLED);
        })->orWhere('shipping_status', '!=', Order::SHIPPING_CANCELLED);
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
