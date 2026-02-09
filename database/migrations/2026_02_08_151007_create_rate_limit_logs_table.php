<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rate_limit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('key',190);
            $table->string('endpoint',120);
            $table->unsignedInteger('hits')->default(0);
            $table->timestamp('window_start');
            $table->timestamp('blocked_until')->nullable();
            $table->timestamps();

            $table->index(['key','endpoint','window_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_limit_logs');
    }
};
