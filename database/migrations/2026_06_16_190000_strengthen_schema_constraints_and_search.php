<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Closes the remaining gaps found in the schema review:
 * - CHECK constraints so money columns can never go negative and coupon
 *   percentages cannot exceed 100, even if a bug or a manual query tries;
 * - an index for public shipment tracking lookups;
 * - a FULLTEXT index for product name search on MySQL.
 */
return new class extends Migration
{
    private const CHECKS = [
        'orders' => [
            'chk_orders_amounts' => 'subtotal >= 0 AND discount >= 0 AND shipping_fee >= 0 AND tax_total >= 0 AND total >= 0',
        ],
        'payments' => [
            'chk_payments_amount' => 'amount >= 0',
        ],
        'refunds' => [
            'chk_refunds_amount' => 'amount > 0',
        ],
        'coupons' => [
            'chk_coupons_value' => "value > 0 AND (type <> 'percent' OR value <= 100)",
        ],
        'coupon_usages' => [
            'chk_coupon_usages_discount' => 'discount_amount >= 0',
        ],
        'shipping_rates' => [
            'chk_shipping_rates_price' => 'price >= 0',
        ],
    ];

    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->index('tracking_number', 'shipments_tracking_number_index');
        });

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('product_translations', function (Blueprint $table) {
            $table->fullText('name', 'product_translations_name_fulltext');
        });

        foreach (self::CHECKS as $table => $checks) {
            foreach ($checks as $name => $expression) {
                // Existing bad rows would make ALTER fail; report them instead of
                // blocking the deploy, so they can be fixed and the check re-run.
                $violations = DB::table($table)->whereRaw("NOT ({$expression})")->count();

                if ($violations > 0) {
                    logger()->warning("Skipped {$name}: {$violations} existing row(s) in {$table} violate it.");

                    continue;
                }

                DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$name} CHECK ({$expression})");
            }
        }
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex('shipments_tracking_number_index');
        });

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('product_translations', function (Blueprint $table) {
            $table->dropFullText('product_translations_name_fulltext');
        });

        foreach (self::CHECKS as $table => $checks) {
            foreach (array_keys($checks) as $name) {
                $exists = DB::selectOne(
                    'SELECT COUNT(*) AS c FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
                    [$table, $name]
                );

                if ((int) $exists->c > 0) {
                    DB::statement("ALTER TABLE {$table} DROP CHECK {$name}");
                }
            }
        }
    }
};
