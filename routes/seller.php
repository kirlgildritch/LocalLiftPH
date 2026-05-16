<?php

use App\Http\Controllers\EarningsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\OrderReturnRequestController as SellerOrderReturnRequestController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\SellerAuthenticatedSessionController;
use App\Http\Controllers\Seller\SellerCenterEntryController;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\SellerGoogleAuthController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerPayoutController;
use App\Http\Controllers\Seller\SellerRegisteredUserController;
use App\Http\Controllers\Seller\SellerSearchController;
use App\Http\Controllers\Seller\SellerVoucherController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SellerNotificationController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('seller-center')->name('seller.')->group(function () {
    Route::middleware('guest:seller')->group(function () {
        Route::get('/login', [SellerAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [SellerAuthenticatedSessionController::class, 'store'])->name('login.store');
        Route::get('/register', [SellerRegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [SellerRegisteredUserController::class, 'store'])->name('register.store');

        Route::get('/auth/google', [SellerGoogleAuthController::class, 'redirect'])->name('google.login');
        Route::get('/auth/google/callback', [SellerGoogleAuthController::class, 'callback'])->name('google.callback');
    });

    Route::middleware('seller')->group(function () {
        Route::post('/logout', [SellerAuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('/setup', [SellerController::class, 'create'])->name('setup');
        Route::post('/setup', [SellerController::class, 'store'])->name('setup.store');
    });
});

Route::get('/become-seller', SellerCenterEntryController::class)
    ->middleware('frontend')
    ->name('seller.center');

Route::middleware('seller')->group(function () {
    Route::get('/seller-dashboard', [SellerDashboardController::class, 'show'])->name('seller.dashboard');
    Route::post('/seller-dashboard/application', [SellerDashboardController::class, 'submitApplication'])->name('seller.dashboard.application.store');

    Route::get('/add-product', [ProductController::class, 'create'])->name('seller.products.create');
    Route::post('/add-product', [ProductController::class, 'store'])->name('seller.products.store');
    Route::get('/manage-products', [ProductController::class, 'index'])->name('seller.products.index');
    Route::get('/seller/products/{product}/edit', [ProductController::class, 'edit'])->name('seller.products.edit');
    Route::patch('/seller/products/{product}', [ProductController::class, 'update'])->name('seller.products.update');
    Route::delete('/seller/products/{product}/media', [ProductController::class, 'destroyMedia'])->name('seller.products.media.destroy');
    Route::delete('/seller/products/{product}', [ProductController::class, 'destroy'])->name('seller.products.destroy');
    Route::get('/seller/products/{product}/reviews', [ProductController::class, 'reviews'])->name('seller.products.reviews');
    Route::patch('/seller/products/{product}/reviews/{review}/reply', [ProductController::class, 'replyToReview'])->name('seller.products.reviews.reply');

    Route::get('/seller-orders', [SellerOrderController::class, 'index'])->name('seller.orders');
    Route::patch('/seller-orders/{order}/shipping-status', [SellerOrderController::class, 'updateShippingStatus'])->name('seller.orders.shipping-status');
    Route::patch('/seller-return-requests/{returnRequest}', [SellerOrderReturnRequestController::class, 'update'])->name('seller.return-requests.update');
    Route::get('/seller-earnings', [EarningsController::class, 'index'])->name('seller.earnings');
    Route::get('/seller-vouchers', [SellerVoucherController::class, 'index'])->name('seller.vouchers.index');
    Route::post('/seller-vouchers', [SellerVoucherController::class, 'store'])->name('seller.vouchers.store');
    Route::get('/seller-vouchers/{voucher}/edit', [SellerVoucherController::class, 'edit'])->name('seller.vouchers.edit');
    Route::patch('/seller-vouchers/{voucher}', [SellerVoucherController::class, 'update'])->name('seller.vouchers.update');
    Route::delete('/seller-vouchers/{voucher}', [SellerVoucherController::class, 'destroy'])->name('seller.vouchers.destroy');
    Route::post('/seller-payouts', [SellerPayoutController::class, 'store'])->name('seller.payouts.store');
    Route::get('/seller-search', [SellerSearchController::class, 'index'])->name('seller.search');
    Route::get('/seller-search/suggestions', [SellerSearchController::class, 'suggestions'])->name('seller.search.suggestions');

    Route::get('/seller-messages', [MessageController::class, 'index'])->name('seller.messages');
    Route::get('/seller-messages/{conversation}', [MessageController::class, 'show'])->name('seller.messages.show');
    Route::post('/seller-messages/{conversation}', [MessageController::class, 'store'])->name('seller.messages.store');
    Route::post('/seller-messages/{conversation}/typing', [MessageController::class, 'typing'])->name('seller.messages.typing');
    Route::get('/seller-chat/widget', [MessageController::class, 'widget'])->name('seller.chat.widget');

    Route::get('/seller-notifications', [SellerNotificationController::class, 'index'])->name('seller.notifications.index');
    Route::get('/seller-notifications/feed', [SellerNotificationController::class, 'feed'])->name('seller.notifications.feed');
    Route::patch('/seller-notifications/read-all', [SellerNotificationController::class, 'markAllAsRead'])->name('seller.notifications.read-all');
    Route::delete('/seller-notifications/clear-read', [SellerNotificationController::class, 'clearRead'])->name('seller.notifications.clear-read');
    Route::patch('/seller-notifications/{notification}/read', [SellerNotificationController::class, 'markAsRead'])->name('seller.notifications.read');
    Route::delete('/seller-notifications/{notification}', [SellerNotificationController::class, 'destroy'])->name('seller.notifications.destroy');
    Route::get('/seller-notifications/{notification}/open', [SellerNotificationController::class, 'open'])->name('seller.notifications.open');

    Route::get('/seller-settings', [SettingsController::class, 'index'])->name('seller.settings');
    Route::patch('/seller-settings', [SettingsController::class, 'update'])->name('seller.settings.update');
    Route::patch('/seller-settings/payout', [SettingsController::class, 'updatePayout'])->name('seller.settings.payout');
    Route::patch('/seller-settings/inventory', [SettingsController::class, 'updateInventory'])->name('seller.settings.inventory');
    Route::patch('/seller-settings/status', [SettingsController::class, 'updateStatus'])->name('seller.settings.status');

    Route::get('/seller-profile', [ProfileController::class, 'edit'])->name('seller.profile');
    Route::patch('/seller-profile', [ProfileController::class, 'update'])->name('seller.profile.update');
    Route::delete('/seller-profile', [ProfileController::class, 'destroy'])->name('seller.profile.destroy');

    Route::get('/seller-shop-preview', [SettingsController::class, 'preview'])->name('seller.shop.preview');
});
