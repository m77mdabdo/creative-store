<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_variant_values', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();
            $table->timestamps();

            // ✅ أسماء قصيرة للـ unique indexes
            $table->unique(['product_variant_id','attribute_id'], 'uq_pvv_variant_attr');
            $table->unique(['product_variant_id','attribute_value_id'], 'uq_pvv_variant_value');

            // ❌ احذفي الثلاثي لأنه يطوّل اسم الـ index ومش لازم
            // $table->unique(['product_variant_id','attribute_id','attribute_value_id'], 'uq_pvv_v_a_v');
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_variant_values');
    }
};
