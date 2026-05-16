<?php

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;

test('buyer can cancel an eligible order and product stock is restored', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Lifecycle Seller',
        'store_description' => 'Seller for order lifecycle tests.',
        'contact_number' => '09171230000',
        'address' => 'Market Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    Address::create([
        'user_id' => $buyer->id,
        'full_name' => 'Lifecycle Buyer',
        'phone' => '09180001234',
        'region' => 'Region VII',
        'province' => 'Cebu',
        'city' => 'Cebu City',
        'barangay' => 'Lahug',
        'street_address' => '123 Buyer Street',
        'postal_code' => '6000',
        'landmark' => 'Near IT Park',
        'label' => 'Home',
        'is_default' => true,
    ]);

    $category = Category::create([
        'name' => 'Lifecycle Category',
        'slug' => 'lifecycle-category',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Lifecycle Product',
        'category_id' => $category->id,
        'description' => 'Product for cancellation tests.',
        'price' => 200.00,
        'stock' => 3,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 45.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $order = Order::create([
        'user_id' => $buyer->id,
        'seller_id' => $seller->id,
        'shipping_fee' => 45.00,
        'total_price' => 445.00,
        'status' => Order::STATUS_PENDING,
        'shipping_status' => Order::SHIPPING_PENDING,
        'payment_method' => Order::PAYMENT_METHOD_COD,
        'payment_status' => Order::PAYMENT_PENDING,
        'seller_earning_status' => Order::EARNING_PENDING,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 200.00,
    ]);

    $response = $this
        ->actingAs($buyer)
        ->patch(route('buyer.orders.cancel', $order), [
            'reasons' => ['Changed my mind'],
        ]);

    $response
        ->assertRedirect(route('buyer.orders.show', $order))
        ->assertSessionHas('success', 'Order cancelled successfully.');

    $order->refresh();

    expect($order->status)->toBe(Order::STATUS_CANCELLED);
    expect($order->shipping_status)->toBe(Order::SHIPPING_CANCELLED);
    expect($order->payment_status)->toBe(Order::PAYMENT_CANCELLED);
    expect($order->seller_earning_status)->toBe(Order::EARNING_REVERSED);
    expect((int) $product->fresh()->stock)->toBe(5);
    expect($order->cancellation)->not->toBeNull();
    expect($order->cancellation->reasons)->toBe(['Changed my mind']);
});

test('buyer can confirm receipt for a shipped order', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Receipt Seller',
        'store_description' => 'Seller for receipt confirmation tests.',
        'contact_number' => '09171235555',
        'address' => 'Receipt Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $order = Order::create([
        'user_id' => $buyer->id,
        'seller_id' => $seller->id,
        'shipping_fee' => 50.00,
        'total_price' => 350.00,
        'status' => Order::STATUS_SHIPPED,
        'shipping_status' => Order::SHIPPING_SHIPPED,
        'payment_method' => Order::PAYMENT_METHOD_COD,
        'payment_status' => Order::PAYMENT_PENDING,
        'seller_earning_status' => Order::EARNING_ON_HOLD,
    ]);

    $response = $this
        ->actingAs($buyer)
        ->patch(route('buyer.orders.received', $order));

    $response
        ->assertRedirect(route('buyer.orders.show', $order))
        ->assertSessionHas('success', 'Order marked as received successfully.');

    $order->refresh();

    expect($order->status)->toBe(Order::STATUS_COMPLETED);
    expect($order->shipping_status)->toBe(Order::SHIPPING_COMPLETED);
    expect($order->payment_status)->toBe(Order::PAYMENT_PAID);
    expect($order->seller_earning_status)->toBe(Order::EARNING_AVAILABLE);
    expect($order->paid_at)->not->toBeNull();
});
