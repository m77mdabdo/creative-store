<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_cart_recoveries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('email')->nullable();
            $table->string('session_id')->nullable();

            $table->enum('channel', ['email'])->default('email');

            $table->enum('status', [
                'pending',
                'sent',
                'failed',
                'recovered',
                'expired',
            ])->default('pending');

            $table->string('recovery_token', 120)->unique();

            $table->timestamp('cart_abandoned_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('recovered_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->text('note')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['cart_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['email', 'status']);
            $table->index(['session_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_cart_recoveries');
    }
};
