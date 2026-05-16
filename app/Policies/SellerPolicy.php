<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\User;

class SellerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function reviewApplication(User $user, Seller $seller): bool
    {
        return $user->isAdmin();
    }
}
