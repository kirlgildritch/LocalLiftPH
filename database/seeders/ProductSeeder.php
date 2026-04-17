<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Earbuds',
                'category' => 'Electronics',
                'price' => 999,
                'stock' => 20,
                'description' => 'High quality sound'
            ],
            [
                'name' => 'Bluetooth Speaker',
                'category' => 'Electronics',
                'price' => 1200,
                'stock' => 15,
                'description' => 'Loud and clear sound'
            ],
            [
                'name' => 'Tote Bag',
                'category' => 'Fashion',
                'price' => 250,
                'stock' => 30,
                'description' => 'Minimalist design'
            ],
            [
                'name' => 'Backpack',
                'category' => 'Fashion',
                'price' => 800,
                'stock' => 10,
                'description' => 'Durable and stylish'
            ],
            [
                'name' => 'Desk Lamp',
                'category' => 'Home',
                'price' => 500,
                'stock' => 25,
                'description' => 'Bright LED light'
            ],
        ];

        $users = User::all();

        foreach ($users as $user) {
            foreach ($products as $product) {
                Product::create([
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'description' => $product['description'],
                    'user_id' => $user->id,
                    'is_active' => 1,
                ]);
            }
        }
    }
}