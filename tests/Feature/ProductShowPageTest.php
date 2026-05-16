<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Seller;
use App\Models\User;

function createVisibleSeller(array $userAttributes = []): User
{
    $seller = User::factory()->create(array_merge([
        'is_seller' => true,
    ], $userAttributes));

    $seller->sellerProfile()->create([
        'store_name' => 'Show Page Seller',
        'store_description' => 'Visible seller for product page tests.',
        'contact_number' => '09171234567',
        'address' => '123 Test Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    return $seller;
}

function createVisibleProduct(User $seller, Category $category, array $attributes = []): Product
{
    return Product::create(array_merge([
        'user_id' => $seller->id,
        'name' => 'Showcase Product',
        'category_id' => $category->id,
        'description' => 'Detailed description for the product page.',
        'price' => 499.99,
        'stock' => 12,
        'condition' => 'new',
        'weight' => 1.0,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 50,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ], $attributes));
}

test('product show page renders the refactored sections for guests', function () {
    $category = Category::create([
        'name' => 'Decor',
        'slug' => 'decor',
    ]);

    $seller = createVisibleSeller();
    $product = createVisibleProduct($seller, $category);
    createVisibleProduct($seller, $category, [
        'name' => 'Related Product',
    ]);

    foreach (range(1, 5) as $index) {
        ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Variant ' . $index,
            'option_values' => ['Size' => 'Option ' . $index],
            'sku' => 'SKU-' . $index,
            'price' => 499.99 + $index,
            'stock' => 3 + $index,
            'is_active' => true,
        ]);
    }

    $response = $this->get(route('products.show', $product));

    $response
        ->assertOk()
        ->assertSeeText('Order summary')
        ->assertSee('Ratings & Reviews', false)
        ->assertSeeText('You may also like')
        ->assertSeeText('View more options')
        ->assertSeeText('Message Seller');
});

test('product model detail display state uses active variants and rounded ratings', function () {
    $category = Category::create([
        'name' => 'Office',
        'slug' => 'office',
    ]);

    $seller = createVisibleSeller([
        'email' => 'detail-state-seller@example.com',
    ]);

    $product = createVisibleProduct($seller, $category, [
        'name' => 'Desk Organizer',
        'price' => 799.99,
        'stock' => 20,
    ]);

    ProductVariant::create([
        'product_id' => $product->id,
        'name' => 'Small',
        'option_values' => ['Size' => 'Small'],
        'sku' => 'DESK-SMALL',
        'price' => 749.99,
        'stock' => 2,
        'is_active' => true,
    ]);

    ProductVariant::create([
        'product_id' => $product->id,
        'name' => 'Large',
        'option_values' => ['Size' => 'Large'],
        'sku' => 'DESK-LARGE',
        'price' => 899.99,
        'stock' => 5,
        'is_active' => true,
    ]);

    ProductVariant::create([
        'product_id' => $product->id,
        'name' => 'Hidden',
        'option_values' => ['Size' => 'Hidden'],
        'sku' => 'DESK-HIDDEN',
        'price' => 199.99,
        'stock' => 99,
        'is_active' => false,
    ]);

    $product->load('variants', 'media');
    $product->setAttribute('reviews_avg_rating', 4.26);

    $state = $product->detailDisplayState();

    expect($state['activeVariants'])->toHaveCount(2);
    expect($state['hasVariants'])->toBeTrue();
    expect($state['displayStock'])->toBe(7);
    expect($state['displayPrice'])->toBe(749.99);
    expect($state['averageRating'])->toBe(4.3);
    expect($state['initialQuantity'])->toBe(0);
    expect($state['purchaseMaxStock'])->toBe(0);
    expect($state['initialPurchaseTotal'])->toBe(0.0);
    expect($state['initialMedia']['url'])->toContain('default-product.png');
});

test('eligible buyer can see the review form on the product show page', function () {
    $category = Category::create([
        'name' => 'Kitchen',
        'slug' => 'kitchen',
    ]);

    $seller = createVisibleSeller([
        'email' => 'seller-show@example.com',
    ]);
    $product = createVisibleProduct($seller, $category, [
        'name' => 'Reviewable Product',
    ]);

    $buyer = User::factory()->create([
        'email' => 'buyer-show@example.com',
    ]);

    $order = Order::create([
        'user_id' => $buyer->id,
        'total_price' => 499.99,
        'shipping_fee' => 50,
        'status' => Order::STATUS_COMPLETED,
        'shipping_status' => Order::SHIPPING_COMPLETED,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 499.99,
        'shipping_fee' => 50,
    ]);

    $response = $this
        ->actingAs($buyer)
        ->get(route('products.show', $product));

    $response
        ->assertOk()
        ->assertSeeText('Leave a review')
        ->assertSeeText('Submit Review')
        ->assertSeeText('Write a review');
});
