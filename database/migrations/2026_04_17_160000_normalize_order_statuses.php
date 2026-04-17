<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('status', 'to ship')
            ->update(['status' => 'processing']);

        DB::table('orders')
            ->where('status', 'to receive')
            ->update(['status' => 'shipped']);
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('status', 'processing')
            ->update(['status' => 'to ship']);

        DB::table('orders')
            ->where('status', 'shipped')
            ->update(['status' => 'to receive']);
    }
};
