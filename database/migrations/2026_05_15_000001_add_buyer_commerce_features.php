<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('shop_follows')) {
            Schema::create('shop_follows', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('seller_user_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['user_id', 'seller_user_id']);
            });
        }

        if (! Schema::hasTable('recently_viewed_products')) {
            Schema::create('recently_viewed_products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->timestamps();

                $table->unique(['user_id', 'product_id']);
                $table->index(['user_id', 'updated_at']);
            });
        } else {
            Schema::table('recently_viewed_products', function (Blueprint $table) {
                if (! Schema::hasColumn('recently_viewed_products', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }

                if (! Schema::hasColumn('recently_viewed_products', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->index();
                }
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'discount_type')) {
                $table->string('discount_type', 20)->nullable()->after('shipping_fee');
            }

            if (! Schema::hasColumn('products', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'voucher_code')) {
                $table->string('voucher_code')->nullable()->after('shipping_fee');
            }

            if (! Schema::hasColumn('orders', 'voucher_discount')) {
                $table->decimal('voucher_discount', 10, 2)->default(0)->after('voucher_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = collect(['voucher_code', 'voucher_discount'])
                ->filter(fn ($column) => Schema::hasColumn('orders', $column))
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $columns = collect(['discount_type', 'discount_value'])
                ->filter(fn ($column) => Schema::hasColumn('products', $column))
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::dropIfExists('recently_viewed_products');
        Schema::dropIfExists('shop_follows');
    }
};
