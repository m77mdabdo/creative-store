<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->char('locale', 2);
            $table->string('title');
            $table->longText('content');
            $table->unique(['page_id','locale']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_translations');
    }
};
