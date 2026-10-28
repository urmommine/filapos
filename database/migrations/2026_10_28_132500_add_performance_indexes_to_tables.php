<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to optimize database performance with targeted indexes.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('payment_status', 'orders_payment_status_idx');
            $table->index('payment_method', 'orders_payment_method_idx');
            $table->index(['payment_status', 'created_at'], 'orders_status_created_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active', 'products_is_active_idx');
            $table->index(['is_active', 'category_id'], 'products_active_category_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_payment_status_idx');
            $table->dropIndex('orders_payment_method_idx');
            $table->dropIndex('orders_status_created_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_is_active_idx');
            $table->dropIndex('products_active_category_idx');
        });
    }
};
