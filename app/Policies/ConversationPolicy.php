<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        return $user->isAdmin()
            || (int) $conversation->buyer_id === (int) $user->id
            || (int) $conversation->seller_id === (int) $user->id;
    }

    public function sendMessage(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }

    public function updateTyping(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }
}
