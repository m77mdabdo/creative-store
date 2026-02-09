<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_sales_aggregates', function (Blueprint $table) {
            $table->id();
            $table->date('day')->unique();
            $table->unsignedInteger('orders_count')->default(0);
            $table->unsignedInteger('paid_orders_count')->default(0);

            $table->decimal('gross_sales',12,2)->default(0);
            $table->decimal('discount_total',12,2)->default(0);
            $table->decimal('shipping_total',12,2)->default(0);
            $table->decimal('tax_total',12,2)->default(0);
            $table->decimal('net_sales',12,2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_sales_aggregates');
    }
};
