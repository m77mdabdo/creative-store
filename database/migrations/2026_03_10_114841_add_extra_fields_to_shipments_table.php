<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('current_location')->nullable()->after('status');
            $table->string('carrier_status')->nullable()->after('current_location');
            $table->text('notes')->nullable()->after('carrier_status');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'current_location',
                'carrier_status',
                'notes',
            ]);
        });
    }
};
