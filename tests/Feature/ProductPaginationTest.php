<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;

test('products pagination ajax returns only the results fragment', function () {
    $category = Category::create([
        'name' => 'Kitchenware',
        'slug' => 'kitchenware',
    ]);

    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Ajax Seller',
        'store_description' => 'A visible seller for pagination tests.',
        'contact_number' => '09171234567',
        'address' => '123 Test Street',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    foreach (range(1, 13) as $index) {
        Product::create([
            'user_id' => $seller->id,
            'name' => 'Ajax Product ' . $index,
            'category_id' => $category->id,
            'description' => 'Visible product ' . $index,
            'price' => 100 + $index,
            'stock' => 5,
            'condition' => 'new',
            'weight' => 1.0,
            'width_cm' => 10,
            'length_cm' => 10,
            'height_cm' => 10,
            'shipping_fee' => 50,
            'is_active' => 1,
            'status' => Product::STATUS_APPROVED,
            'created_at' => now()->addSeconds($index),
            'updated_at' => now()->addSeconds($index),
        ]);
    }

    $response = $this
        ->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->get(route('products.index', ['page' => 2]));

    $response
        ->assertOk()
        ->assertSee('data-market-pagination-grid', false)
        ->assertSee('Ajax Product 1')
        ->assertDontSee('Ajax Product 13')
        ->assertDontSee('Categories')
        ->assertDontSee('<html', false);
});
