<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('comment');
            $table->string('video_path')->nullable()->after('image_path');
            $table->text('seller_reply')->nullable()->after('video_path');
            $table->timestamp('seller_replied_at')->nullable()->after('seller_reply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'image_path',
                'video_path',
                'seller_reply',
                'seller_replied_at',
            ]);
        });
    }
};
