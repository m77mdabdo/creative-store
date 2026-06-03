<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes for the filters the dashboard runs on every page and for coupon
 * limit checks during checkout, so they stay fast as tables grow.
 */
return new class extends Migration
{
    private const INDEXES = [
        'orders'                    => ['orders_status_id_index' => ['status', 'id'], 'orders_payment_status_index' => ['payment_status']],
        'payments'                  => ['payments_status_index' => ['status']],
        'contact_messages'          => ['contact_messages_is_read_id_index' => ['is_read', 'id']],
        'reviews'                   => ['reviews_is_approved_id_index' => ['is_approved', 'id']],
        'shipments'                 => ['shipments_status_index' => ['status']],
        'carts'                     => ['carts_updated_at_index' => ['updated_at']],
        'abandoned_cart_recoveries' => ['abandoned_cart_recoveries_status_index' => ['status']],
        'coupon_usages'             => ['coupon_usages_coupon_user_index' => ['coupon_id', 'user_id']],
    ];

    public function up(): void
    {
        foreach (self::INDEXES as $table => $indexes) {
            Schema::table($table, function (Blueprint $blueprint) use ($indexes) {
                foreach ($indexes as $name => $columns) {
                    $blueprint->index($columns, $name);
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::INDEXES as $table => $indexes) {
            Schema::table($table, function (Blueprint $blueprint) use ($indexes) {
                foreach (array_keys($indexes) as $name) {
                    $blueprint->dropIndex($name);
                }
            });
        }
    }
};
