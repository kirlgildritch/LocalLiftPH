<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    if (auth('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }

    if (auth('seller')->check()) {
        return redirect()->route('seller.dashboard');
    }

    if (auth('web')->check()) {
        return redirect()->route('home');
    }

    return redirect()->route('login');
})->name('dashboard');

require __DIR__ . '/frontend.php';
require __DIR__ . '/seller.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/buyer.php';
require __DIR__ . '/auth.php';