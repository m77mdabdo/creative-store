<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label')->nullable();
            $table->string('full_name');
            $table->string('phone', 30);
            $table->string('country', 100)->default('Saudi Arabia');
            $table->string('city', 100);
            $table->string('district', 100)->nullable();
            $table->string('street', 150);
            $table->string('building_no', 50)->nullable();
            $table->string('apartment_no', 50)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id','is_default']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('addresses');
    }
};
