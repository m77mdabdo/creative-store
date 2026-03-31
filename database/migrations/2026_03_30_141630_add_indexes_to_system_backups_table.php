<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_backups', function (Blueprint $table) {
            $table->index('type');
            $table->index('status');
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('system_backups', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['started_at']);
        });
    }
};
