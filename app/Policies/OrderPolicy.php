<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || (int) $order->user_id === (int) $user->id;
    }

    public function viewMonitoring(User $user): bool
    {
        return $user->isAdmin();
    }

    public function updateShippingStatus(User $user, Order $order): bool
    {
        return $user->isSeller() && $order->sellerOwnsAllItems($user);
    }
}
