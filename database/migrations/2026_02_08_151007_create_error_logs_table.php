<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level',20);
            $table->string('service',80)->nullable();
            $table->text('message');
            $table->json('context')->nullable();
            $table->longText('trace')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['level','created_at']);
            $table->index('resolved_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
