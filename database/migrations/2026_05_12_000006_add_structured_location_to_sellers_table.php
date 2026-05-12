<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->string('street_address')->nullable()->after('address');
            $table->string('barangay')->nullable()->after('street_address');
            $table->string('city')->nullable()->after('barangay');
            $table->string('province')->nullable()->after('city');
            $table->string('region')->nullable()->after('province');
            $table->string('postal_code', 20)->nullable()->after('region');
            $table->string('landmark')->nullable()->after('postal_code');

            $table->index(['province', 'city'], 'sellers_province_city_index');
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropIndex('sellers_province_city_index');
            $table->dropColumn([
                'street_address',
                'barangay',
                'city',
                'province',
                'region',
                'postal_code',
                'landmark',
            ]);
        });
    }
};
