<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherRedemption;

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

test('buyer can apply a fixed voucher during checkout', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Voucher Seller',
        'store_description' => 'Ready for voucher tests.',
        'contact_number' => '09171234567',
        'address' => '123 Market Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Voucher Goods',
        'slug' => 'voucher-goods',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Voucher Product',
        'category_id' => $category->id,
        'description' => 'Ready to purchase with voucher.',
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
        'full_name' => 'Voucher Buyer',
        'phone' => '09180001111',
        'region' => 'Region VII',
        'province' => 'Cebu',
        'city' => 'Cebu City',
        'barangay' => 'Lahug',
        'street_address' => '123 Buyer Lane',
        'postal_code' => '6000',
        'label' => 'Home',
        'is_default' => true,
    ]);

    $cartItem = Cart::create([
        'user_id' => $buyer->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    Voucher::create([
        'code' => 'WELCOME50',
        'type' => Voucher::TYPE_FIXED,
        'value' => 50,
        'minimum_subtotal' => 100,
        'per_user_limit' => 1,
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($buyer)
        ->post(route('checkout.store'), [
            'selected_cart_items' => [$cartItem->id],
            'payment_method' => Order::PAYMENT_METHOD_COD,
            'voucher_code' => 'welcome50',
        ]);

    $order = Order::query()->first();

    $response->assertRedirect(route('buyer.orders.show', $order));

    expect($order->voucher_code)->toBe('WELCOME50');
    expect((float) $order->voucher_discount)->toBe(50.0);
    expect((float) $order->total_price)->toBe(550.0);
    expect(VoucherRedemption::query()->count())->toBe(1);
    expect((float) VoucherRedemption::query()->first()->discount_amount)->toBe(50.0);
});

test('voucher usage limits are enforced per buyer', function () {
    $buyer = User::factory()->create();

    $voucher = Voucher::create([
        'code' => 'ONCEONLY',
        'type' => Voucher::TYPE_PERCENT,
        'value' => 10,
        'per_user_limit' => 1,
        'is_active' => true,
    ]);

    VoucherRedemption::create([
        'voucher_id' => $voucher->id,
        'user_id' => $buyer->id,
        'code' => $voucher->code,
        'discount_amount' => 25,
        'redeemed_at' => now(),
    ]);

    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Limit Seller',
        'store_description' => 'Ready for voucher limit tests.',
        'contact_number' => '09171234567',
        'address' => '123 Market Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Limit Goods',
        'slug' => 'limit-goods',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Limit Product',
        'category_id' => $category->id,
        'description' => 'Ready to reject repeated voucher.',
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
        'full_name' => 'Limit Buyer',
        'phone' => '09180001111',
        'region' => 'Region VII',
        'province' => 'Cebu',
        'city' => 'Cebu City',
        'barangay' => 'Lahug',
        'street_address' => '123 Buyer Lane',
        'postal_code' => '6000',
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
        ->post(route('checkout.store'), [
            'selected_cart_items' => [$cartItem->id],
            'payment_method' => Order::PAYMENT_METHOD_COD,
            'voucher_code' => 'ONCEONLY',
        ])
        ->assertSessionHasErrors('voucher_code');

    expect(Order::query()->count())->toBe(0);
});

test('seller voucher only discounts that sellers order in multi seller checkout', function () {
    $buyer = User::factory()->create();
    $sellerA = User::factory()->create([
        'is_seller' => true,
    ]);
    $sellerB = User::factory()->create([
        'is_seller' => true,
    ]);

    foreach ([[$sellerA, 'Seller A'], [$sellerB, 'Seller B']] as [$seller, $storeName]) {
        $seller->sellerProfile()->create([
            'store_name' => $storeName,
            'store_description' => 'Ready for scoped voucher tests.',
            'contact_number' => '09171234567',
            'address' => '123 Market Street',
            'application_status' => Seller::STATUS_APPROVED,
            'shop_status' => Seller::SHOP_STATUS_OPEN,
        ]);
    }

    $category = Category::create([
        'name' => 'Scoped Voucher Goods',
        'slug' => 'scoped-voucher-goods',
    ]);

    $productA = Product::create([
        'user_id' => $sellerA->id,
        'name' => 'Seller A Product',
        'category_id' => $category->id,
        'description' => 'Seller A item.',
        'price' => 300.00,
        'stock' => 5,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 40.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $productB = Product::create([
        'user_id' => $sellerB->id,
        'name' => 'Seller B Product',
        'category_id' => $category->id,
        'description' => 'Seller B item.',
        'price' => 200.00,
        'stock' => 5,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 30.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    Address::create([
        'user_id' => $buyer->id,
        'full_name' => 'Scoped Voucher Buyer',
        'phone' => '09180001111',
        'region' => 'Region VII',
        'province' => 'Cebu',
        'city' => 'Cebu City',
        'barangay' => 'Lahug',
        'street_address' => '123 Buyer Lane',
        'postal_code' => '6000',
        'label' => 'Home',
        'is_default' => true,
    ]);

    $cartItemA = Cart::create([
        'user_id' => $buyer->id,
        'product_id' => $productA->id,
        'quantity' => 1,
    ]);

    $cartItemB = Cart::create([
        'user_id' => $buyer->id,
        'product_id' => $productB->id,
        'quantity' => 1,
    ]);

    Voucher::create([
        'seller_id' => $sellerA->id,
        'code' => 'SELLERA50',
        'type' => Voucher::TYPE_FIXED,
        'value' => 50,
        'minimum_subtotal' => 100,
        'is_active' => true,
    ]);

    $this
        ->actingAs($buyer)
        ->post(route('checkout.store'), [
            'selected_cart_items' => [$cartItemA->id, $cartItemB->id],
            'payment_method' => Order::PAYMENT_METHOD_COD,
            'voucher_code' => 'SELLERA50',
        ])
        ->assertRedirect();

    $sellerAOrder = Order::query()->where('seller_id', $sellerA->id)->first();
    $sellerBOrder = Order::query()->where('seller_id', $sellerB->id)->first();

    expect((float) $sellerAOrder->voucher_discount)->toBe(50.0);
    expect((float) $sellerAOrder->total_price)->toBe(290.0);
    expect((float) $sellerBOrder->voucher_discount)->toBe(0.0);
    expect((float) $sellerBOrder->total_price)->toBe(230.0);
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
