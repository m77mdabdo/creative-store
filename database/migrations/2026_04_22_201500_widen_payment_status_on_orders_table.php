<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STATUSES = [
        'unpaid',
        'pending',
        'paid',
        'failed',
        'refund_pending',
        'refunded',
        'partially_refunded',
        'cancelled',
    ];

    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('payment_status')->default('pending')->change();
            });

            return;
        }

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $values = "'" . implode("','", self::STATUSES) . "'";

        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM({$values}) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('unpaid','paid','failed','refunded','partially_refunded') NOT NULL DEFAULT 'unpaid'");
    }
};
