<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('seller can view the edit product page for their own product', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Laptop Stand',
        'category_id' => $category->id,
        'description' => '<p><strong>Adjustable stand</strong><br>Ideal for desks.</p>',
        'price' => 899.99,
        'stock' => 5,
        'condition' => 'new',
        'weight' => 1.5,
        'width_cm' => 20,
        'length_cm' => 30,
        'height_cm' => 10,
        'shipping_fee' => 112.5,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $response = $this
        ->actingAs($seller, 'seller')
        ->get(route('seller.products.edit', $product));

    $response
        ->assertOk()
        ->assertSee('Edit Product')
        ->assertSee('Laptop Stand')
        ->assertSee('Adjustable stand')
        ->assertSee('Ideal for desks.')
        ->assertDontSee('&lt;p&gt;');
});

test('seller can update their own product and replace its image', function () {
    Storage::fake('public');

    $pngPixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO7Z0N8AAAAASUVORK5CYII=');

    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $category = Category::create([
        'name' => 'Home',
        'slug' => 'home',
    ]);

    $newCategory = Category::create([
        'name' => 'Office',
        'slug' => 'office',
    ]);

    $oldImage = 'products/old-product.png';
    Storage::disk('public')->put($oldImage, $pngPixel);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Desk Lamp',
        'category_id' => $category->id,
        'description' => 'Warm lighting',
        'price' => 499.99,
        'stock' => 8,
        'condition' => 'used',
        'weight' => 2.25,
        'width_cm' => 25,
        'length_cm' => 25,
        'height_cm' => 20,
        'shipping_fee' => 138.75,
        'image' => $oldImage,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $response = $this
        ->actingAs($seller, 'seller')
        ->patch(route('seller.products.update', $product), [
            'name' => 'Desk Lamp Pro',
            'category_id' => $newCategory->id,
            'description' => 'Brighter lighting for workspaces',
            'price' => 649.50,
            'stock' => 11,
            'condition' => 'new',
            'weight' => 3.5,
            'width_cm' => 40,
            'length_cm' => 30,
            'height_cm' => 20,
            'image' => UploadedFile::fake()->createWithContent('new-product.png', $pngPixel),
        ]);

    $response
        ->assertRedirect(route('seller.products.index'))
        ->assertSessionHas('success', 'Product updated successfully.');

    $product->refresh();

    expect($product->name)->toBe('Desk Lamp Pro');
    expect($product->category_id)->toBe($newCategory->id);
    expect((float) $product->price)->toBe(649.50);
    expect($product->stock)->toBe(11);
    expect($product->condition)->toBe('new');
    expect((float) $product->weight)->toBe(3.5);
    expect((float) $product->width_cm)->toBe(40.0);
    expect((float) $product->length_cm)->toBe(30.0);
    expect((float) $product->height_cm)->toBe(20.0);
    expect((float) $product->shipping_fee)->toBe(228.0);
    expect($product->image)->not->toBe($oldImage);

    Storage::disk('public')->assertMissing($oldImage);
    Storage::disk('public')->assertExists($product->image);
});

test('seller cannot update another sellers product', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $otherSeller = User::factory()->create([
        'is_seller' => true,
    ]);

    $category = Category::create([
        'name' => 'Books',
        'slug' => 'books',
    ]);

    $product = Product::create([
        'user_id' => $otherSeller->id,
        'name' => 'Notebook',
        'category_id' => $category->id,
        'description' => 'Lined pages',
        'price' => 79.99,
        'stock' => 20,
        'condition' => 'new',
        'weight' => 0.5,
        'width_cm' => 15,
        'length_cm' => 21,
        'height_cm' => 2,
        'shipping_fee' => 77.5,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $this
        ->actingAs($seller, 'seller')
        ->patch(route('seller.products.update', $product), [
            'name' => 'Changed',
            'category_id' => $category->id,
            'description' => 'Changed description',
            'price' => 100,
            'stock' => 1,
            'condition' => 'used',
            'weight' => 1,
            'width_cm' => 10,
            'length_cm' => 10,
            'height_cm' => 10,
        ])
        ->assertNotFound();
});

test('seller can view a dedicated reviews page for their own product', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $buyer = User::factory()->create();

    $category = Category::create([
        'name' => 'Accessories',
        'slug' => 'accessories',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Phone Case',
        'category_id' => $category->id,
        'description' => 'Protective case',
        'price' => 299.99,
        'stock' => 14,
        'condition' => 'new',
        'weight' => 0.25,
        'width_cm' => 10,
        'length_cm' => 18,
        'height_cm' => 2,
        'shipping_fee' => 68.75,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $order = Order::create([
        'user_id' => $buyer->id,
        'total_price' => 299.99,
        'shipping_fee' => 0,
        'status' => Order::STATUS_DELIVERED,
        'shipping_status' => Order::SHIPPING_DELIVERED,
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 299.99,
        'shipping_fee' => 0,
    ]);

    Review::create([
        'product_id' => $product->id,
        'user_id' => $buyer->id,
        'order_item_id' => $orderItem->id,
        'rating' => 5,
        'comment' => 'Excellent quality.',
    ]);

    $response = $this
        ->actingAs($seller, 'seller')
        ->get(route('seller.products.reviews', $product));

    $response
        ->assertOk()
        ->assertSee('Product Reviews')
        ->assertSee('Phone Case')
        ->assertSee('Excellent quality.');
});

test('seller cannot view another sellers dedicated reviews page', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $otherSeller = User::factory()->create([
        'is_seller' => true,
    ]);

    $category = Category::create([
        'name' => 'Audio',
        'slug' => 'audio',
    ]);

    $product = Product::create([
        'user_id' => $otherSeller->id,
        'name' => 'Headphones',
        'category_id' => $category->id,
        'description' => 'Wireless audio',
        'price' => 1999.99,
        'stock' => 6,
        'condition' => 'new',
        'weight' => 0.8,
        'width_cm' => 18,
        'length_cm' => 20,
        'height_cm' => 8,
        'shipping_fee' => 88,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $this
        ->actingAs($seller, 'seller')
        ->get(route('seller.products.reviews', $product))
        ->assertNotFound();
});
