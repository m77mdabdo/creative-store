<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
            $table->string('slug_ar')->nullable()->after('slug');
            $table->text('excerpt_ar')->nullable()->after('excerpt');
            $table->longText('content_ar')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'slug_ar', 'excerpt_ar', 'content_ar']);
        });
    }
};
