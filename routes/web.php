<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/shops', function () {
    return view('shops.index');
})->name('shops.index');

/*Route::get('/shops/{id}', function ($id) {
    return view('shops.show');
})->name('shops.show');
*/
Route::get('/shops/show', function () {
    return view('shops.show');
})->name('shops.show');


Route::get('/products', function () {
    return view('products.index');
})->name('products.index');

/*Route::get('/products/{id}', function ($id) {
    return view('products.show');
})->name('products.show'); 

*/
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

    Route::get('/add-product', function () {
        return view('seller.add_product');
    })->name('seller.add_product');

    Route::get('/manage-products', function () {
        return view('seller.manage_products');
    })->name('seller.manage_products');
});

Route::middleware(['auth', 'seller'])->group(function () {
    Route::get('/seller-dashboard', function () {
        return view('seller.dashboard');
    })->name('seller.dashboard');

    Route::get('/add-product', function () {
        return view('seller.add_product');
    })->name('seller.add_product');

    Route::get('/manage-products', function () {
        return view('seller.manage_products');
    })->name('seller.manage_products');
});


Route::get('/seller-dashboard', function () {
    return view('seller.dashboard');
});
require __DIR__.'/auth.php';
