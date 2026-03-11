<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ShippingZone;

class SaudiShippingZoneCitiesSeeder extends Seeder
{
    public function run(): void
    {
        $citiesByZone = [
            'riyadh-region' => ['Riyadh', 'Al Kharj', 'Diriyah', 'Dawadmi', 'Majmaah', 'Shaqra', 'Afif'],
            'makkah-region' => ['Makkah', 'Jeddah', 'Taif', 'Rabigh', 'Al Lith', 'Al Qunfudhah'],
            'madinah-region' => ['Madinah', 'Yanbu', 'Al Ula', 'Badr', 'Mahd Al Dhahab'],
            'eastern-region' => ['Dammam', 'Khobar', 'Dhahran', 'Jubail', 'Al Ahsa', 'Qatif', 'Hafar Al-Batin'],
            'qassim-region' => ['Buraidah', 'Unaizah', 'Ar Rass', 'Al Bukayriyah'],
            'asir-region' => ['Abha', 'Khamis Mushait', 'Ahad Rafidah', 'Muhayil'],
            'tabuk-region' => ['Tabuk', 'Duba', 'Al Wajh', 'Tayma'],
            'hail-region' => ['Hail', 'Baqa', 'Shinan'],
            'northern-borders-region' => ['Arar', 'Rafha', 'Turaif', 'Al Uwayqilah'],
            'jazan-region' => ['Jazan', 'Sabya', 'Samtah', 'Abu Arish'],
            'najran-region' => ['Najran', 'Sharurah', 'Hubuna'],
            'al-baha-region' => ['Al Baha', 'Baljurashi', 'Al Mandaq'],
            'al-jouf-region' => ['Sakaka', 'Dumat Al Jandal', 'Al Qurayyat'],
        ];

        foreach ($citiesByZone as $zoneCode => $cities) {
            $zone = ShippingZone::where('code', $zoneCode)->first();

            if (!$zone) {
                continue;
            }

            foreach ($cities as $city) {
                DB::table('shipping_zone_cities')->updateOrInsert(
                    [
                        'shipping_zone_id' => $zone->id,
                        'city' => $city,
                        'district' => null,
                    ],
                    [
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
