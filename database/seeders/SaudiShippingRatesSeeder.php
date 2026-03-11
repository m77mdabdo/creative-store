<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\ShippingRate;

class SaudiShippingRatesSeeder extends Seeder
{
    public function run(): void
    {
        $zone = ShippingZone::updateOrCreate(
            ['code' => 'saudi-arabia'],
            [
                'name' => 'Saudi Arabia',
                'country' => 'Saudi Arabia',
                'description' => 'All Saudi Arabia',
                'is_active' => true,
            ]
        );

        $standard = ShippingMethod::updateOrCreate(
            ['code' => 'standard'],
            [
                'name' => 'Standard Delivery',
                'description' => 'Standard shipping',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $express = ShippingMethod::updateOrCreate(
            ['code' => 'express'],
            [
                'name' => 'Express Delivery',
                'description' => 'Fast shipping',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $sameDay = ShippingMethod::updateOrCreate(
            ['code' => 'same-day'],
            [
                'name' => 'Same Day Delivery',
                'description' => 'Delivery in same day',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $pickup = ShippingMethod::updateOrCreate(
            ['code' => 'pickup'],
            [
                'name' => 'Store Pickup',
                'description' => 'Pick up from store',
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Riyadh
        |--------------------------------------------------------------------------
        */
        $riyadhNames = ['Riyadh', 'الرياض'];

        foreach ($riyadhNames as $city) {
            ShippingRate::updateOrCreate(
                [
                    'shipping_zone_id' => $zone->id,
                    'shipping_method_id' => $standard->id,
                    'city' => $city,
                    'district' => null,
                    'min_weight' => 0,
                    'max_weight' => 999999,
                ],
                [
                    'price' => 20,
                    'eta_min_days' => 1,
                    'eta_max_days' => 2,
                    'free_shipping_threshold' => 300,
                    'cash_on_delivery_fee' => 0,
                    'priority' => 1,
                    'is_active' => true,
                ]
            );

            ShippingRate::updateOrCreate(
                [
                    'shipping_zone_id' => $zone->id,
                    'shipping_method_id' => $express->id,
                    'city' => $city,
                    'district' => null,
                    'min_weight' => 0,
                    'max_weight' => 999999,
                ],
                [
                    'price' => 30,
                    'eta_min_days' => 0,
                    'eta_max_days' => 1,
                    'free_shipping_threshold' => null,
                    'cash_on_delivery_fee' => 0,
                    'priority' => 2,
                    'is_active' => true,
                ]
            );

            ShippingRate::updateOrCreate(
                [
                    'shipping_zone_id' => $zone->id,
                    'shipping_method_id' => $sameDay->id,
                    'city' => $city,
                    'district' => null,
                    'min_weight' => 0,
                    'max_weight' => 999999,
                ],
                [
                    'price' => 40,
                    'eta_min_days' => 0,
                    'eta_max_days' => 0,
                    'free_shipping_threshold' => null,
                    'cash_on_delivery_fee' => 0,
                    'priority' => 3,
                    'is_active' => true,
                ]
            );

            ShippingRate::updateOrCreate(
                [
                    'shipping_zone_id' => $zone->id,
                    'shipping_method_id' => $pickup->id,
                    'city' => $city,
                    'district' => null,
                    'min_weight' => 0,
                    'max_weight' => 999999,
                ],
                [
                    'price' => 0,
                    'eta_min_days' => 0,
                    'eta_max_days' => 0,
                    'free_shipping_threshold' => null,
                    'cash_on_delivery_fee' => 0,
                    'priority' => 4,
                    'is_active' => true,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Major Cities
        |--------------------------------------------------------------------------
        */
        $majorCities = [
            'Jeddah', 'جدة',
            'Makkah', 'Mecca', 'مكة', 'مكة المكرمة',
            'Madinah', 'Medina', 'المدينة', 'المدينة المنورة',
            'Dammam', 'الدمام',
            'Khobar', 'Al Khobar', 'الخبر',
            'Dhahran', 'الظهران',
        ];

        foreach ($majorCities as $city) {
            ShippingRate::updateOrCreate(
                [
                    'shipping_zone_id' => $zone->id,
                    'shipping_method_id' => $standard->id,
                    'city' => $city,
                    'district' => null,
                    'min_weight' => 0,
                    'max_weight' => 999999,
                ],
                [
                    'price' => 25,
                    'eta_min_days' => 2,
                    'eta_max_days' => 4,
                    'free_shipping_threshold' => 500,
                    'cash_on_delivery_fee' => 0,
                    'priority' => 1,
                    'is_active' => true,
                ]
            );

            ShippingRate::updateOrCreate(
                [
                    'shipping_zone_id' => $zone->id,
                    'shipping_method_id' => $express->id,
                    'city' => $city,
                    'district' => null,
                    'min_weight' => 0,
                    'max_weight' => 999999,
                ],
                [
                    'price' => 40,
                    'eta_min_days' => 1,
                    'eta_max_days' => 2,
                    'free_shipping_threshold' => null,
                    'cash_on_delivery_fee' => 0,
                    'priority' => 2,
                    'is_active' => true,
                ]
            );

            ShippingRate::updateOrCreate(
                [
                    'shipping_zone_id' => $zone->id,
                    'shipping_method_id' => $pickup->id,
                    'city' => $city,
                    'district' => null,
                    'min_weight' => 0,
                    'max_weight' => 999999,
                ],
                [
                    'price' => 0,
                    'eta_min_days' => 0,
                    'eta_max_days' => 0,
                    'free_shipping_threshold' => null,
                    'cash_on_delivery_fee' => 0,
                    'priority' => 4,
                    'is_active' => true,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback for Other Cities
        |--------------------------------------------------------------------------
        */
        ShippingRate::updateOrCreate(
            [
                'shipping_zone_id' => $zone->id,
                'shipping_method_id' => $standard->id,
                'city' => null,
                'district' => null,
                'min_weight' => 0,
                'max_weight' => 999999,
            ],
            [
                'price' => 35,
                'eta_min_days' => 3,
                'eta_max_days' => 6,
                'free_shipping_threshold' => null,
                'cash_on_delivery_fee' => 0,
                'priority' => 10,
                'is_active' => true,
            ]
        );

        ShippingRate::updateOrCreate(
            [
                'shipping_zone_id' => $zone->id,
                'shipping_method_id' => $express->id,
                'city' => null,
                'district' => null,
                'min_weight' => 0,
                'max_weight' => 999999,
            ],
            [
                'price' => 50,
                'eta_min_days' => 2,
                'eta_max_days' => 3,
                'free_shipping_threshold' => null,
                'cash_on_delivery_fee' => 0,
                'priority' => 11,
                'is_active' => true,
            ]
        );

        ShippingRate::updateOrCreate(
            [
                'shipping_zone_id' => $zone->id,
                'shipping_method_id' => $pickup->id,
                'city' => null,
                'district' => null,
                'min_weight' => 0,
                'max_weight' => 999999,
            ],
            [
                'price' => 0,
                'eta_min_days' => 0,
                'eta_max_days' => 0,
                'free_shipping_threshold' => null,
                'cash_on_delivery_fee' => 0,
                'priority' => 12,
                'is_active' => true,
            ]
        );
    }
}
