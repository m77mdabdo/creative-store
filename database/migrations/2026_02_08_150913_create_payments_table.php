<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->restrictOnDelete();

            $table->string('gateway', 40);
            $table->string('transaction_id', 120)->nullable();
            $table->string('idempotency_key', 120)->nullable();

            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('SAR');
            $table->decimal('exchange_rate', 14, 6)->default(1.000000);

            $table->enum('status', ['pending','authorized','captured','failed','refunded','partially_refunded'])
                ->default('pending');

            $table->json('raw_response')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->unique(['gateway','transaction_id']);
            $table->index('idempotency_key');
            $table->index(['order_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
