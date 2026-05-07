<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function updateProductIdNullability(bool $nullable): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('order_items', function (Blueprint $table) use ($nullable) {
                $column = $table->unsignedBigInteger('product_id');

                if ($nullable) {
                    $column->nullable();
                } else {
                    $column->nullable(false);
                }

                $column->change();
            });

            return;
        }

        DB::statement(
            sprintf(
                'ALTER TABLE order_items MODIFY product_id BIGINT UNSIGNED %s',
                $nullable ? 'NULL' : 'NOT NULL'
            )
        );
    }

    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        $this->updateProductIdNullability(true);

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        DB::table('order_items')->whereNull('product_id')->delete();

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        $this->updateProductIdNullability(false);

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }
};
