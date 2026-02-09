<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->restrictOnDelete();
            $table->foreignId('return_id')->nullable()->constrained('returns')->nullOnDelete();

            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending','processed','failed'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
