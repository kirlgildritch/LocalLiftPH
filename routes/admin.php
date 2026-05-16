<?php

use App\Http\Controllers\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPayoutController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\ProductApprovalController;
use App\Http\Controllers\Admin\SellerReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.store');
    });

    Route::middleware('admin')->post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/feed', [AdminNotificationController::class, 'feed'])->name('notifications.feed');
    Route::patch('/notifications/read-all', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/clear-read', [AdminNotificationController::class, 'clearRead'])->name('notifications.clear-read');
    Route::patch('/notifications/{notification}/read', [AdminNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/{notification}/open', [AdminNotificationController::class, 'open'])->name('notifications.open');

    Route::get('/products', [ProductApprovalController::class, 'index'])->name('products');
    Route::patch('/products/bulk', [ProductApprovalController::class, 'bulkUpdate'])->name('products.bulk');
    Route::patch('/products/{product}/approve', [ProductApprovalController::class, 'approve'])->name('products.approve');
    Route::patch('/products/{product}/reject', [ProductApprovalController::class, 'reject'])->name('products.reject');

    Route::get('/sellers', [SellerReviewController::class, 'index'])->name('sellers');
    Route::get('/sellers/{seller}/documents/{type}', [SellerReviewController::class, 'document'])->name('sellers.documents.show');
    Route::patch('/sellers/{seller}/status', [SellerReviewController::class, 'updateStatus'])->name('sellers.status');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');

    Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts');
    Route::patch('/payouts/{payout}/paid', [AdminPayoutController::class, 'markPaid'])->name('payouts.paid');
    Route::patch('/payouts/{payout}/reject', [AdminPayoutController::class, 'reject'])->name('payouts.reject');

    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
    Route::patch('/reports/{report}/action', [AdminReportController::class, 'action'])->name('reports.action');
    Route::patch('/reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
});
