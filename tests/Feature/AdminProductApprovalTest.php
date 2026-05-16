<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

test('admin can approve a pending product', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $category = Category::create([
        'name' => 'Approval Category',
        'slug' => 'approval-category',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Pending Approval Product',
        'category_id' => $category->id,
        'description' => 'Awaiting moderation.',
        'price' => 300.00,
        'stock' => 5,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 50.00,
        'is_active' => 0,
        'status' => Product::STATUS_PENDING,
    ]);

    $this
        ->actingAs($admin, 'admin')
        ->patch(route('admin.products.approve', $product))
        ->assertRedirect()
        ->assertSessionHas('success', 'Pending Approval Product approved successfully.');

    $product->refresh();

    expect($product->status)->toBe(Product::STATUS_APPROVED);
    expect((int) $product->is_active)->toBe(1);
    expect($product->rejection_reason)->toBeNull();
});

test('non admin cannot approve a pending product', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $category = Category::create([
        'name' => 'Unauthorized Approval',
        'slug' => 'unauthorized-approval',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Protected Pending Product',
        'category_id' => $category->id,
        'description' => 'Should stay pending.',
        'price' => 325.00,
        'stock' => 4,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 50.00,
        'is_active' => 0,
        'status' => Product::STATUS_PENDING,
    ]);

    $this
        ->actingAs($buyer, 'admin')
        ->patch(route('admin.products.approve', $product))
        ->assertForbidden();

    expect($product->fresh()->status)->toBe(Product::STATUS_PENDING);
});
