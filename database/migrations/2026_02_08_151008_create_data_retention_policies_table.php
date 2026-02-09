<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('data_retention_policies', function (Blueprint $table) {
            $table->id();
            $table->string('target_table')->unique();
            $table->unsignedInteger('retain_days');
            $table->boolean('is_anonymize')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_retention_policies');
    }
};
