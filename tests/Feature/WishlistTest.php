<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Models\Wishlist;

test('buyer can add a visible product to wishlist', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Wishlist Shop',
        'store_description' => 'Visible shop.',
        'contact_number' => '09178889999',
        'address' => 'Wishlist Address',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Wishlist Category',
        'slug' => 'wishlist-category',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Wishlist Product',
        'category_id' => $category->id,
        'description' => 'Save me later.',
        'price' => 450.00,
        'stock' => 9,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 50.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $this
        ->actingAs($buyer)
        ->post(route('buyer.wishlist.store', $product))
        ->assertRedirect()
        ->assertSessionHas('success', 'Product added to your wishlist.');

    expect(Wishlist::query()->count())->toBe(1);
    expect((int) Wishlist::query()->first()->user_id)->toBe($buyer->id);
});

test('buyer cannot add their own product to wishlist', function () {
    $buyerSeller = User::factory()->create([
        'is_seller' => true,
    ]);

    $buyerSeller->sellerProfile()->create([
        'store_name' => 'Own Wishlist Shop',
        'store_description' => 'Own visible shop.',
        'contact_number' => '09176667777',
        'address' => 'Own Wishlist Address',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Own Wishlist Category',
        'slug' => 'own-wishlist-category',
    ]);

    $product = Product::create([
        'user_id' => $buyerSeller->id,
        'name' => 'Own Wishlist Product',
        'category_id' => $category->id,
        'description' => 'Cannot save own item.',
        'price' => 175.00,
        'stock' => 4,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 50.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $this
        ->actingAs($buyerSeller)
        ->post(route('buyer.wishlist.store', $product))
        ->assertRedirect()
        ->assertSessionHas('error', 'You cannot add your own product to your wishlist.');

    expect(Wishlist::query()->count())->toBe(0);
});
