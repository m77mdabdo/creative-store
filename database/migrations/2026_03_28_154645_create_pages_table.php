<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->string('title', 255);
            $table->string('slug', 255)->unique();

            $table->longText('content');

            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_header')->default(false);
            $table->boolean('show_in_footer')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('is_active');
            $table->index('show_in_header');
            $table->index('show_in_footer');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
