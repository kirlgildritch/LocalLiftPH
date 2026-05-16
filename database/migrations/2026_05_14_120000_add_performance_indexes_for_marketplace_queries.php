<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['status', 'is_active', 'created_at'], 'products_visibility_sort_index');
            $table->index(['user_id', 'status', 'is_active'], 'products_seller_visibility_index');
        });

        Schema::table('sellers', function (Blueprint $table) {
            $table->index(
                ['application_status', 'suspended_at', 'shop_status', 'shop_status_until'],
                'sellers_marketplace_visibility_index'
            );
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->index(['buyer_id', 'updated_at'], 'conversations_buyer_updated_at_index');
            $table->index(['seller_id', 'updated_at'], 'conversations_seller_updated_at_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['conversation_id', 'created_at'], 'messages_conversation_created_at_index');
            $table->index(['conversation_id', 'read_at', 'sender_id'], 'messages_unread_lookup_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(
                ['notifiable_type', 'notifiable_id', 'read_at', 'created_at'],
                'notifications_notifiable_read_at_created_at_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_read_at_created_at_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_unread_lookup_index');
            $table->dropIndex('messages_conversation_created_at_index');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex('conversations_seller_updated_at_index');
            $table->dropIndex('conversations_buyer_updated_at_index');
        });

        Schema::table('sellers', function (Blueprint $table) {
            $table->dropIndex('sellers_marketplace_visibility_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_seller_visibility_index');
            $table->dropIndex('products_visibility_sort_index');
        });
    }
};
