<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('shipment_tracking_logs', function (Blueprint $table) {
    $table->id();

    $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();

    $table->string('status')->nullable(); // shipped, in_transit, delivered...
    $table->string('location')->nullable();
    $table->string('carrier_status')->nullable();

    $table->text('note')->nullable();

    $table->timestamp('tracked_at')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking_logs');
    }
};
