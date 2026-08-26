<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        // SQLite (tests) stores enums as text with a CHECK list; relax it to
        // a plain string instead of the MySQL-only MODIFY ENUM.
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });

            return;
        }

        DB::statement("
            ALTER TABLE payments
            MODIFY status ENUM(
                'pending',
                'authorized',
                'captured',
                'paid',
                'failed',
                'refunded',
                'partially_refunded'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE payments
            MODIFY status ENUM(
                'pending',
                'authorized',
                'captured',
                'failed',
                'refunded',
                'partially_refunded'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
