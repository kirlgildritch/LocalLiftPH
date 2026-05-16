<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('eligible buyer can submit a review with uploaded media', function () {
    Storage::fake('public');
    $pngPixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO7Z0N8AAAAASUVORK5CYII=');

    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Review Shop',
        'store_description' => 'Visible seller.',
        'contact_number' => '09173334444',
        'address' => 'Review Seller Address',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Review Category',
        'slug' => 'review-category',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Review Product',
        'category_id' => $category->id,
        'description' => 'Ready for feedback.',
        'price' => 520.00,
        'stock' => 5,
        'condition' => 'new',
        'weight' => 1,
        'width_cm' => 10,
        'length_cm' => 10,
        'height_cm' => 10,
        'shipping_fee' => 50.00,
        'is_active' => 1,
        'status' => Product::STATUS_APPROVED,
    ]);

    $order = Order::create([
        'user_id' => $buyer->id,
        'seller_id' => $seller->id,
        'shipping_fee' => 50.00,
        'total_price' => 570.00,
        'status' => Order::STATUS_COMPLETED,
        'shipping_status' => Order::SHIPPING_COMPLETED,
        'payment_method' => Order::PAYMENT_METHOD_COD,
        'payment_status' => Order::PAYMENT_PAID,
        'paid_at' => now(),
        'seller_earning_status' => Order::EARNING_AVAILABLE,
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 520.00,
        'shipping_fee' => 50.00,
    ]);

    $response = $this
        ->actingAs($buyer)
        ->post(route('products.reviews.store', $product), [
            'order_item_id' => $orderItem->id,
            'rating' => 5,
            'comment' => 'Excellent quality and fast delivery.',
            'review_media' => [
                UploadedFile::fake()->createWithContent('review-photo.jpg', $pngPixel),
            ],
        ]);

    $response
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHas('success', 'Review submitted successfully.');

    $review = Review::query()->first();

    expect($review)->not->toBeNull();
    expect((int) $review->product_id)->toBe($product->id);
    expect((int) $review->user_id)->toBe($buyer->id);
    expect((int) $review->order_item_id)->toBe($orderItem->id);
    expect($review->comment)->toBe('Excellent quality and fast delivery.');
    expect($review->media()->count())->toBe(1);

    Storage::disk('public')->assertExists($review->image_path);
});

test('buyer cannot review an order item twice', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create([
        'is_seller' => true,
    ]);

    $seller->sellerProfile()->create([
        'store_name' => 'Duplicate Review Shop',
        'store_description' => 'Visible seller.',
        'contact_number' => '09174445555',
        'address' => 'Duplicate Review Address',
        'application_status' => Seller::STATUS_APPROVED,
        'shop_status' => Seller::SHOP_STATUS_OPEN,
    ]);

    $category = Category::create([
        'name' => 'Duplicate Review Category',
        'slug' => 'duplicate-review-category',
    ]);

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Duplicate Review Product',
        'category_id' => $category->id,
        'description' => 'Already reviewed once.',
        'price' => 380.00,
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

    $order = Order::create([
        'user_id' => $buyer->id,
        'seller_id' => $seller->id,
        'shipping_fee' => 50.00,
        'total_price' => 430.00,
        'status' => Order::STATUS_COMPLETED,
        'shipping_status' => Order::SHIPPING_COMPLETED,
        'payment_method' => Order::PAYMENT_METHOD_COD,
        'payment_status' => Order::PAYMENT_PAID,
        'paid_at' => now(),
        'seller_earning_status' => Order::EARNING_AVAILABLE,
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 380.00,
        'shipping_fee' => 50.00,
    ]);

    Review::create([
        'product_id' => $product->id,
        'user_id' => $buyer->id,
        'order_item_id' => $orderItem->id,
        'rating' => 4,
        'comment' => 'Already reviewed.',
    ]);

    $this
        ->actingAs($buyer)
        ->post(route('products.reviews.store', $product), [
            'order_item_id' => $orderItem->id,
            'rating' => 5,
            'comment' => 'Trying again.',
        ])
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHas('error', 'You can only review products from your completed purchases, once per order item.');

    expect(Review::query()->count())->toBe(1);
});
