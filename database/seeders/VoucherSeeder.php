<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::query()->updateOrCreate(
            ['code' => 'WELCOME50'],
            [
                'name' => 'Welcome PHP 50 Off',
                'type' => Voucher::TYPE_FIXED,
                'value' => 50,
                'minimum_subtotal' => 100,
                'per_user_limit' => 1,
                'is_active' => true,
            ]
        );

        Voucher::query()->updateOrCreate(
            ['code' => 'LOCAL10'],
            [
                'name' => 'LocalLift 10 Percent Off',
                'type' => Voucher::TYPE_PERCENT,
                'value' => 10,
                'minimum_subtotal' => 200,
                'maximum_discount' => 150,
                'is_active' => true,
            ]
        );
    }
}
