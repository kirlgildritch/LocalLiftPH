<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MarketplaceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::updateOrCreate(
            ['email' => 'buyer.demo@locallift.test'],
            [
                'name' => 'Demo Buyer',
                'password' => Hash::make('password'),
                'is_seller' => false,
                'is_admin' => false,
                'phone' => '09170000001',
                'address' => 'Quezon City, Metro Manila',
            ]
        );

        $sellerProfiles = [
            [
                'user' => [
                    'name' => 'Mira Santos',
                    'email' => 'seller.mira@locallift.test',
                    'phone' => '09170000011',
                    'address' => 'Baguio City, Benguet',
                ],
                'shop' => [
                    'seller_type' => 'individual',
                    'full_name' => 'Mira Santos',
                    'age' => 29,
                    'email' => 'seller.mira@locallift.test',
                    'store_name' => 'Northcraft Studio',
                    'store_description' => 'Handmade woven pieces, home accents, and local artisan gift items.',
                    'contact_number' => '09170000011',
                    'address' => 'Baguio City, Benguet',
                    'valid_id_type' => 'National ID',
                    'valid_id_number' => 'MIRA-1001',
                    'application_status' => 'approved',
                    'submitted_at' => now(),
                    'reviewed_at' => now(),
                ],
                'products' => [
                    ['name' => 'Handwoven Table Runner', 'category' => 'Home & Living', 'price' => 780, 'stock' => 12, 'description' => 'A textured handwoven table runner for cozy dining setups.'],
                    ['name' => 'Cord Tote Bag', 'category' => 'Bags', 'price' => 950, 'stock' => 8, 'description' => 'A roomy everyday tote made from thick woven cord.'],
                    ['name' => 'Mountain Scent Candle', 'category' => 'Home & Living', 'price' => 320, 'stock' => 20, 'description' => 'Soy candle with pine and cedar notes inspired by cool mountain mornings.'],
                ],
                'messages' => [
                    'Hi Mira, is the table runner available in another color?',
                    'Yes, I have a clay-beige version and a deep blue version ready to ship.',
                    'Great, I may order two pieces this week.',
                ],
            ],
            [
                'user' => [
                    'name' => 'Carlo Dela Cruz',
                    'email' => 'seller.carlo@locallift.test',
                    'phone' => '09170000022',
                    'address' => 'Marikina City, Metro Manila',
                ],
                'shop' => [
                    'seller_type' => 'registered_business',
                    'full_name' => 'Carlo Dela Cruz',
                    'age' => 35,
                    'email' => 'seller.carlo@locallift.test',
                    'store_name' => 'Marikina Sole Works',
                    'store_description' => 'Locally crafted footwear and leather essentials made in Marikina.',
                    'contact_number' => '09170000022',
                    'address' => 'Marikina City, Metro Manila',
                    'valid_id_type' => 'Driver\'s License',
                    'valid_id_number' => 'CARLO-2044',
                    'application_status' => 'approved',
                    'submitted_at' => now(),
                    'reviewed_at' => now(),
                ],
                'products' => [
                    ['name' => 'Leather Slip-On Sandals', 'category' => 'Shoes', 'price' => 1450, 'stock' => 10, 'description' => 'Minimal leather sandals with padded footbed and durable sole.'],
                    ['name' => 'Classic Card Wallet', 'category' => 'Accessories', 'price' => 680, 'stock' => 18, 'description' => 'Compact handmade wallet with four card slots.'],
                    ['name' => 'Weekend Loafers', 'category' => 'Shoes', 'price' => 2250, 'stock' => 6, 'description' => 'Casual loafers built for daily wear and smart casual outfits.'],
                ],
                'messages' => [
                    'Hello Carlo, do your loafers run true to size?',
                    'They fit true to size, but I can also suggest the best size if you send your foot length.',
                    'Thanks, I will send my size details later.',
                ],
            ],
            [
                'user' => [
                    'name' => 'Alyssa Reyes',
                    'email' => 'seller.alyssa@locallift.test',
                    'phone' => '09170000033',
                    'address' => 'Cebu City, Cebu',
                ],
                'shop' => [
                    'seller_type' => 'individual',
                    'full_name' => 'Alyssa Reyes',
                    'age' => 31,
                    'email' => 'seller.alyssa@locallift.test',
                    'store_name' => 'Cebu Pantry Finds',
                    'store_description' => 'Small-batch pantry goods, snack boxes, and giftable local food picks.',
                    'contact_number' => '09170000033',
                    'address' => 'Cebu City, Cebu',
                    'valid_id_type' => 'Passport',
                    'valid_id_number' => 'ALYSSA-1888',
                    'application_status' => 'approved',
                    'submitted_at' => now(),
                    'reviewed_at' => now(),
                ],
                'products' => [
                    ['name' => 'Otap Snack Box', 'category' => 'Food', 'price' => 260, 'stock' => 24, 'description' => 'A shareable box of crisp sweet otap pastries.'],
                    ['name' => 'Calamansi Honey Spread', 'category' => 'Food', 'price' => 210, 'stock' => 16, 'description' => 'Bright citrus honey spread for bread, tea, and desserts.'],
                    ['name' => 'Dried Mango Gift Pack', 'category' => 'Souvenirs', 'price' => 340, 'stock' => 14, 'description' => 'Gift-ready pack of chewy Cebu dried mango slices.'],
                ],
                'messages' => [
                    'Hi Alyssa, can you ship the snack box tomorrow?',
                    'Yes, I can ship tomorrow morning if the order is confirmed tonight.',
                    'Perfect, I am checking out the options now.',
                ],
            ],
        ];

        foreach ($sellerProfiles as $profile) {
            $sellerUser = User::updateOrCreate(
                ['email' => $profile['user']['email']],
                [
                    'name' => $profile['user']['name'],
                    'password' => Hash::make('password'),
                    'is_seller' => true,
                    'is_admin' => false,
                    'phone' => $profile['user']['phone'],
                    'address' => $profile['user']['address'],
                ]
            );

            Seller::updateOrCreate(
                ['user_id' => $sellerUser->id],
                $profile['shop']
            );

            foreach ($profile['products'] as $productData) {
                $category = Category::where('name', $productData['category'])->first();

                Product::updateOrCreate(
                    [
                        'user_id' => $sellerUser->id,
                        'name' => $productData['name'],
                    ],
                    [
                        'category_id' => $category?->id,
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'stock' => $productData['stock'],
                        'condition' => 'new',
                        'weight' => 0.50,
                        'width_cm' => 18.00,
                        'length_cm' => 24.00,
                        'height_cm' => 8.00,
                        'shipping_fee' => 120.00,
                        'is_active' => true,
                        'status' => 'approved',
                    ]
                );
            }

            $conversation = Conversation::firstOrCreate([
                'buyer_id' => $buyer->id,
                'seller_id' => $sellerUser->id,
            ]);

            if (! $conversation->messages()->exists()) {
                foreach ($profile['messages'] as $index => $text) {
                    Message::create([
                        'conversation_id' => $conversation->id,
                        'sender_id' => $index % 2 === 0 ? $buyer->id : $sellerUser->id,
                        'message' => $text,
                        'created_at' => now()->subMinutes(15 - ($index * 4)),
                        'updated_at' => now()->subMinutes(15 - ($index * 4)),
                    ]);
                }

                $conversation->touch();
            }
        }
    }
}
