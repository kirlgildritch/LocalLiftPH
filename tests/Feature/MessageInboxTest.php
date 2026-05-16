<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

test('buyer can open a conversation page without latest message query errors', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $conversation = Conversation::create([
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
    ]);

    Message::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $buyer->id,
        'message' => 'Hello seller',
    ]);

    $response = $this
        ->actingAs($buyer)
        ->get(route('messages.show', $conversation));

    $response
        ->assertOk()
        ->assertSee('Hello seller');
});
