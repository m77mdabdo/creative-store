<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class ShippingPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'shipping-methods.view',
            'shipping-methods.create',
            'shipping-methods.edit',
            'shipping-methods.delete',

            'shipping-zones.view',
            'shipping-zones.create',
            'shipping-zones.edit',
            'shipping-zones.delete',

            'shipping-rates.view',
            'shipping-rates.create',
            'shipping-rates.edit',
            'shipping-rates.delete',

            'shipments.view',
            'shipments.create',
            'shipments.edit',
            'shipments.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['code' => $permission],
                [
                    'name' => $permission,
                    'code' => $permission,
                ]
            );
        }
    }
}
