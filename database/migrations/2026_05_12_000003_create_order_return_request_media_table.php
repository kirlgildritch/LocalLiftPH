<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_return_request_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_return_request_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_return_request_media');
    }
};
