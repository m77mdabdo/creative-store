<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingZone;

class SaudiShippingZonesSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Riyadh Region',
                'code' => 'riyadh-region',
                'country' => 'Saudi Arabia',
                'description' => 'Riyadh administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Makkah Region',
                'code' => 'makkah-region',
                'country' => 'Saudi Arabia',
                'description' => 'Makkah administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Madinah Region',
                'code' => 'madinah-region',
                'country' => 'Saudi Arabia',
                'description' => 'Madinah administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Eastern Region',
                'code' => 'eastern-region',
                'country' => 'Saudi Arabia',
                'description' => 'Eastern Province',
                'is_active' => true,
            ],
            [
                'name' => 'Al-Qassim Region',
                'code' => 'qassim-region',
                'country' => 'Saudi Arabia',
                'description' => 'Al-Qassim administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Asir Region',
                'code' => 'asir-region',
                'country' => 'Saudi Arabia',
                'description' => 'Asir administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Tabuk Region',
                'code' => 'tabuk-region',
                'country' => 'Saudi Arabia',
                'description' => 'Tabuk administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Hail Region',
                'code' => 'hail-region',
                'country' => 'Saudi Arabia',
                'description' => 'Hail administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Northern Borders Region',
                'code' => 'northern-borders-region',
                'country' => 'Saudi Arabia',
                'description' => 'Northern Borders administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Jazan Region',
                'code' => 'jazan-region',
                'country' => 'Saudi Arabia',
                'description' => 'Jazan administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Najran Region',
                'code' => 'najran-region',
                'country' => 'Saudi Arabia',
                'description' => 'Najran administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Al-Baha Region',
                'code' => 'al-baha-region',
                'country' => 'Saudi Arabia',
                'description' => 'Al-Baha administrative region',
                'is_active' => true,
            ],
            [
                'name' => 'Al-Jouf Region',
                'code' => 'al-jouf-region',
                'country' => 'Saudi Arabia',
                'description' => 'Al-Jouf administrative region',
                'is_active' => true,
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::updateOrCreate(
                ['code' => $zone['code']],
                $zone
            );
        }
    }
}
