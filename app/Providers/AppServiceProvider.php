<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Conversation;
use App\Models\Order;
use App\Policies\OrderPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Order::class, OrderPolicy::class);

        View::composer('*', function ($view) {
            $defaultAddress = null;
            $miniCartItems = collect();
            $miniCartCount = 0;
            $cartCount = 0;
            $messagePreviewConversations = collect();
            $messageConversationCount = 0;
            $buyerGuard = Auth::guard('web');

            if ($buyerGuard->check()) {
                $buyerId = $buyerGuard->id();
                $buyerUser = $buyerGuard->user();

                $defaultAddress = Address::where('user_id', $buyerId)
                    ->where('is_default', true)
                    ->first();

                $miniCartItems = Cart::with(['product.user'])
                    ->where('user_id', $buyerId)
                    ->latest()
                    ->take(4)
                    ->get();

                $miniCartCount = Cart::where('user_id', $buyerId)->count();
                $cartCount = $miniCartCount;

                $messagePreviewConversations = Conversation::with(['buyer', 'seller', 'latestMessage.sender'])
                    ->where('buyer_id', $buyerId)
                    ->latest('updated_at')
                    ->take(5)
                    ->get();

                $messageConversationCount = Conversation::where('buyer_id', $buyerId)->count();
            }

            $view->with('defaultAddress', $defaultAddress);
            $view->with('miniCartItems', $miniCartItems);
            $view->with('miniCartCount', $miniCartCount);
            $view->with('cartCount', $cartCount);
            $view->with('messagePreviewConversations', $messagePreviewConversations);
            $view->with('messageConversationCount', $messageConversationCount);
        });
    }
}
