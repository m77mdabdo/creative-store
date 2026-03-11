<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->decimal('cash_on_delivery_fee', 12, 2)->default(0)->after('free_shipping_threshold');
            $table->integer('priority')->default(0)->after('cash_on_delivery_fee');
            $table->boolean('is_active')->default(true)->after('priority');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->dropColumn([
                'cash_on_delivery_fee',
                'priority',
                'is_active',
            ]);
        });
    }
};
