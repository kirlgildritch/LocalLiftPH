<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'orders_user_created_at_index');
            $table->index(['seller_id', 'shipping_status', 'created_at'], 'orders_seller_shipping_created_at_index');
            $table->index(['seller_earning_status', 'seller_payout_id'], 'orders_earning_payout_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['order_id', 'product_id'], 'order_items_order_product_index');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'reports_status_created_at_index');
            $table->index(['product_id', 'status'], 'reports_product_status_index');
            $table->index(['seller_id', 'status'], 'reports_seller_status_index');
        });

        Schema::table('seller_payouts', function (Blueprint $table) {
            $table->index(['seller_id', 'status', 'requested_at'], 'payouts_seller_status_requested_index');
            $table->index(['status', 'requested_at'], 'payouts_status_requested_index');
        });

        Schema::table('seller_document_requests', function (Blueprint $table) {
            $table->index(['seller_id', 'status', 'requested_at'], 'doc_requests_seller_status_requested_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'reviews_user_created_at_index');
        });

        Schema::table('order_return_requests', function (Blueprint $table) {
            $table->index(['seller_id', 'status', 'requested_at'], 'returns_seller_status_requested_index');
            $table->index(['user_id', 'status', 'requested_at'], 'returns_user_status_requested_index');
        });
    }

    public function down(): void
    {
        Schema::table('order_return_requests', function (Blueprint $table) {
            $table->dropIndex('returns_user_status_requested_index');
            $table->dropIndex('returns_seller_status_requested_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_user_created_at_index');
        });

        Schema::table('seller_document_requests', function (Blueprint $table) {
            $table->dropIndex('doc_requests_seller_status_requested_index');
        });

        Schema::table('seller_payouts', function (Blueprint $table) {
            $table->dropIndex('payouts_status_requested_index');
            $table->dropIndex('payouts_seller_status_requested_index');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_seller_status_index');
            $table->dropIndex('reports_product_status_index');
            $table->dropIndex('reports_status_created_at_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_order_product_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_earning_payout_index');
            $table->dropIndex('orders_seller_shipping_created_at_index');
            $table->dropIndex('orders_user_created_at_index');
        });
    }
};
