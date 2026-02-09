<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 40);
            $table->string('event_id', 120)->nullable();
            $table->string('event_type', 120)->nullable();
            $table->json('payload');
            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['gateway','event_id']);
            $table->index(['processed','created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_logs');
    }
};
