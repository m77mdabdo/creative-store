<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('privacy_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();

            $table->enum('type',['export','anonymize','delete']);
            $table->enum('status',['pending','approved','rejected','completed'])->default('pending');

            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy_requests');
    }
};
