<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        /* =========================
         * Roles
         * ========================= */
        $adminRole    = Role::firstOrCreate(['name' => 'admin']);
        $managerRole  = Role::firstOrCreate(['name' => 'manager']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        /* =========================
         * Permissions
         * ========================= */
        $permissions = [
            // Users
            'users.view','users.create','users.update','users.delete',

            // Categories
            'categories.view','categories.create','categories.update','categories.delete',

            // Products
            'products.view','products.create','products.update','products.delete',

            // Orders / Payments
            'orders.view','orders.update_status',
            'payments.view','refunds.process',

            // Ops
            'shipping.manage','inventory.manage','returns.manage','settings.manage',
        ];

        $permissionIds = [];
        foreach ($permissions as $code) {
            $permission = Permission::firstOrCreate(
                ['code' => $code],
                ['name' => Str::headline(str_replace('.', ' ', $code))]
            );
            $permissionIds[] = $permission->id;
        }

        /* =========================
         * Attach permissions to roles
         * ========================= */

        // Admin: كل الصلاحيات
        $adminRole->permissions()->sync($permissionIds);

        // Manager: صلاحيات تشغيل
        $managerPermissions = Permission::whereIn('code', [
            'categories.view','categories.create','categories.update',
            'products.view','products.create','products.update',
            'orders.view','orders.update_status',
            'payments.view',
            'shipping.manage','inventory.manage','returns.manage',
        ])->pluck('id')->all();

        $managerRole->permissions()->sync($managerPermissions);

        // Customer: قراءة فقط
        $customerPermissions = Permission::whereIn('code', [
            'categories.view',
            'products.view',
            'orders.view',
        ])->pluck('id')->all();

        $customerRole->permissions()->sync($customerPermissions);

        /* =========================
         * Users
         * ========================= */

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->roles()->sync([$adminRole->id]);

        // Manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Store Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->roles()->sync([$managerRole->id]);

        // Customers (Fake)
        User::factory(10)->create()->each(function ($user) use ($customerRole) {
            $user->roles()->sync([$customerRole->id]);
        });
    }
}
