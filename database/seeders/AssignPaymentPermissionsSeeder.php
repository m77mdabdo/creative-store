<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class AssignPaymentPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = Permission::whereIn('code', [
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',

            'refunds.view',
            'refunds.create',
            'refunds.edit',
            'refunds.delete',

            'returns.view',
            'returns.create',
            'returns.edit',
            'returns.delete',
        ])->get();

        $roles = Role::whereIn('name', ['admin', 'owner'])->get();

        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching(
                $permissions->pluck('id')->toArray()
            );
        }
    }
}
