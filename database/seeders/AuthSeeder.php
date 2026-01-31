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
        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */
        $ownerRole    = Role::firstOrCreate(['name' => 'owner']);
        $adminRole    = Role::firstOrCreate(['name' => 'admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */
        $permissions = [
            'dashboard.view',

            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',

            'products.view',
            'products.create',
            'products.edit',
            'products.delete',

            'attributes.view',
            'attributes.create',
            'attributes.edit',
            'attributes.delete',

            'reviews.view',
            'reviews.edit',
            'reviews.delete',

            'orders.view',
            'orders.edit',
            'orders.delete',

           'coupons.view',
'coupons.create',
'coupons.edit',
'coupons.delete',

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

            'shipping.view',
            'shipping.create',
            'shipping.edit',
            'shipping.delete',

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

            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',

            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',

            'addresses.view',
            'addresses.create',
            'addresses.edit',
            'addresses.delete',

            'wishlists.view',
            'wishlists.delete',

            'carts.view',
            'carts.edit',
            'carts.delete',

            'blogs.view',
            'blogs.create',
            'blogs.edit',
            'blogs.delete',

            'blog-categories.view',
            'blog-categories.create',
            'blog-categories.edit',
            'blog-categories.delete',

            'blog-comments.view',
            'blog-comments.edit',
            'blog-comments.delete',

            'pages.view',
            'pages.create',
            'pages.edit',
            'pages.delete',

            'contact.view',
            'contact.delete',

            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Abandoned Carts
            'abandoned-carts.view',
            

            'audit.view',
            'error-logs.view',
            'error-logs.edit',
            'backups.view',
            'backups.create',
            'backups.delete',
            'settings.view',
            'settings.edit',
            'uat.view',
            'uat.create',
            'uat.edit',
            'uat.delete',
            'privacy-requests.view',
            'privacy-requests.edit',
            'retention-policies.view',
            'retention-policies.edit',
        ];

        $permissionIds = [];

        foreach ($permissions as $code) {
            $permission = Permission::firstOrCreate(
                ['code' => $code],
                ['name' => Str::headline(str_replace(['.', '-'], ' ', $code))]
            );

            $permissionIds[] = $permission->id;
        }

        /*
        |--------------------------------------------------------------------------
        | Attach Permissions To Roles
        |--------------------------------------------------------------------------
        */

        // Owner = Full Access
        $ownerRole->permissions()->sync($permissionIds);

        // Admin = Full Access
        $adminRole->permissions()->sync($permissionIds);

        // Employee = Operational Access
        $employeePermissions = Permission::whereIn('code', [
            'dashboard.view',

            'users.view',

            'categories.view',
            'categories.create',
            'categories.edit',

            'products.view',
            'products.create',
            'products.edit',

            'attributes.view',
            'attributes.create',
            'attributes.edit',

            'reviews.view',
            'reviews.edit',

            'orders.view',
            'orders.edit',

            'coupons.view',
            'coupons.create',
            'coupons.edit',

            'payments.view',
            'refunds.view',
            'returns.view',

            'shipping.view',
            'shipping.edit',
            'shipments.view',
            'shipments.edit',

            'customers.view',
            'addresses.view',
            'wishlists.view',
            'carts.view',

            'blogs.view',
            'blog-comments.view',
            'pages.view',
            'contact.view',

            'audit.view',
            'error-logs.view',
            'settings.view',
        ])->pluck('id')->all();

        $employeeRole->permissions()->sync($employeePermissions);

        // Customer = no dashboard permissions
        $customerRole->permissions()->sync([]);

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        if (! $this->shouldSeedDemoAccounts()) {
            $this->seedProductionOwner($ownerRole);

            return;
        }

        // Demo accounts for local development and tests only.
        $password = (string) (env('SEED_DEMO_PASSWORD') ?: 'password');

        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Store Owner',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );
        $owner->roles()->sync([$ownerRole->id]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );
        $admin->roles()->sync([$adminRole->id]);

        $employee = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Store Employee',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );
        $employee->roles()->sync([$employeeRole->id]);

        // Customers
        User::factory(10)->create()->each(function ($user) use ($customerRole) {
            $user->roles()->sync([$customerRole->id]);
        });
    }

    /**
     * Known demo logins are never created outside local development and
     * tests unless SEED_DEMO_ACCOUNTS=true is set on purpose.
     */
    private function shouldSeedDemoAccounts(): bool
    {
        return app()->environment(['local', 'testing'])
            || filter_var(env('SEED_DEMO_ACCOUNTS', false), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * On a real install, create the first owner from SEED_OWNER_EMAIL with a
     * random password, shown once in the console. Nothing is created when
     * the variable is empty.
     */
    private function seedProductionOwner(Role $ownerRole): void
    {
        $email = strtolower(trim((string) env('SEED_OWNER_EMAIL', '')));

        if ($email === '' || User::withTrashed()->where('email', $email)->exists()) {
            $this->command?->info('Skipped demo accounts. Use SEED_OWNER_EMAIL or `php artisan users:assign-role` to set up the first owner.');

            return;
        }

        $password = Str::password(16);

        $owner = User::create([
            'name' => 'Store Owner',
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $owner->forceFill(['email_verified_at' => now()])->save();
        $owner->roles()->sync([$ownerRole->id]);

        $this->command?->warn("Owner account created for {$email}. Temporary password: {$password}");
        $this->command?->warn('Sign in and change this password straight away.');
    }
}
