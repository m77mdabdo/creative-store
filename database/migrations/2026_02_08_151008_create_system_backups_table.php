<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['manual','scheduled']);
            $table->string('file_path');
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->enum('status',['success','failed']);
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_backups');
    }
};
