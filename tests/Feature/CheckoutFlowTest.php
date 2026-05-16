<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;

test('buyer can place an order for selected cart items', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Checkout Seller',
        'store_description' => 'Ready for checkout tests.',
        'contact_number' => '09171234567',
        'address' => '123 Market Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Checkout Goods',
        'slug' => 'checkout-goods',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Checkout Product',
        'category_id' => $category->id,
        'description' => 'Ready to purchase.',
        'price' => 250.00,
        'stock' => 7,
        'condition' => 'new',
        'weight' => 1.2,
        'width_cm' => 15,
        'length_cm' => 20,
        'height_cm' => 10,
        'shipping_fee' => 50.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    Address::create([
        'user_id' => $buyer->id,
        'full_name' => 'Buyer Address',
        'phone' => '09180001111',
        'region' => 'Region VII',
        'province' => 'Cebu',
        'city' => 'Cebu City',
        'barangay' => 'Lahug',
        'street_address' => '123 Buyer Lane',
        'postal_code' => '6000',
        'landmark' => 'Near IT Park',
        'label' => 'Home',
        'is_default' => true,
    ]);

    $cartItem = Cart::create([
        'user_id' => $buyer->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this
        ->actingAs($buyer)
        ->post(route('checkout.store'), [
            'selected_cart_items' => [$cartItem->id],
            'payment_method' => Order::PAYMENT_METHOD_COD,
        ]);

    $order = Order::query()->first();

    $response
        ->assertRedirect(route('buyer.orders.show', $order))
        ->assertSessionHas('success', 'Order placed successfully using Cash on Delivery.');

    expect($order)->not->toBeNull();
    expect((int) $order->user_id)->toBe($buyer->id);
    expect((int) $order->seller_id)->toBe($seller->id);
    expect((float) $order->shipping_fee)->toBe(100.0);
    expect((float) $order->total_price)->toBe(600.0);
    expect($order->payment_method)->toBe(Order::PAYMENT_METHOD_COD);

    expect(Cart::query()->count())->toBe(0);
    expect((int) $product->fresh()->stock)->toBe(5);
    expect($order->items()->count())->toBe(1);
});

test('buyer cannot checkout their own product', function () {
    $buyerSeller = User::factory()->create([
        'is_seller' => true,
    ]);

    $buyerSeller->sellerProfile()->create([
        'store_name' => 'Own Shop',
        'store_description' => 'Seller and buyer same account.',
        'contact_number' => '09171231234',
        'address' => 'Own Address',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Self Checkout',
        'slug' => 'self-checkout',
    ]);

    $product = Product::create([
        'user_id' => $buyerSeller->id,
        'name' => 'Own Product',
        'category_id' => $category->id,
        'description' => 'Should not be self-checkout.',
        'price' => 99.00,
        'stock' => 3,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 40.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $cartItem = Cart::create([
        'user_id' => $buyerSeller->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this
        ->actingAs($buyerSeller)
        ->post(route('checkout.store'), [
            'selected_cart_items' => [$cartItem->id],
            'payment_method' => Order::PAYMENT_METHOD_COD,
        ])
        ->assertRedirect(route('cart.index'))
        ->assertSessionHas('error', 'You cannot order your own products.');

    expect(Order::query()->count())->toBe(0);
    expect(Cart::query()->count())->toBe(1);
});

test('checkout page renders refactored sections and external payment script', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Checkout Render Seller',
        'store_description' => 'Ready for checkout render tests.',
        'contact_number' => '09170000000',
        'address' => 'Render Market Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Checkout Render Goods',
        'slug' => 'checkout-render-goods',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Checkout Render Product',
        'category_id' => $category->id,
        'description' => 'Ready to render.',
        'price' => 125.00,
        'stock' => 4,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 35.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    Address::create([
        'user_id' => $buyer->id,
        'full_name' => 'Checkout Render Buyer',
        'phone' => '09180002222',
        'region' => 'Region VII',
        'province' => 'Cebu',
        'city' => 'Cebu City',
        'barangay' => 'Lahug',
        'street_address' => '456 Render Lane',
        'postal_code' => '6000',
        'landmark' => 'Near IT Park',
        'label' => 'Home',
        'is_default' => true,
    ]);

    $cartItem = Cart::create([
        'user_id' => $buyer->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this
        ->actingAs($buyer)
        ->get(route('checkout.index', ['selected_cart_items' => [$cartItem->id]]))
        ->assertOk()
        ->assertSee('Shipping Address')
        ->assertSee('Shipping Method')
        ->assertSee('Payment Information')
        ->assertSee('Review Your Order')
        ->assertSee('assets/js/checkout-payment.js', false);
});
