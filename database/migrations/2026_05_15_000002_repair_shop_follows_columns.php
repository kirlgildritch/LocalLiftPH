<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('shop_follows')) {
            return;
        }

        if (! Schema::hasColumn('shop_follows', 'seller_user_id')) {
            Schema::table('shop_follows', function (Blueprint $table) {
                $table->foreignId('seller_user_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            });
        }

        if (Schema::hasColumn('shop_follows', 'seller_id')) {
            DB::table('shop_follows')
                ->whereNull('seller_user_id')
                ->update(['seller_user_id' => DB::raw('seller_id')]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('shop_follows') || ! Schema::hasColumn('shop_follows', 'seller_user_id')) {
            return;
        }

        Schema::table('shop_follows', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_user_id');
        });
    }
};
