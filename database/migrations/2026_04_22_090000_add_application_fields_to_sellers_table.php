<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->string('seller_type')->nullable()->after('user_id');
            $table->string('full_name')->nullable()->after('seller_type');
            $table->unsignedSmallInteger('age')->nullable()->after('full_name');
            $table->string('email')->nullable()->after('age');
            $table->string('valid_id_type')->nullable()->after('address');
            $table->string('valid_id_number')->nullable()->after('valid_id_type');
            $table->string('valid_id_path')->nullable()->after('valid_id_number');
            $table->string('business_permit_path')->nullable()->after('valid_id_path');
            $table->string('application_status')->default('pending')->after('business_permit_path');
            $table->text('review_notes')->nullable()->after('application_status');
            $table->timestamp('submitted_at')->nullable()->after('review_notes');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
        });

        DB::table('sellers')
            ->whereNotNull('store_name')
            ->update([
                'application_status' => 'approved',
                'submitted_at' => now(),
                'reviewed_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn([
                'seller_type',
                'full_name',
                'age',
                'email',
                'valid_id_type',
                'valid_id_number',
                'valid_id_path',
                'business_permit_path',
                'application_status',
                'review_notes',
                'submitted_at',
                'reviewed_at',
            ]);
        });
    }
};
