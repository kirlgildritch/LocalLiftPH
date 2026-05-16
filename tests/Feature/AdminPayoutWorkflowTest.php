<?php

use App\Models\Order;
use App\Models\Seller;
use App\Models\SellerPayout;
use App\Models\User;

test('admin can mark a pending payout as paid and release linked orders', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $sellerUser = User::factory()->create([
        'is_seller' => true,
    ]);

    $sellerProfile = $sellerUser->sellerProfile()->create([
        'store_name' => 'Paid Out Seller',
        'store_description' => 'Seller for payout tests.',
        'contact_number' => '09171238888',
        'address' => 'Payout Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $payout = SellerPayout::create([
        'seller_id' => $sellerProfile->id,
        'amount' => 1500.00,
        'method' => 'gcash',
        'account_name' => 'Paid Out Seller',
        'account_number' => '09171238888',
        'status' => SellerPayout::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $order = Order::create([
        'user_id' => User::factory()->create()->id,
        'seller_id' => $sellerUser->id,
        'shipping_fee' => 60.00,
        'total_price' => 560.00,
        'status' => Order::STATUS_COMPLETED,
        'shipping_status' => Order::SHIPPING_COMPLETED,
        'payment_method' => Order::PAYMENT_METHOD_COD,
        'payment_status' => Order::PAYMENT_PAID,
        'paid_at' => now(),
        'seller_earning_status' => Order::EARNING_AVAILABLE,
        'seller_payout_id' => $payout->id,
    ]);

    $this
        ->actingAs($admin, 'admin')
        ->patch(route('admin.payouts.paid', $payout), [
            'reference_number' => 'LLP-REF-1001',
        ])
        ->assertRedirect()
        ->assertSessionHas('success', 'Payout marked paid.');

    $payout->refresh();
    $order->refresh();

    expect($payout->status)->toBe(SellerPayout::STATUS_PAID);
    expect($payout->reference_number)->toBe('LLP-REF-1001');
    expect($payout->processed_at)->not->toBeNull();
    expect($order->seller_earning_status)->toBe(Order::EARNING_PAID_OUT);
    expect($order->seller_released_at)->not->toBeNull();
});

test('admin can reject a pending payout and return linked orders to available earnings', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $sellerUser = User::factory()->create([
        'is_seller' => true,
    ]);

    $sellerProfile = $sellerUser->sellerProfile()->create([
        'store_name' => 'Rejected Payout Seller',
        'store_description' => 'Seller for rejected payout tests.',
        'contact_number' => '09171239999',
        'address' => 'Rejected Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $payout = SellerPayout::create([
        'seller_id' => $sellerProfile->id,
        'amount' => 950.00,
        'method' => 'bank',
        'account_name' => 'Rejected Payout Seller',
        'account_number' => '1234567890',
        'status' => SellerPayout::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $order = Order::create([
        'user_id' => User::factory()->create()->id,
        'seller_id' => $sellerUser->id,
        'shipping_fee' => 40.00,
        'total_price' => 440.00,
        'status' => Order::STATUS_COMPLETED,
        'shipping_status' => Order::SHIPPING_COMPLETED,
        'payment_method' => Order::PAYMENT_METHOD_COD,
        'payment_status' => Order::PAYMENT_PAID,
        'paid_at' => now(),
        'seller_earning_status' => Order::EARNING_ON_HOLD,
        'seller_payout_id' => $payout->id,
    ]);

    $this
        ->actingAs($admin, 'admin')
        ->patch(route('admin.payouts.reject', $payout))
        ->assertRedirect()
        ->assertSessionHas('success', 'Payout rejected.');

    $payout->refresh();
    $order->refresh();

    expect($payout->status)->toBe(SellerPayout::STATUS_REJECTED);
    expect($payout->processed_at)->not->toBeNull();
    expect($order->seller_payout_id)->toBeNull();
    expect($order->seller_earning_status)->toBe(Order::EARNING_AVAILABLE);
});
