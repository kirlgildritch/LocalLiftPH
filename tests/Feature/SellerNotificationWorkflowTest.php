<?php

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function createSellerNotification(User $seller, array $data = [], ?string $readAt = null): DatabaseNotification
{
    $id = (string) Str::uuid();

    DB::table('notifications')->insert([
        'id' => $id,
        'type' => 'seller.test',
        'notifiable_type' => User::class,
        'notifiable_id' => $seller->id,
        'data' => json_encode(array_merge([
            'type' => 'orders',
            'action' => 'new_order',
            'title' => 'New order received',
            'message' => 'A buyer placed a new order.',
        ], $data)),
        'read_at' => $readAt,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return DatabaseNotification::query()->findOrFail($id);
}

test('seller can view notifications page with notification partials', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    createSellerNotification($seller, [
        'title' => 'Product approved',
        'message' => 'Your product is now visible to buyers.',
        'type' => 'admin',
    ]);

    $this
        ->actingAs($seller, 'seller')
        ->get(route('seller.notifications.index'))
        ->assertOk()
        ->assertSee('Product approved')
        ->assertSee('Your product is now visible to buyers.')
        ->assertSee('Mark all as read');
});

test('seller can mark own notification as read', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);
    $notification = createSellerNotification($seller);

    $this
        ->actingAs($seller, 'seller')
        ->from(route('seller.notifications.index'))
        ->patch(route('seller.notifications.read', $notification))
        ->assertRedirect(route('seller.notifications.index'))
        ->assertSessionHas('success', 'Notification marked as read.');

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('seller cannot manage another sellers notification', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);
    $otherSeller = User::factory()->create([
        'is_seller' => true,
    ]);
    $notification = createSellerNotification($otherSeller);

    $this
        ->actingAs($seller, 'seller')
        ->patch(route('seller.notifications.read', $notification))
        ->assertForbidden();

    expect($notification->fresh()->read_at)->toBeNull();
});
