<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('messages')) {
            return;
        }

        DB::statement('ALTER TABLE messages MODIFY message TEXT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('messages')) {
            return;
        }

        DB::statement("UPDATE messages SET message = '' WHERE message IS NULL");
        DB::statement('ALTER TABLE messages MODIFY message TEXT NOT NULL');
    }
};
