<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Models\Voucher;

function createVoucherDiscoveryProduct(): array
{
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Voucher Discovery Shop',
        'store_description' => 'A shop with buyer-facing vouchers.',
        'contact_number' => '09171234567',
        'address' => '123 Voucher Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Voucher Discovery Goods',
        'slug' => 'voucher-discovery-goods',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Voucher Discovery Product',
        'category_id' => $category->id,
        'description' => 'Visible product with vouchers.',
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

    return [$seller, $product];
}

test('buyers can see active seller vouchers on shop and product pages', function () {
    [$seller, $product] = createVoucherDiscoveryProduct();

    Voucher::create([
        'seller_id' => $seller->id,
        'code' => 'DISCOVER10',
        'name' => 'Discovery Discount',
        'type' => Voucher::TYPE_PERCENT,
        'value' => 10,
        'minimum_subtotal' => 200,
        'maximum_discount' => 80,
        'ends_at' => now()->addDays(7),
        'is_active' => true,
    ]);

    Voucher::create([
        'seller_id' => $seller->id,
        'code' => 'EXPIRED10',
        'type' => Voucher::TYPE_PERCENT,
        'value' => 10,
        'ends_at' => now()->subDay(),
        'is_active' => true,
    ]);

    $this
        ->get(route('shops.show', $seller))
        ->assertOk()
        ->assertSee('DISCOVER10')
        ->assertSee('10% off')
        ->assertDontSee('EXPIRED10');

    $this
        ->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('DISCOVER10')
        ->assertSee('10% off')
        ->assertDontSee('EXPIRED10');
});

test('checkout shows seller voucher apply links for selected seller groups', function () {
    $buyer = User::factory()->create();
    [$seller, $product] = createVoucherDiscoveryProduct();

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
        'quantity' => 1,
    ]);

    Voucher::create([
        'seller_id' => $seller->id,
        'code' => 'CHECKOUT50',
        'type' => Voucher::TYPE_FIXED,
        'value' => 50,
        'minimum_subtotal' => 100,
        'is_active' => true,
    ]);

    $this
        ->actingAs($buyer)
        ->get(route('checkout.index', ['selected_cart_items' => [$cartItem->id]]))
        ->assertOk()
        ->assertSee('CHECKOUT50')
        ->assertSee('Use')
        ->assertSee('voucher_code=CHECKOUT50', false);

    $response = $this
        ->actingAs($buyer)
        ->post(route('checkout.store'), [
            'selected_cart_items' => [$cartItem->id],
            'payment_method' => Order::PAYMENT_METHOD_COD,
            'voucher_code' => 'CHECKOUT50',
        ]);

    $order = Order::query()->first();

    $response->assertRedirect(route('buyer.orders.show', $order));
    expect((float) $order->voucher_discount)->toBe(50.0);
});
