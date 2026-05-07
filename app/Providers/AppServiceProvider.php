<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Order;
use App\Notifications\SellerNotificationService;
use App\Policies\OrderPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->runningUnitTests()) {
            return;
        }

        $testingStoragePath = base_path('.test-runtime/storage');

        $this->app->useStoragePath($testingStoragePath);

        foreach ([
            $testingStoragePath,
            storage_path('app'),
            storage_path('app/private'),
            storage_path('app/public'),
            storage_path('framework/cache'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/testing'),
            storage_path('framework/testing/disks'),
            storage_path('framework/views'),
            storage_path('logs'),
        ] as $directory) {
            File::ensureDirectoryExists($directory);
        }

        config()->set('filesystems.disks.local.root', storage_path('app/private'));
        config()->set('filesystems.disks.public.root', storage_path('app/public'));
        config()->set('view.compiled', storage_path('framework/views'));
    }

    public function boot(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);

        View::composer('layouts.app', function ($view) {
            $miniCartItems = collect();
            $miniCartCount = 0;
            $cartCount = 0;
            $buyerGuard = Auth::guard('web');

            if ($buyerGuard->check()) {
                $buyerId = $buyerGuard->id();
                $cartQuery = Cart::query()->where('user_id', $buyerId);

                $miniCartItems = (clone $cartQuery)
                    ->with(['product.user'])
                    ->latest()
                    ->take(4)
                    ->get();

                $miniCartCount = (clone $cartQuery)->count();
                $cartCount = $miniCartCount;
            }

            $view->with('miniCartItems', $miniCartItems);
            $view->with('miniCartCount', $miniCartCount);
            $view->with('cartCount', $cartCount);
        });

        View::composer('layouts.seller', function ($view) {
            $sellerHeaderNotifications = collect();
            $sellerUnreadNotificationCount = 0;
            $sellerGuard = Auth::guard('seller');

            if ($sellerGuard->check()) {
                $sellerUser = $sellerGuard->user();

                app(SellerNotificationService::class)->syncPendingOrdersNotShipped($sellerUser);

                $sellerHeaderNotifications = $sellerUser->notifications()
                    ->latest()
                    ->limit(5)
                    ->get();

                $sellerUnreadNotificationCount = $sellerUser->unreadNotifications()->count();
            }

            $view->with('sellerHeaderNotifications', $sellerHeaderNotifications);
            $view->with('sellerUnreadNotificationCount', $sellerUnreadNotificationCount);
        });
    }
}
