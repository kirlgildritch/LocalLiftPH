<?php

use App\Http\Controllers\Buyer\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderReturnRequestController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware('buyer')->group(function () {
    Route::get('/profile', [ProfileController::class, 'buyerEdit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'buyerUpdate'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
    Route::patch('/my-orders/{order}/received', [OrderController::class, 'confirmReceived'])->name('buyer.orders.received');
    Route::post('/my-orders/{order}/return-request', [OrderReturnRequestController::class, 'store'])->name('buyer.orders.return-request');
    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('products.reviews.store');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('buyer.wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'store'])->name('buyer.wishlist.store');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('buyer.wishlist.destroy');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/start/{seller}', [MessageController::class, 'start'])->name('messages.start');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/{conversation}/typing', [MessageController::class, 'typing'])->name('messages.typing');
    Route::get('/chat/widget', [MessageController::class, 'widget'])->name('chat.widget');

    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});
