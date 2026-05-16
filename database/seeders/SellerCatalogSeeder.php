<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SellerCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()
            ->whereIn('name', [
                'Home & Living',
                'Kitchenware',
                'Gift Sets',
            ])
            ->get()
            ->keyBy('name');

        $sellers = [
            [
                'user' => [
                    'name' => 'Cebu Home Finds',
                    'email' => 'cebuhome@locallift.test',
                    'phone' => '09171234567',
                ],
                'store' => [
                    'store_name' => 'Cebu Home Finds',
                    'store_description' => 'Affordable home essentials and stylish decor.',
                ],
                'products' => [
                    [
                        'name' => 'Handwoven Storage Basket',
                        'category' => 'Home & Living',
                        'description' => 'A sturdy handwoven basket for organizing household items.',
                        'price' => 349,
                        'stock' => 18,
                        'weight' => 0.80,
                        'width_cm' => 28,
                        'length_cm' => 28,
                        'height_cm' => 18,
                    ],
                    [
                        'name' => 'Minimalist Table Lamp',
                        'category' => 'Home & Living',
                        'description' => 'Modern bedside lamp with warm lighting.',
                        'price' => 699,
                        'stock' => 10,
                        'weight' => 1.20,
                        'width_cm' => 18,
                        'length_cm' => 18,
                        'height_cm' => 35,
                    ],
                    [
                        'name' => 'Decorative Wall Clock',
                        'category' => 'Home & Living',
                        'description' => 'Elegant wall clock perfect for living rooms.',
                        'price' => 899,
                        'stock' => 7,
                        'weight' => 1.50,
                        'width_cm' => 40,
                        'length_cm' => 40,
                        'height_cm' => 5,
                    ],
                ],
            ],

            [
                'user' => [
                    'name' => 'Kitchen Corner',
                    'email' => 'kitchen@locallift.test',
                    'phone' => '09181234567',
                ],
                'store' => [
                    'store_name' => 'Kitchen Corner',
                    'store_description' => 'Premium kitchen tools and cookware.',
                ],
                'products' => [
                    [
                        'name' => 'Bamboo Serving Tray',
                        'category' => 'Kitchenware',
                        'description' => 'Stylish bamboo serving tray for drinks and snacks.',
                        'price' => 429,
                        'stock' => 12,
                        'weight' => 1.10,
                        'width_cm' => 35,
                        'length_cm' => 24,
                        'height_cm' => 5,
                    ],
                    [
                        'name' => 'Ceramic Coffee Mug Set',
                        'category' => 'Kitchenware',
                        'description' => 'Set of 4 ceramic mugs with minimalist design.',
                        'price' => 799,
                        'stock' => 14,
                        'weight' => 1.80,
                        'width_cm' => 24,
                        'length_cm' => 24,
                        'height_cm' => 12,
                    ],
                    [
                        'name' => 'Nonstick Frying Pan',
                        'category' => 'Kitchenware',
                        'description' => 'Durable nonstick frying pan for everyday cooking.',
                        'price' => 1250,
                        'stock' => 9,
                        'weight' => 2.10,
                        'width_cm' => 32,
                        'length_cm' => 32,
                        'height_cm' => 8,
                    ],
                ],
            ],

            [
                'user' => [
                    'name' => 'Gift Haven',
                    'email' => 'gift@locallift.test',
                    'phone' => '09192345678',
                ],
                'store' => [
                    'store_name' => 'Gift Haven',
                    'store_description' => 'Curated gift boxes for all occasions.',
                ],
                'products' => [
                    [
                        'name' => 'Cebu Welcome Gift Set',
                        'category' => 'Gift Sets',
                        'description' => 'A locally inspired welcome gift package.',
                        'price' => 799,
                        'stock' => 9,
                        'weight' => 1.60,
                        'width_cm' => 32,
                        'length_cm' => 26,
                        'height_cm' => 12,
                    ],
                    [
                        'name' => 'Self Care Package',
                        'category' => 'Gift Sets',
                        'description' => 'Relaxing self-care essentials gift package.',
                        'price' => 999,
                        'stock' => 6,
                        'weight' => 2.00,
                        'width_cm' => 30,
                        'length_cm' => 24,
                        'height_cm' => 14,
                    ],
                    [
                        'name' => 'Birthday Surprise Box',
                        'category' => 'Gift Sets',
                        'description' => 'Colorful birthday-themed gift set.',
                        'price' => 1199,
                        'stock' => 8,
                        'weight' => 2.50,
                        'width_cm' => 35,
                        'length_cm' => 28,
                        'height_cm' => 15,
                    ],
                ],
            ],

            [
                'user' => [
                    'name' => 'Urban Living',
                    'email' => 'urban@locallift.test',
                    'phone' => '09173456789',
                ],
                'store' => [
                    'store_name' => 'Urban Living',
                    'store_description' => 'Modern home and apartment essentials.',
                ],
                'products' => [
                    [
                        'name' => 'Foldable Laundry Basket',
                        'category' => 'Home & Living',
                        'description' => 'Space-saving foldable laundry organizer.',
                        'price' => 499,
                        'stock' => 20,
                        'weight' => 0.90,
                        'width_cm' => 30,
                        'length_cm' => 30,
                        'height_cm' => 40,
                    ],
                    [
                        'name' => 'Wooden Floating Shelf',
                        'category' => 'Home & Living',
                        'description' => 'Minimalist floating shelf for decor and storage.',
                        'price' => 899,
                        'stock' => 11,
                        'weight' => 2.40,
                        'width_cm' => 60,
                        'length_cm' => 20,
                        'height_cm' => 8,
                    ],
                ],
            ],

            [
                'user' => [
                    'name' => 'Daily Kitchen PH',
                    'email' => 'dailykitchen@locallift.test',
                    'phone' => '09174567890',
                ],
                'store' => [
                    'store_name' => 'Daily Kitchen PH',
                    'store_description' => 'Kitchen essentials for everyday cooking.',
                ],
                'products' => [
                    [
                        'name' => 'Knife Set with Holder',
                        'category' => 'Kitchenware',
                        'description' => 'Premium stainless kitchen knife set.',
                        'price' => 1499,
                        'stock' => 5,
                        'weight' => 2.20,
                        'width_cm' => 20,
                        'length_cm' => 15,
                        'height_cm' => 35,
                    ],
                    [
                        'name' => 'Glass Food Container Set',
                        'category' => 'Kitchenware',
                        'description' => 'Reusable glass storage containers with lids.',
                        'price' => 899,
                        'stock' => 13,
                        'weight' => 2.70,
                        'width_cm' => 32,
                        'length_cm' => 24,
                        'height_cm' => 16,
                    ],
                ],
            ],
        ];

        foreach ($sellers as $sellerData) {

            $user = User::updateOrCreate(
                ['email' => $sellerData['user']['email']],
                [
                    'name' => $sellerData['user']['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'phone' => $sellerData['user']['phone'],
                    'address' => 'Cebu City, Cebu',
                    'is_seller' => true,
                    'is_admin' => false,
                    'role' => 'seller',
                ]
            );

            Seller::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'seller_type' => 'individual',
                    'full_name' => $sellerData['user']['name'],
                    'age' => rand(22, 40),
                    'email' => $sellerData['user']['email'],
                    'store_name' => $sellerData['store']['store_name'],
                    'store_description' => $sellerData['store']['store_description'],
                    'contact_number' => $sellerData['user']['phone'],
                    'address' => 'Cebu City, Cebu',
                    'street_address' => 'Sample Street',
                    'barangay' => 'Barangay Apas',
                    'city' => 'Cebu City',
                    'province' => 'Cebu',
                    'region' => 'Central Visayas',
                    'postal_code' => '6000',
                    'landmark' => 'Near IT Park',
                    'payout_method' => 'gcash',
                    'payout_account_name' => $sellerData['user']['name'],
                    'payout_account_number' => $sellerData['user']['phone'],
                    'low_stock_threshold' => 5,
                    'hide_out_of_stock' => false,
                    'shop_status' => Seller::SHOP_STATUS_OPEN,
                    'application_status' => Seller::STATUS_APPROVED,
                    'submitted_at' => now()->subDays(14),
                    'reviewed_at' => now()->subDays(13),
                ]
            );

            foreach ($sellerData['products'] as $item) {

                $category = $categories->get($item['category']);

                if (!$category) {
                    continue;
                }

                Product::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $item['name'],
                    ],
                    [
                        'category_id' => $category->id,
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'stock' => $item['stock'],
                        'condition' => 'new',
                        'weight' => $item['weight'],
                        'width_cm' => $item['width_cm'],
                        'length_cm' => $item['length_cm'],
                        'height_cm' => $item['height_cm'],
                        'shipping_fee' => $this->calculateShippingFee(
                            $item['weight'],
                            $item['width_cm'],
                            $item['length_cm'],
                            $item['height_cm']
                        ),
                        'image' => null,
                        'is_active' => true,
                        'status' => Product::STATUS_APPROVED,
                        'rejection_reason' => null,
                    ]
                );
            }
        }
    }

    private function calculateShippingFee(
        float $weight,
        float $widthCm,
        float $lengthCm,
        float $heightCm
    ): float {
        $volumetricWeight = ($widthCm * $lengthCm * $heightCm) / 5000;

        $billableWeight = max($weight, $volumetricWeight);

        return round(60 + ($billableWeight * 35), 2);
    }
}