<?php

use App\Models\Seller;
use App\Models\User;
use App\Models\Voucher;

test('seller can create their own voucher', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Voucher Shop',
        'store_description' => 'Voucher management shop.',
        'contact_number' => '09171234567',
        'address' => '123 Seller Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $this
        ->actingAs($seller, 'seller')
        ->post(route('seller.vouchers.store'), [
            'code' => 'shop10',
            'name' => 'Shop Ten',
            'type' => Voucher::TYPE_PERCENT,
            'value' => 10,
            'minimum_subtotal' => 100,
            'maximum_discount' => 80,
            'usage_limit' => 20,
            'per_user_limit' => 1,
            'is_active' => 1,
        ])
        ->assertRedirect(route('seller.vouchers.index'))
        ->assertSessionHas('success', 'Voucher created successfully.');

    $voucher = Voucher::query()->where('code', 'SHOP10')->first();

    expect($voucher)->not->toBeNull();
    expect((int) $voucher->seller_id)->toBe($seller->id);
    expect($voucher->type)->toBe(Voucher::TYPE_PERCENT);
    expect((float) $voucher->value)->toBe(10.0);
});

test('seller cannot edit another sellers voucher', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);
    $otherSeller = User::factory()->create([
        'is_seller' => true,
    ]);

    $voucher = Voucher::create([
        'seller_id' => $otherSeller->id,
        'code' => 'OTHER10',
        'type' => Voucher::TYPE_FIXED,
        'value' => 10,
        'is_active' => true,
    ]);

    $this
        ->actingAs($seller, 'seller')
        ->get(route('seller.vouchers.edit', $voucher))
        ->assertNotFound();
});

test('seller voucher schedule is stored from manila local time', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Timezone Voucher Shop',
        'store_description' => 'Voucher schedule shop.',
        'contact_number' => '09171234567',
        'address' => '123 Seller Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $this
        ->actingAs($seller, 'seller')
        ->post(route('seller.vouchers.store'), [
            'code' => 'MNLTIME',
            'name' => 'Manila Time',
            'type' => Voucher::TYPE_FIXED,
            'value' => 20,
            'starts_at' => '2026-05-17T06:20',
            'ends_at' => '2026-05-18T06:20',
            'is_active' => 1,
        ])
        ->assertRedirect(route('seller.vouchers.index'));

    $voucher = Voucher::query()->where('code', 'MNLTIME')->first();

    expect($voucher->starts_at->timezone('UTC')->format('Y-m-d H:i'))->toBe('2026-05-16 22:20');
    expect($voucher->ends_at->timezone('UTC')->format('Y-m-d H:i'))->toBe('2026-05-17 22:20');
});
