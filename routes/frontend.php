<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductBrowseController;
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

Route::middleware('guest:web')->group(function () {
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});
