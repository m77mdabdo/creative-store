<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->decimal('base_price', 12, 2);
            $table->char('currency', 3)->default('SAR');
            $table->enum('status', ['draft','active','archived'])->default('draft');
            $table->boolean('has_variants')->default(false);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('weight', 8, 2)->nullable();
            $table->boolean('is_featured')->default(false);

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status','category_id','is_featured']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
