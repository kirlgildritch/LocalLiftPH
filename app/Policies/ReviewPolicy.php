<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function reply(User $user, Review $review): bool
    {
        return $user->isAdmin() || (int) ($review->product->user_id ?? 0) === (int) $user->id;
    }
}
