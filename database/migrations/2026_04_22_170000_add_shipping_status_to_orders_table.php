<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_status')->default('pending')->after('status');
        });

        DB::table('orders')->update([
            'shipping_status' => DB::raw("
                CASE
                    WHEN status IN ('confirmed', 'processing') THEN 'to_ship'
                    WHEN status = 'shipped' THEN 'shipped'
                    WHEN status = 'delivered' THEN 'delivered'
                    WHEN status = 'cancelled' THEN 'cancelled'
                    ELSE 'pending'
                END
            "),
        ]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_status');
        });
    }
};
