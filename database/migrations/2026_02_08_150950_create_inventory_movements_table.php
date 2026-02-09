<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();

            $table->enum('type', ['restock','sale','return','adjustment','reservation','release']);
            $table->integer('quantity');

            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('note')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['variant_id','created_at','type']);
            $table->index(['reference_type','reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
