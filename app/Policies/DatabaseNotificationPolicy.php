<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class DatabaseNotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, DatabaseNotification $notification): bool
    {
        return $user->isAdmin()
            && $notification->notifiable_type === $user::class
            && (string) $notification->notifiable_id === (string) $user->id;
    }

    public function update(User $user, DatabaseNotification $notification): bool
    {
        return $this->view($user, $notification);
    }

    public function delete(User $user, DatabaseNotification $notification): bool
    {
        return $this->view($user, $notification);
    }

    public function clearRead(User $user): bool
    {
        return $user->isAdmin();
    }
}
