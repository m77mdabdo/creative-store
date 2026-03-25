<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
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
