<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->foreignId('shipping_method_id')->constrained('shipping_methods')->cascadeOnDelete();

            $table->string('city')->nullable();
            $table->string('district')->nullable();

            $table->decimal('min_weight', 8, 2)->nullable();
            $table->decimal('max_weight', 8, 2)->nullable();

            $table->decimal('price', 12, 2);
            $table->unsignedSmallInteger('eta_min_days')->nullable();
            $table->unsignedSmallInteger('eta_max_days')->nullable();
            $table->decimal('free_shipping_threshold', 12, 2)->nullable();

            $table->timestamps();

            $table->index(['shipping_zone_id','price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
