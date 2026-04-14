<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EarningsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\Seller\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/shops', function () {
    return view('shops.index');
})->name('shops.index');

Route::get('/shops/show', function () {
    return view('shops.show');
})->name('shops.show');

Route::get('/products', function () {
    return view('products.index');
})->name('products.index');

Route::get('/products/show', function () {
    return view('products.show');
})->name('products.show');

Route::get('/cart', function () {
    return view('cart.index');
})->name('cart.index');

Route::get('/checkout', function () {
    return view('checkout.index');
})->name('checkout.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'seller'])->group(function () {
    Route::get('/seller-dashboard', function () {
        return view('seller.dashboard');
    })->name('seller.dashboard');

    Route::get('/add-product', [ProductController::class, 'create'])->name('seller.products.create');
    Route::post('/add-product', [ProductController::class, 'store'])->name('seller.products.store');

    Route::get('/manage-products', [ProductController::class, 'index'])->name('seller.products.index');

     Route::get('/seller-orders', [OrderController::class, 'index'])->name('seller.orders');

      Route::get('/seller-earnings', [EarningsController::class, 'index'])->name('seller.earnings');

    Route::get('/seller-messages', [MessageController::class, 'index'])->name('seller.messages');

    Route::get('/seller-settings', [SettingsController::class, 'index'])->name('seller.settings');
    
    Route::post('/seller-settings', [SettingsController::class, 'update'])->name('seller.settings.update');

    Route::get('/seller-profile', [ProfileController::class, 'edit'])->name('seller.profile');
    Route::patch('/seller-profile', [ProfileController::class, 'update'])->name('seller.profile.update');

    Route::delete('/seller-profile', [ProfileController::class, 'destroy'])->name('seller.profile.destroy');

    Route::get('/seller-settings', [SettingsController::class, 'index'])->name('seller.settings');
    Route::patch('/seller-settings', [SettingsController::class, 'update'])->name('seller.settings.update');
    Route::patch('/seller-settings/notifications', [SettingsController::class, 'updateNotifications'])->name('seller.settings.notifications');
    Route::patch('/seller-settings/policies', [SettingsController::class, 'updatePolicies'])->name('seller.settings.policies');
    Route::patch('/seller-settings/payout', [SettingsController::class, 'updatePayout'])->name('seller.settings.payout');
    Route::patch('/seller-settings/inventory', [SettingsController::class, 'updateInventory'])->name('seller.settings.inventory');
    Route::patch('/seller-settings/status', [SettingsController::class, 'updateStatus'])->name('seller.settings.status');

    Route::get('/seller-shop-preview', [SettingsController::class, 'preview'])->name('seller.shop.preview');
});



Route::middleware('auth')->group(function () {
    Route::get('/become-seller', [SellerController::class, 'create'])->name('seller.setup');
    Route::post('/become-seller', [SellerController::class, 'store'])->name('seller.store');
});

require __DIR__.'/auth.php';