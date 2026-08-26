<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        // SQLite (tests) stores enums as text with a CHECK list; relax it to
        // a plain string instead of the MySQL-only MODIFY ENUM.
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('shipments', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });

            return;
        }

        DB::statement("
            ALTER TABLE shipments
            MODIFY status ENUM(
                'pending',
                'processing',
                'shipped',
                'in_transit',
                'delivered',
                'failed',
                'cancelled'
            ) DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE shipments
            MODIFY status ENUM(
                'pending',
                'picked',
                'in_transit',
                'delivered',
                'failed_return'
            ) DEFAULT 'pending'
        ");
    }
};
