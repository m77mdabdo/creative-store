<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();

            $table->integer('quantity');

            $table->timestamp('expires_at');
            $table->timestamp('released_at')->nullable();

            $table->timestamps();

            $table->index(['variant_id','expires_at','released_at']);
            $table->index('cart_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_reservations');
    }
};
