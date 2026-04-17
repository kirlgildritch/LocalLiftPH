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