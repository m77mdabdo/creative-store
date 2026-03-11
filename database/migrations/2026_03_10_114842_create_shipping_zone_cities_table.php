<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zone_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')
                ->constrained('shipping_zones')
                ->cascadeOnDelete();

            $table->string('city');
            $table->string('district')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['city', 'district']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zone_cities');
    }
};
