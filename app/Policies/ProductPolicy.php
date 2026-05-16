<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function create(User $user): bool
    {
        return $user->isSeller() || $user->isAdmin();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin() || (int) $product->user_id === (int) $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }

    public function manageMedia(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }

    public function viewSellerReviews(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }

    public function moderate(User $user): bool
    {
        return $user->isAdmin();
    }

    public function approve(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    public function reject(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    public function bulkModerate(User $user): bool
    {
        return $user->isAdmin();
    }
}
