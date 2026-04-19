<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('condition')->nullable()->after('stock');
            $table->decimal('weight', 8, 2)->nullable()->after('condition');
            $table->decimal('width_cm', 8, 2)->nullable()->after('weight');
            $table->decimal('length_cm', 8, 2)->nullable()->after('width_cm');
            $table->decimal('height_cm', 8, 2)->nullable()->after('length_cm');
            $table->decimal('shipping_fee', 10, 2)->nullable()->after('height_cm');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'condition',
                'weight',
                'width_cm',
                'length_cm',
                'height_cm',
                'shipping_fee',
            ]);
        });
    }
};
