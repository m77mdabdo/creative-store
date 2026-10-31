<?php

namespace App\Support\Shipping;

use Illuminate\Support\Facades\DB;

/**
 * The store's delivery tiers: how many days each shipping method takes and
 * where next-day delivery is offered.
 *
 *   standard   5 – 7 days   everywhere
 *   express    3 – 4 days   everywhere
 *   next-day   tomorrow     Riyadh and the major cities
 *   same-day   today        Riyadh
 *   pickup     today        cities with a pickup point
 *
 * Applied by the shipping seeder and by a data migration so existing stores
 * get the same tiers. Safe to run any number of times.
 */
final class DeliveryTiers
{
    public const TIERS = [
        'standard' => ['sort' => 1, 'min' => 5, 'max' => 7, 'name' => 'Standard Delivery', 'description' => 'Delivered in 5 to 7 days'],
        'express'  => ['sort' => 2, 'min' => 3, 'max' => 4, 'name' => 'Express Delivery', 'description' => 'Delivered in 3 to 4 days'],
        'next-day' => ['sort' => 3, 'min' => 1, 'max' => 1, 'name' => 'Next-Day Delivery', 'description' => 'Order before the cut-off, get it tomorrow'],
        'same-day' => ['sort' => 4, 'min' => 0, 'max' => 0, 'name' => 'Same Day Delivery', 'description' => 'Order before the cut-off, get it today'],
        'pickup'   => ['sort' => 5, 'min' => 0, 'max' => 0, 'name' => 'Store Pickup', 'description' => 'Pick up from store'],
    ];

    /** Cities with next-day delivery, in the spellings customers type. */
    public const NEXT_DAY_CITIES = [
        'Riyadh', 'الرياض',
        'Jeddah', 'جدة',
        'Makkah', 'Mecca', 'مكة', 'مكة المكرمة',
        'Madinah', 'Medina', 'المدينة', 'المدينة المنورة',
        'Dammam', 'الدمام',
        'Khobar', 'Al Khobar', 'الخبر',
        'Dhahran', 'الظهران',
    ];

    /** Next-day costs this much more than express on the same route. */
    private const NEXT_DAY_SURCHARGE = 10;

    public static function apply(): void
    {
        DB::transaction(function () {
            $methodIds = [];

            foreach (self::TIERS as $code => $tier) {
                $existing = DB::table('shipping_methods')->where('code', $code)->first();

                if ($existing) {
                    DB::table('shipping_methods')->where('id', $existing->id)->update([
                        'sort_order' => $tier['sort'],
                        'updated_at' => now(),
                    ]);
                    $methodIds[$code] = $existing->id;

                    continue;
                }

                if ($code !== 'next-day') {
                    continue; // Only next-day is new; the others belong to the store's own setup.
                }

                $methodIds[$code] = DB::table('shipping_methods')->insertGetId([
                    'name'        => $tier['name'],
                    'code'        => $code,
                    'description' => $tier['description'],
                    'sort_order'  => $tier['sort'],
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            foreach ($methodIds as $code => $methodId) {
                DB::table('shipping_rates')->where('shipping_method_id', $methodId)->update([
                    'eta_min_days' => self::TIERS[$code]['min'],
                    'eta_max_days' => self::TIERS[$code]['max'],
                    'updated_at'   => now(),
                ]);
            }

            if (isset($methodIds['express'], $methodIds['next-day'])) {
                self::offerNextDay($methodIds['express'], $methodIds['next-day']);
            }
        });
    }

    /**
     * Copy each big-city express route into a next-day route that does not exist yet.
     */
    private static function offerNextDay(int $expressId, int $nextDayId): void
    {
        $routes = DB::table('shipping_rates')
            ->where('shipping_method_id', $expressId)
            ->whereIn('city', self::NEXT_DAY_CITIES)
            ->get();

        foreach ($routes as $route) {
            $exists = DB::table('shipping_rates')
                ->where('shipping_method_id', $nextDayId)
                ->where('shipping_zone_id', $route->shipping_zone_id)
                ->where('city', $route->city)
                ->where(fn ($q) => $route->district === null
                    ? $q->whereNull('district')
                    : $q->where('district', $route->district))
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('shipping_rates')->insert([
                'shipping_zone_id'        => $route->shipping_zone_id,
                'shipping_method_id'      => $nextDayId,
                'city'                    => $route->city,
                'district'                => $route->district,
                'min_weight'              => $route->min_weight,
                'max_weight'              => $route->max_weight,
                'price'                   => (float) $route->price + self::NEXT_DAY_SURCHARGE,
                'eta_min_days'            => self::TIERS['next-day']['min'],
                'eta_max_days'            => self::TIERS['next-day']['max'],
                'free_shipping_threshold' => null,
                'cash_on_delivery_fee'    => $route->cash_on_delivery_fee,
                'priority'                => $route->priority,
                'is_active'               => true,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);
        }
    }
}
