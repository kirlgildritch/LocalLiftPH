<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->unsignedInteger('low_stock_threshold')->default(5)->after('payout_account_number');
            $table->boolean('hide_out_of_stock')->default(false)->after('low_stock_threshold');
            $table->string('shop_status')->default('open')->after('hide_out_of_stock');
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn([
                'low_stock_threshold',
                'hide_out_of_stock',
                'shop_status',
            ]);
        });
    }
};
