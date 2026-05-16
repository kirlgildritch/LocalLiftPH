<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Report;
use App\Models\Seller;
use App\Models\User;

test('buyer can submit a product report', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Reported Shop',
        'store_description' => 'Visible shop.',
        'contact_number' => '09175550000',
        'address' => 'Seller Address',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Reports',
        'slug' => 'reports',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Reported Product',
        'category_id' => $category->id,
        'description' => 'Potentially problematic.',
        'price' => 150.00,
        'stock' => 6,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 45.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $response = $this
        ->actingAs($buyer)
        ->from(route('products.show', $product))
        ->post(route('reports.store'), [
            'product_id' => $product->id,
            'seller_id' => $seller->id,
            'reason' => 'spam',
            'message' => 'This listing looks suspicious.',
            'modal_context' => 'product',
        ]);

    $response
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHas('success', 'Your report has been submitted for review.');

    $report = Report::query()->first();

    expect($report)->not->toBeNull();
    expect((int) $report->user_id)->toBe($buyer->id);
    expect((int) $report->product_id)->toBe($product->id);
    expect((int) $report->seller_id)->toBe($seller->id);
    expect($report->reason)->toBe('spam');
});

test('report submission fails when the selected seller does not match the product', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create(['is_seller' => true]);
    $otherSeller = User::factory()->create(['is_seller' => true]);

    foreach ([$seller, $otherSeller] as $shopUser) {
        $shopUser->sellerProfile()->create([
            'store_name' => $shopUser->name . ' Shop',
            'store_description' => 'Visible seller.',
            'contact_number' => '09170000000',
            'address' => 'Seller Address',
            'application_status' => Seller::STATUS_APPROVED,
            'shop_status' => Seller::SHOP_STATUS_OPEN,
        ]);
    }

    $category = Category::create([
        'name' => 'Mismatch Reports',
        'slug' => 'mismatch-reports',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Mismatch Product',
        'category_id' => $category->id,
        'description' => 'Wrong seller should fail.',
        'price' => 190.00,
        'stock' => 4,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 45.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $this
        ->actingAs($buyer)
        ->from(route('products.show', $product))
        ->post(route('reports.store'), [
            'product_id' => $product->id,
            'seller_id' => $otherSeller->id,
            'reason' => 'other',
            'message' => 'Seller does not match product.',
            'modal_context' => 'product',
        ])
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHasErrorsIn('reportSubmission', 'seller_id')
        ->assertSessionHas('report_modal_open', 'product');

    expect(Report::query()->count())->toBe(0);
});
