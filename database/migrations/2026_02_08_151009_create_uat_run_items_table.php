<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uat_run_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uat_run_id')->constrained('uat_runs')->cascadeOnDelete();
            $table->string('scenario_code',80);
            $table->string('title',180);
            $table->enum('status',['pending','passed','failed'])->default('pending');
            $table->text('evidence')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uat_run_items');
    }
};
