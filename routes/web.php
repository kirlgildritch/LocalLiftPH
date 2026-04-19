<?php

use App\Http\Controllers\Buyer\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EarningsController;
use App\Http\Controllers\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductBrowseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::middleware('frontend')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
    Route::get('/shops/{user}', [ShopController::class, 'show'])->name('shops.show');

    Route::get('/products/suggestions', [ProductBrowseController::class, 'suggestions'])->name('products.suggestions');
    Route::get('/products', [ProductBrowseController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductBrowseController::class, 'show'])->name('products.show');

    Route::view('/about', 'about')->name('about');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->isSeller()) {
        return redirect()->route('seller.dashboard');
    }

    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'seller'])->group(function () {
    Route::get('/seller-dashboard', function () {
        return view('seller.dashboard');
    })->name('seller.dashboard');

    Route::get('/add-product', [ProductController::class, 'create'])->name('seller.products.create');
    Route::post('/add-product', [ProductController::class, 'store'])->name('seller.products.store');
    Route::get('/manage-products', [ProductController::class, 'index'])->name('seller.products.index');

    Route::get('/seller-orders', [SellerOrderController::class, 'index'])->name('seller.orders');
    Route::get('/seller-earnings', [EarningsController::class, 'index'])->name('seller.earnings');
    Route::get('/seller-messages', [MessageController::class, 'index'])->name('seller.messages');

    Route::get('/seller-settings', [SettingsController::class, 'index'])->name('seller.settings');
    Route::patch('/seller-settings', [SettingsController::class, 'update'])->name('seller.settings.update');
    Route::patch('/seller-settings/notifications', [SettingsController::class, 'updateNotifications'])->name('seller.settings.notifications');
    Route::patch('/seller-settings/policies', [SettingsController::class, 'updatePolicies'])->name('seller.settings.policies');
    Route::patch('/seller-settings/payout', [SettingsController::class, 'updatePayout'])->name('seller.settings.payout');
    Route::patch('/seller-settings/inventory', [SettingsController::class, 'updateInventory'])->name('seller.settings.inventory');
    Route::patch('/seller-settings/status', [SettingsController::class, 'updateStatus'])->name('seller.settings.status');

    Route::get('/seller-profile', [ProfileController::class, 'edit'])->name('seller.profile');
    Route::patch('/seller-profile', [ProfileController::class, 'update'])->name('seller.profile.update');
    Route::delete('/seller-profile', [ProfileController::class, 'destroy'])->name('seller.profile.destroy');

    Route::get('/seller-shop-preview', [SettingsController::class, 'preview'])->name('seller.shop.preview');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.store');
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::view('/products', 'admin.products')->name('products');
    Route::view('/sellers', 'admin.sellers')->name('sellers');
    Route::view('/orders', 'admin.orders')->name('orders');
    Route::view('/reports', 'admin.reports')->name('reports');
});
Route::middleware(['auth', 'buyer'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{productId}', [CartController::class, 'store'])->name('cart.add');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'destroy'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/buyer-profile', [ProfileController::class, 'buyerEdit'])->name('buyer.profile');
    Route::patch('/buyer-profile', [ProfileController::class, 'buyerUpdate'])->name('buyer.profile.update');

    Route::get('/my-addresses', [AddressController::class, 'index'])->name('buyer.addresses');
    Route::get('/my-addresses/create', [AddressController::class, 'create'])->name('buyer.addresses.create');
    Route::post('/my-addresses', [AddressController::class, 'store'])->name('buyer.addresses.store');
    Route::patch('/my-addresses/{address}', [AddressController::class, 'update'])->name('buyer.addresses.update');
    Route::delete('/my-addresses/{address}', [AddressController::class, 'destroy'])->name('buyer.addresses.destroy');
    Route::patch('/my-addresses/{address}/default', [AddressController::class, 'setDefault'])->name('buyer.addresses.default');

    Route::get('/my-orders', [OrderController::class, 'index'])->name('buyer.orders');
    Route::get('/my-orders/{order}', [OrderController::class, 'show'])->name('buyer.orders.show');
    Route::post('/my-orders/{order}/buy-again', [OrderController::class, 'buyAgain'])->name('buyer.orders.buyAgain');
    Route::patch('/my-orders/{order}/cancel', [OrderController::class, 'cancel'])->name('buyer.orders.cancel');
});

Route::middleware(['auth', 'frontend'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/start/{seller}', [MessageController::class, 'start'])->name('messages.start');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');

    Route::get('/become-seller', [SellerController::class, 'create'])->name('seller.setup');
    Route::post('/become-seller', [SellerController::class, 'store'])->name('seller.store');
});

require __DIR__ . '/auth.php';


