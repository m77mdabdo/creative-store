<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('attempt_no')->default(1);

            $table->enum('status', [
                'scheduled',
                'out_for_delivery',
                'delivered',
                'failed',
                'rescheduled',
                'cancelled',
            ])->default('scheduled');

            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('location')->nullable();

            $table->string('failure_reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('next_attempt_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_attempts');
    }
};
