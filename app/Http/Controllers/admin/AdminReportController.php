<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportActionRequest;
use App\Models\Product;
use App\Models\Report;
use App\Models\Seller;
use App\Models\User;
use App\Notifications\SellerNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Report::class);
        $reports = Report::with([
            'user',
            'product.category',
            'product.reports',
            'product.user.sellerProfile',
            'seller.sellerProfile',
            'seller.products.category',
            'actions.admin',
        ])
            ->latest()
            ->get();

        return view('admin.reports', compact('reports'));
    }

    public function resolve(Report $report, SellerNotificationService $sellerNotifications): RedirectResponse
    {
        $this->authorize('resolve', $report);
        return $this->applyActionData([
            'action' => 'mark_resolved',
            'admin_notes' => null,
        ], $report, $sellerNotifications);
    }

    public function action(ReportActionRequest $request, Report $report, SellerNotificationService $sellerNotifications): RedirectResponse
    {
        $this->authorize('moderate', $report);
        return $this->applyActionData($request->validated(), $report, $sellerNotifications);
    }

    protected function applyActionData(array $validated, Report $report, SellerNotificationService $sellerNotifications): RedirectResponse
    {
        $report->loadMissing([
            'product.user.sellerProfile',
            'seller.sellerProfile',
            'actions.admin',
        ]);

        $admin = Auth::guard('admin')->user();
        $action = $validated['action'];
        $notes = trim((string) ($validated['admin_notes'] ?? ''));
        $sellerUser = $this->targetSellerUser($report);
        $sellerProfile = $sellerUser?->sellerProfile;
        $product = $report->product;

        DB::transaction(function () use ($report, $action, $notes, $admin, $sellerUser, $sellerProfile, $product, $sellerNotifications) {
            match ($action) {
                'warn_seller' => $this->warnSeller($sellerUser, $notes, $report, $sellerNotifications),
                'delist_product' => $this->delistProduct($product, $sellerUser, $notes, $report, $sellerNotifications),
                'ban_product' => $this->banProduct($product, $sellerUser, $notes, $report, $sellerNotifications),
                'suspend_seller' => $this->suspendSeller($sellerProfile, $sellerUser, $notes, $report, $sellerNotifications),
                'mark_resolved' => $report->update(['status' => Report::STATUS_RESOLVED]),
                'dismiss_report' => $this->dismissReport($report, $sellerUser, $notes, $sellerNotifications),
                default => null,
            };

            $report->actions()->create([
                'handled_by' => $admin?->id,
                'action' => $action,
                'admin_notes' => $notes !== '' ? $notes : null,
                'handled_at' => now(),
            ]);
        });

        return back()->with('success', 'Report action saved.');
    }

    protected function targetSellerUser(Report $report): ?User
    {
        return $report->seller ?: $report->product?->user;
    }

    protected function notifySeller(?User $seller, string $title, string $message, string $action, ?int $reportId, SellerNotificationService $sellerNotifications): void
    {
        if (! $seller) {
            return;
        }

        $sellerNotifications->adminViolation($seller, $title, $message, $action, $reportId);
    }

    protected function warnSeller(?User $seller, string $notes, Report $report, SellerNotificationService $sellerNotifications): void
    {
        $this->notifySeller(
            $seller,
            'Seller Warning',
            $notes !== '' ? $notes : 'A report on your account or listing has been reviewed and a warning was issued.',
            'warn_seller',
            $report->id,
            $sellerNotifications,
        );
    }

    protected function delistProduct(?Product $product, ?User $seller, string $notes, Report $report, SellerNotificationService $sellerNotifications): void
    {
        if ($product) {
            $product->update([
                'is_active' => 0,
            ]);
        }

        $this->notifySeller(
            $seller,
            'Product Delisted',
            $notes !== '' ? $notes : 'One of your products was hidden from buyers after a report review.',
            'delist_product',
            $report->id,
            $sellerNotifications,
        );
    }

    protected function banProduct(?Product $product, ?User $seller, string $notes, Report $report, SellerNotificationService $sellerNotifications): void
    {
        if ($product) {
            $product->update([
                'status' => Product::STATUS_REJECTED,
                'is_active' => 0,
                'rejection_reason' => $notes !== '' ? $notes : 'Violation from report moderation.',
            ]);
        }

        $this->notifySeller(
            $seller,
            'Product Removed',
            $notes !== '' ? $notes : 'One of your products was removed after a report review.',
            'ban_product',
            $report->id,
            $sellerNotifications,
        );
    }

    protected function suspendSeller(?Seller $sellerProfile, ?User $seller, string $notes, Report $report, SellerNotificationService $sellerNotifications): void
    {
        if ($sellerProfile) {
            $sellerProfile->update([
                'suspended_at' => now(),
                'suspension_reason' => $notes !== '' ? $notes : 'Seller account suspended after moderation review.',
            ]);
        }

        $this->notifySeller(
            $seller,
            'Seller Account Suspended',
            $notes !== '' ? $notes : 'Your seller account was suspended after a report review.',
            'suspend_seller',
            $report->id,
            $sellerNotifications,
        );
    }

    protected function dismissReport(Report $report, ?User $seller, string $notes, SellerNotificationService $sellerNotifications): void
    {
        $report->update([
            'status' => Report::STATUS_DISMISSED,
        ]);

        $this->notifySeller(
            $seller,
            'Report Dismissed',
            $notes !== '' ? $notes : 'A report connected to your account or listing was reviewed and dismissed.',
            'dismiss_report',
            $report->id,
            $sellerNotifications,
        );
    }
}
