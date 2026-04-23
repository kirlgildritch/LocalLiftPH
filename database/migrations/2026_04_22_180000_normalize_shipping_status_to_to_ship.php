<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('shipping_status', 'pending')
            ->update(['shipping_status' => 'to_ship']);

        DB::table('orders')
            ->where('status', 'pending')
            ->where('shipping_status', 'to_ship')
            ->update(['status' => 'processing']);
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('shipping_status', 'to_ship')
            ->where('status', 'processing')
            ->update([
                'shipping_status' => 'pending',
                'status' => 'pending',
            ]);
    }
};
