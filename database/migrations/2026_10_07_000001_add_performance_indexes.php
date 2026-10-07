<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->index('order_status', 'idx_orders_status');
            $table->index('customer_phone', 'idx_orders_phone');
            $table->index('payment_status', 'idx_orders_payment_status');
            $table->index('created_at', 'idx_orders_created_at');
            $table->index(['order_status', 'created_at'], 'idx_orders_status_created');
        });

        // Add indexes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'is_featured'], 'idx_products_active_featured');
            $table->index(['is_active', 'is_trending'], 'idx_products_active_trending');
            $table->index(['is_active', 'created_at'], 'idx_products_active_created');
            $table->index(['is_active', 'category_id'], 'idx_products_active_category');
        });

        // Add indexes to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'idx_categories_active_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_phone');
            $table->dropIndex('idx_orders_payment_status');
            $table->dropIndex('idx_orders_created_at');
            $table->dropIndex('idx_orders_status_created');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_active_featured');
            $table->dropIndex('idx_products_active_trending');
            $table->dropIndex('idx_products_active_created');
            $table->dropIndex('idx_products_active_category');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_active_sort');
        });
    }
};
