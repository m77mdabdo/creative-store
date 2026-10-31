<?php

use App\Support\Shipping\DeliveryTiers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Moves existing shipping rates to the new delivery tiers and adds
 * next-day delivery for the major cities.
 */
return new class extends Migration
{
    public function up(): void
    {
        DeliveryTiers::apply();
    }

    public function down(): void
    {
        $nextDay = DB::table('shipping_methods')->where('code', 'next-day')->value('id');

        if ($nextDay && ! DB::table('orders')->where('shipping_method_id', $nextDay)->exists()) {
            DB::table('shipping_rates')->where('shipping_method_id', $nextDay)->delete();
            DB::table('shipping_methods')->where('id', $nextDay)->delete();
        }
    }
};
