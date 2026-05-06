<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'products_status_created_at_index');
            $table->index(['status', 'base_price'], 'products_status_base_price_index');
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->index(['status', 'published_at'], 'blogs_status_published_at_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'payment_status', 'placed_at'], 'orders_status_payment_placed_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_created_at_index');
            $table->dropIndex('products_status_base_price_index');
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex('blogs_status_published_at_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_payment_placed_index');
        });
    }
};
