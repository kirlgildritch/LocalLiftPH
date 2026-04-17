<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $defaultAddress = null;
            $miniCartItems = collect();
            $miniCartCount = 0;

            if (Auth::check()) {
                $defaultAddress = Address::where('user_id', Auth::id())
                    ->where('is_default', true)
                    ->first();

                $miniCartItems = Cart::with(['product.user'])
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->take(4)
                    ->get();

                $miniCartCount = Cart::where('user_id', Auth::id())->count();
            }

            $view->with('defaultAddress', $defaultAddress);
            $view->with('miniCartItems', $miniCartItems);
            $view->with('miniCartCount', $miniCartCount);
        });
    }
}