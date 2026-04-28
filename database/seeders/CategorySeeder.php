<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Main Categories
            ['name' => 'Food', 'icon' => 'fa-utensils'],
            ['name' => 'Fashion', 'icon' => 'fa-shirt'],
            ['name' => 'Beauty', 'icon' => 'fa-heart'],
            ['name' => 'Electronics', 'icon' => 'fa-mobile-screen'],
            ['name' => 'Home & Living', 'icon' => 'fa-couch'],
            ['name' => 'Bags', 'icon' => 'fa-bag-shopping'],
            ['name' => 'Shoes', 'icon' => 'fa-shoe-prints'],
            ['name' => 'Books', 'icon' => 'fa-book'],
            ['name' => 'Toys', 'icon' => 'fa-puzzle-piece'],
            ['name' => 'Pets', 'icon' => 'fa-paw'],
            ['name' => 'Accessories', 'icon' => 'fa-gem'],
            ['name' => 'Souvenirs', 'icon' => 'fa-gift'],

            // Extended E-commerce Categories
            ['name' => 'Mobile Phones', 'icon' => 'fa-mobile'],
            ['name' => 'Laptops & Computers', 'icon' => 'fa-laptop'],
            ['name' => 'Computer Accessories', 'icon' => 'fa-keyboard'],
            ['name' => 'Cameras', 'icon' => 'fa-camera'],
            ['name' => 'Audio & Headphones', 'icon' => 'fa-headphones'],
            ['name' => 'Gaming', 'icon' => 'fa-gamepad'],

            ['name' => 'Men\'s Clothing', 'icon' => 'fa-user'],
            ['name' => 'Women\'s Clothing', 'icon' => 'fa-person-dress'],
            ['name' => 'Kid\'s Clothing', 'icon' => 'fa-child'],
            ['name' => 'Watches', 'icon' => 'fa-clock'],

            ['name' => 'Makeup', 'icon' => 'fa-palette'],
            ['name' => 'Skincare', 'icon' => 'fa-spa'],
            ['name' => 'Hair Care', 'icon' => 'fa-scissors'],
            ['name' => 'Fragrances', 'icon' => 'fa-spray-can'],

            ['name' => 'Furniture', 'icon' => 'fa-chair'],
            ['name' => 'Kitchenware', 'icon' => 'fa-kitchen-set'],
            ['name' => 'Home Decor', 'icon' => 'fa-house'],
            ['name' => 'Lighting', 'icon' => 'fa-lightbulb'],

            ['name' => 'Backpacks', 'icon' => 'fa-backpack'],
            ['name' => 'Travel Bags', 'icon' => 'fa-suitcase'],
            ['name' => 'Wallets', 'icon' => 'fa-wallet'],

            ['name' => 'Sports & Fitness', 'icon' => 'fa-dumbbell'],
            ['name' => 'Outdoor & Camping', 'icon' => 'fa-campground'],
            ['name' => 'Cycling', 'icon' => 'fa-bicycle'],

            ['name' => 'Office Supplies', 'icon' => 'fa-briefcase'],
            ['name' => 'School Supplies', 'icon' => 'fa-school'],

            ['name' => 'Baby Products', 'icon' => 'fa-baby'],
            ['name' => 'Maternity', 'icon' => 'fa-person-pregnant'],

            ['name' => 'Automotive', 'icon' => 'fa-car'],
            ['name' => 'Motorcycle', 'icon' => 'fa-motorcycle'],

            ['name' => 'Health & Wellness', 'icon' => 'fa-heart-pulse'],
            ['name' => 'Medical Supplies', 'icon' => 'fa-kit-medical'],

            ['name' => 'Handmade Crafts', 'icon' => 'fa-hand-sparkles'],
            ['name' => 'Gift Sets', 'icon' => 'fa-box-open'],
            ['name' => 'Party Supplies', 'icon' => 'fa-cake-candles'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'icon' => $category['icon'],
                ]
            );
        }
    }
}