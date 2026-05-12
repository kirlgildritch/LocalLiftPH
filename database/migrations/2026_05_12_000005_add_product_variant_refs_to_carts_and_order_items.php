<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->index('user_id', 'carts_user_id_index');
            $table->index('product_id', 'carts_product_id_index');
            $table->dropUnique('carts_user_id_product_id_unique');
            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variants')
                ->nullOnDelete();
            $table->index(['user_id', 'product_id', 'product_variant_id'], 'carts_user_product_variant_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variants')
                ->nullOnDelete();
            $table->string('variant_name')->nullable()->after('product_variant_id');
            $table->json('variant_options')->nullable()->after('variant_name');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variant_id');
            $table->dropColumn(['variant_name', 'variant_options']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('carts_user_product_variant_index');
            $table->dropConstrainedForeignId('product_variant_id');
            $table->unique(['user_id', 'product_id']);
            $table->dropIndex('carts_user_id_index');
            $table->dropIndex('carts_product_id_index');
        });
    }
};
