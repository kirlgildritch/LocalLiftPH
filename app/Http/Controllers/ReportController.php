<?php

namespace App\Http\Controllers;

use App\Http\Requests\Report\StoreReportRequest;
use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use App\Notifications\SellerNotificationService;
use App\Services\AdminActivityService;
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{
    public function store(StoreReportRequest $request, SellerNotificationService $sellerNotifications, AdminActivityService $adminActivity): RedirectResponse
    {
        $validated = $request->validated();
        $report = Report::create([
            'user_id' => (int) $request->user()->id,
            'product_id' => $request->integer('product_id') ?: null,
            'seller_id' => $request->integer('seller_id') ?: null,
            'reason' => $validated['reason'],
            'message' => trim((string) ($validated['message'] ?? '')) ?: null,
            'status' => Report::STATUS_PENDING,
        ]);

        $targetLabel = $report->product_id
            ? (Product::query()->find($report->product_id)?->name ?: 'a product')
            : (User::query()->find($report->seller_id)?->name ?: 'a seller');

        $adminActivity->reportSubmitted($request->user(), $targetLabel);

        if ($report->product_id) {
            $sellerNotifications->productReported($report->fresh(['product.user.sellerProfile', 'user']));
        }

        if ($report->seller_id) {
            $sellerNotifications->shopFlagged($report->fresh(['seller.sellerProfile', 'user']));
        }

        return back()->with('success', 'Your report has been submitted for review.');
    }
}
