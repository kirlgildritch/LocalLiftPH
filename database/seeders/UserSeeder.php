<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = [
            ['name' => 'Tech Hub Store', 'email' => 'tech@test.com'],
            ['name' => 'Urban Style Shop', 'email' => 'urban@test.com'],
            ['name' => 'Home Essentials', 'email' => 'home@test.com'],
            ['name' => 'Gadget World', 'email' => 'gadget@test.com'],
        ];

        foreach ($sellers as $seller) {
            User::firstOrCreate(
                ['email' => $seller['email']],
                [
                    'name' => $seller['name'],
                    'password' => Hash::make('12345678'),
                ]
            );
        }
    }
}