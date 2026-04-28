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
                    ['name' => 'Handmade Flower Bouquet', 'category' => 'Gifts', 'price' => 499, 'stock' => 25, 'description' => 'A handcrafted flower bouquet made with care, perfect for gifts, home decor, and special occasions.'],
                    ['name' => 'Macrame Wall Hanging', 'category' => 'Home & Living', 'price' => 650, 'stock' => 9, 'description' => 'A boho-inspired handmade wall hanging that adds warmth and texture to any room.'],
                    ['name' => 'Woven Storage Basket', 'category' => 'Home & Living', 'price' => 890, 'stock' => 11, 'description' => 'Durable woven basket for organizing clothes, toys, towels, and home essentials.'],
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
                    ['name' => 'Leather Key Holder', 'category' => 'Accessories', 'price' => 250, 'stock' => 30, 'description' => 'Simple handmade leather key holder for everyday use.'],
                    ['name' => 'Formal Leather Belt', 'category' => 'Accessories', 'price' => 799, 'stock' => 16, 'description' => 'A clean and durable leather belt suitable for office and formal outfits.'],
                    ['name' => 'Handmade Women Flats', 'category' => 'Shoes', 'price' => 1350, 'stock' => 7, 'description' => 'Comfortable handmade flats with soft lining and flexible sole.'],
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
                    ['name' => 'Rosquillos Cookie Jar', 'category' => 'Food', 'price' => 299, 'stock' => 21, 'description' => 'Crispy ring-shaped cookies packed in a reusable jar.'],
                    ['name' => 'Tablea Chocolate Pack', 'category' => 'Food', 'price' => 180, 'stock' => 19, 'description' => 'Rich local tablea for making traditional hot chocolate drinks.'],
                    ['name' => 'Cebu Pasalubong Bundle', 'category' => 'Souvenirs', 'price' => 699, 'stock' => 10, 'description' => 'A curated bundle of Cebu snacks and treats for gifting.'],
                ],
                'messages' => [
                    'Hi Alyssa, can you ship the snack box tomorrow?',
                    'Yes, I can ship tomorrow morning if the order is confirmed tonight.',
                    'Perfect, I am checking out the options now.',
                ],
            ],
            [
                'user' => [
                    'name' => 'Jessa Villanueva',
                    'email' => 'seller.jessa@locallift.test',
                    'phone' => '09170000044',
                    'address' => 'Davao City, Davao del Sur',
                ],
                'shop' => [
                    'seller_type' => 'individual',
                    'full_name' => 'Jessa Villanueva',
                    'age' => 27,
                    'email' => 'seller.jessa@locallift.test',
                    'store_name' => 'Davao Bloom Crafts',
                    'store_description' => 'Handmade flowers, gift boxes, ribbons, and personalized craft items.',
                    'contact_number' => '09170000044',
                    'address' => 'Davao City, Davao del Sur',
                    'valid_id_type' => 'National ID',
                    'valid_id_number' => 'JESSA-3004',
                    'application_status' => 'approved',
                    'submitted_at' => now(),
                    'reviewed_at' => now(),
                ],
                'products' => [
                    ['name' => 'Handmade Satin Flower', 'category' => 'Gifts', 'price' => 120, 'stock' => 40, 'description' => 'A delicate handmade satin flower designed for bouquets, decorations, and thoughtful gifts.'],
                    ['name' => 'Mini Crochet Flower Pot', 'category' => 'Gifts', 'price' => 350, 'stock' => 18, 'description' => 'A cute handmade crochet flower pot that lasts longer than real flowers.'],
                    ['name' => 'Personalized Gift Box', 'category' => 'Gifts', 'price' => 450, 'stock' => 15, 'description' => 'A customizable gift box ideal for birthdays, anniversaries, and special surprises.'],
                    ['name' => 'Ribbon Rose Bouquet', 'category' => 'Gifts', 'price' => 599, 'stock' => 13, 'description' => 'Elegant ribbon roses arranged beautifully for romantic and memorable occasions.'],
                    ['name' => 'Handmade Greeting Card', 'category' => 'Stationery', 'price' => 95, 'stock' => 50, 'description' => 'A handmade greeting card with a clean and heartfelt design.'],
                    ['name' => 'Paper Flower Backdrop Set', 'category' => 'Party Supplies', 'price' => 850, 'stock' => 6, 'description' => 'A decorative paper flower set for birthdays, events, and photo booths.'],
                ],
                'messages' => [
                    'Hi Jessa, can you customize the flower color?',
                    'Yes, I can make it in red, pink, white, or blue.',
                    'Nice, I will order the pink one.',
                ],
            ],
            [
                'user' => [
                    'name' => 'Ramon Garcia',
                    'email' => 'seller.ramon@locallift.test',
                    'phone' => '09170000055',
                    'address' => 'Iloilo City, Iloilo',
                ],
                'shop' => [
                    'seller_type' => 'registered_business',
                    'full_name' => 'Ramon Garcia',
                    'age' => 42,
                    'email' => 'seller.ramon@locallift.test',
                    'store_name' => 'Iloilo Woodworks',
                    'store_description' => 'Locally made wooden kitchenware, organizers, and home display pieces.',
                    'contact_number' => '09170000055',
                    'address' => 'Iloilo City, Iloilo',
                    'valid_id_type' => 'UMID',
                    'valid_id_number' => 'RAMON-5005',
                    'application_status' => 'approved',
                    'submitted_at' => now(),
                    'reviewed_at' => now(),
                ],
                'products' => [
                    ['name' => 'Wooden Serving Tray', 'category' => 'Home & Living', 'price' => 720, 'stock' => 12, 'description' => 'A polished wooden serving tray for snacks, coffee, and home styling.'],
                    ['name' => 'Bamboo Utensil Set', 'category' => 'Kitchen', 'price' => 280, 'stock' => 25, 'description' => 'Eco-friendly bamboo utensils for everyday cooking and serving.'],
                    ['name' => 'Wooden Phone Stand', 'category' => 'Accessories', 'price' => 180, 'stock' => 35, 'description' => 'A compact wooden phone stand for desks and bedside tables.'],
                    ['name' => 'Rustic Spice Rack', 'category' => 'Kitchen', 'price' => 950, 'stock' => 8, 'description' => 'A rustic wooden spice rack for organizing kitchen condiments.'],
                    ['name' => 'Mini Wooden Shelf', 'category' => 'Home & Living', 'price' => 1100, 'stock' => 5, 'description' => 'A small wooden shelf for plants, books, and display items.'],
                    ['name' => 'Handcrafted Chopping Board', 'category' => 'Kitchen', 'price' => 580, 'stock' => 14, 'description' => 'A sturdy handcrafted chopping board with smooth finish.'],
                ],
                'messages' => [
                    'Hello Ramon, is the wooden tray food safe?',
                    'Yes, it is sealed with food-safe finish.',
                    'Thank you, I will add it to my cart.',
                ],
            ],
            [
                'user' => [
                    'name' => 'Lara Mendoza',
                    'email' => 'seller.lara@locallift.test',
                    'phone' => '09170000066',
                    'address' => 'Tagaytay City, Cavite',
                ],
                'shop' => [
                    'seller_type' => 'individual',
                    'full_name' => 'Lara Mendoza',
                    'age' => 26,
                    'email' => 'seller.lara@locallift.test',
                    'store_name' => 'Tagaytay Cozy Finds',
                    'store_description' => 'Cozy lifestyle products, scented items, and simple home decorations.',
                    'contact_number' => '09170000066',
                    'address' => 'Tagaytay City, Cavite',
                    'valid_id_type' => 'National ID',
                    'valid_id_number' => 'LARA-6066',
                    'application_status' => 'approved',
                    'submitted_at' => now(),
                    'reviewed_at' => now(),
                ],
                'products' => [
                    ['name' => 'Coffee Scent Candle', 'category' => 'Home & Living', 'price' => 350, 'stock' => 22, 'description' => 'A warm coffee-scented candle for a cozy and relaxing atmosphere.'],
                    ['name' => 'Knitted Mug Sleeve', 'category' => 'Accessories', 'price' => 150, 'stock' => 30, 'description' => 'A soft knitted mug sleeve that keeps drinks warm and hands comfortable.'],
                    ['name' => 'Cozy Throw Pillow Cover', 'category' => 'Home & Living', 'price' => 420, 'stock' => 17, 'description' => 'A soft pillow cover that adds comfort and style to sofas and beds.'],
                    ['name' => 'Minimal Desk Plant Pot', 'category' => 'Home & Living', 'price' => 260, 'stock' => 20, 'description' => 'A simple decorative pot for small plants and desk setups.'],
                    ['name' => 'Aesthetic Room Garland', 'category' => 'Home & Living', 'price' => 190, 'stock' => 24, 'description' => 'A lightweight garland for decorating bedrooms, shelves, and study corners.'],
                    ['name' => 'Scented Wax Melts Set', 'category' => 'Home & Living', 'price' => 299, 'stock' => 18, 'description' => 'A set of scented wax melts with calming fragrance blends.'],
                ],
                'messages' => [
                    'Hi Lara, what scent is your best seller?',
                    'Coffee scent candle is the most ordered this week.',
                    'Okay, I want to try that one.',
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

            if (!$conversation->messages()->exists()) {
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