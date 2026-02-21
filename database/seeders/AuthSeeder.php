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
        $ownerRole    = Role::firstOrCreate(['name' => 'owner']);
        $adminRole    = Role::firstOrCreate(['name' => 'admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        /* =========================
         * Permissions
         * ========================= */
        $permissions = [
            // Users (Employees management)
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

            // Audit Logs (متابعة الموظفين)
            'audit.view',
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

        // Owner: كل الصلاحيات
        $ownerRole->permissions()->sync($permissionIds);

        // Admin: صلاحيات الموظفين + التشغيل + مشاهدة اللوجز
        $adminPermissions = Permission::whereIn('code', [
            'users.view','users.create','users.update','users.delete',
            'categories.view','categories.create','categories.update','categories.delete',
            'products.view','products.create','products.update','products.delete',
            'orders.view','orders.update_status',
            'payments.view','refunds.process',
            'shipping.manage','inventory.manage','returns.manage','settings.manage',
            'audit.view',
        ])->pluck('id')->all();

        $adminRole->permissions()->sync($adminPermissions);

        // Employee: تشغيل محدود (عدّلها حسب شغل الموظف)
        $employeePermissions = Permission::whereIn('code', [
            'categories.view',
            'products.view',
            'orders.view',
        ])->pluck('id')->all();

        $employeeRole->permissions()->sync($employeePermissions);

        // Customer: قراءة فقط على الموقع (وممنوع Dashboard بالـ role middleware)
        $customerPermissions = Permission::whereIn('code', [
            'categories.view',
            'products.view',
            'orders.view',
        ])->pluck('id')->all();

        $customerRole->permissions()->sync($customerPermissions);

        /* =========================
         * Users
         * ========================= */

        // Owner user
        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Store Owner',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner->roles()->sync([$ownerRole->id]);

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

        // Employee user
        $employee = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Store Employee',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $employee->roles()->sync([$employeeRole->id]);

        // Customers (Fake)
        User::factory(10)->create()->each(function ($user) use ($customerRole) {
            $user->roles()->sync([$customerRole->id]);
        });
    }
}
