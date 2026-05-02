<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.conversation.{conversationId}', function ($user, int $conversationId) {
    return Conversation::query()
        ->whereKey($conversationId)
        ->where(function ($query) use ($user) {
            $query->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id);
        })
        ->exists();
});

Broadcast::channel('chat.presence.{conversationId}', function ($user, int $conversationId) {
    $conversation = Conversation::query()
        ->whereKey($conversationId)
        ->where(function ($query) use ($user) {
            $query->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id);
        })
        ->first();

    if (! $conversation) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});
