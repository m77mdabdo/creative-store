<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PaymentPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'payments.export',
            'payments.sync',

            'refunds.view',
            'refunds.create',
            'refunds.edit',
            'refunds.delete',

            'returns.view',
            'returns.create',
            'returns.edit',
            'returns.delete',
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
