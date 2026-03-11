<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_method_id')
                ->nullable()
                ->after('shipping_address_id')
                ->constrained('shipping_methods')
                ->nullOnDelete();

            $table->string('shipping_status')
                ->default('pending')
                ->after('status');

            $table->timestamp('estimated_delivery_from')
                ->nullable()
                ->after('shipping_status');

            $table->timestamp('estimated_delivery_to')
                ->nullable()
                ->after('estimated_delivery_from');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_method_id');
            $table->dropColumn([
                'shipping_status',
                'estimated_delivery_from',
                'estimated_delivery_to',
            ]);
        });
    }
};
