<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AclSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedRoles();
            $this->seedPermissions();
            $this->syncRolePermissions();
            $this->attachOwnerToFirstUser();
        });
    }

    protected function seedRoles(): void
    {
        foreach (['owner', 'admin', 'employee', 'customer'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }

    protected function seedPermissions(): void
    {
        foreach ($this->permissions() as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::updateOrCreate(
                    ['code' => $permission['code']],
                    [
                        'name'  => $permission['name'],
                        'group' => $group,
                    ]
                );
            }
        }
    }

    protected function syncRolePermissions(): void
    {
        $owner    = Role::where('name', 'owner')->firstOrFail();
        $admin    = Role::where('name', 'admin')->firstOrFail();
        $employee = Role::where('name', 'employee')->firstOrFail();
        $customer = Role::where('name', 'customer')->firstOrFail();

        $allPermissionIds = Permission::query()->pluck('id')->all();
        $owner->permissions()->sync($allPermissionIds);

        $adminCodes = [
            'dashboard.view',

            'roles.view',
            'roles.create',
            'roles.edit',
            'permissions.view',

            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'audit.view',
            'error-logs.view',
            'error-logs.edit',

            'orders.view',
            'orders.edit',

            'products.view',
            'products.create',
            'products.edit',
            'products.delete',

            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',

            'contact.view',
            'contact.edit',
            'contact.delete',

            'attributes.view',
            'attributes.create',
            'attributes.edit',
            'attributes.delete',

            'reviews.view',
            'reviews.edit',
            'reviews.delete',

            'coupons.view',
            'coupons.create',
            'coupons.edit',
            'coupons.delete',

            'payments.view',
            'refunds.view',
            'returns.view',

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

            'addresses.view',
            'addresses.edit',
            'addresses.delete',

            'wishlists.view',
            'wishlists.edit',
            'wishlists.delete',

            'carts.view',
            'carts.edit',
            'carts.delete',

            'abandoned-cart-recoveries.view',

            'blog-categories.view',
            'blog-categories.create',
            'blog-categories.edit',
            'blog-categories.delete',

            'blogs.view',
            'blogs.create',
            'blogs.edit',
            'blogs.delete',

            'blog-comments.view',
            'blog-comments.delete',

            'pages.view',
            'pages.create',
            'pages.edit',
            'pages.delete',
        ];

        $employeeCodes = [
            'dashboard.view',

            'orders.view',
            'orders.edit',

            'products.view',
            'products.edit',

            'categories.view',
            'attributes.view',
            'reviews.view',
            'reviews.edit',

            'contact.view',

            'payments.view',
            'refunds.view',
            'returns.view',

            'shipments.view',
            'shipments.edit',

            'addresses.view',
            'wishlists.view',
            'carts.view',
            'abandoned-cart-recoveries.view',

            'blogs.view',
            'blog-comments.view',
            'pages.view',
        ];

        $customerCodes = [];

        $admin->permissions()->sync($this->permissionIdsByCodes($adminCodes));
        $employee->permissions()->sync($this->permissionIdsByCodes($employeeCodes));
        $customer->permissions()->sync($this->permissionIdsByCodes($customerCodes));
    }

    protected function permissionIdsByCodes(array $codes): array
    {
        return Permission::query()
            ->whereIn('code', $codes)
            ->pluck('id')
            ->all();
    }

    /**
     * Local installs get an owner out of the box. A store that already has an
     * owner is left alone, and outside local development nobody is promoted
     * automatically (the first user might be a customer).
     */
    protected function attachOwnerToFirstUser(): void
    {
        $hasOwner = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'owner'))->exists();

        if ($hasOwner || ! app()->environment(['local', 'testing'])) {
            return;
        }

        $firstUser = User::query()->oldest('id')->first();

        if (! $firstUser) {
            return;
        }

        $firstUser->assignRole('owner');
    }

    protected function permissions(): array
    {
        return [
            'dashboard' => [
                ['code' => 'dashboard.view', 'name' => 'View dashboard'],
            ],

            'roles' => [
                ['code' => 'roles.view', 'name' => 'View roles'],
                ['code' => 'roles.create', 'name' => 'Create roles'],
                ['code' => 'roles.edit', 'name' => 'Edit roles'],
                ['code' => 'roles.delete', 'name' => 'Delete roles'],
            ],

            'permissions' => [
                ['code' => 'permissions.view', 'name' => 'View permissions'],
            ],

            'users' => [
                ['code' => 'users.view', 'name' => 'View users'],
                ['code' => 'users.create', 'name' => 'Create users'],
                ['code' => 'users.edit', 'name' => 'Edit users'],
                ['code' => 'users.delete', 'name' => 'Delete users'],
            ],

            'audit' => [
                ['code' => 'audit.view', 'name' => 'View audit logs'],
            ],

            'error-logs' => [
                ['code' => 'error-logs.view', 'name' => 'View error logs'],
                ['code' => 'error-logs.edit', 'name' => 'Resolve and clear error logs'],
            ],

            // Backups contain the whole database; only owners get them by default.
            'backups' => [
                ['code' => 'backups.view', 'name' => 'View and download backups'],
                ['code' => 'backups.create', 'name' => 'Create backups'],
                ['code' => 'backups.delete', 'name' => 'Delete backups'],
            ],

            'orders' => [
                ['code' => 'orders.view', 'name' => 'View orders'],
                ['code' => 'orders.edit', 'name' => 'Edit orders'],
            ],

            'products' => [
                ['code' => 'products.view', 'name' => 'View products'],
                ['code' => 'products.create', 'name' => 'Create products'],
                ['code' => 'products.edit', 'name' => 'Edit products'],
                ['code' => 'products.delete', 'name' => 'Delete products'],
            ],

            'categories' => [
                ['code' => 'categories.view', 'name' => 'View categories'],
                ['code' => 'categories.create', 'name' => 'Create categories'],
                ['code' => 'categories.edit', 'name' => 'Edit categories'],
                ['code' => 'categories.delete', 'name' => 'Delete categories'],
            ],

            'contact' => [
                ['code' => 'contact.view', 'name' => 'View contact messages'],
                ['code' => 'contact.edit', 'name' => 'Edit contact messages'],
                ['code' => 'contact.delete', 'name' => 'Delete contact messages'],
            ],

            'attributes' => [
                ['code' => 'attributes.view', 'name' => 'View attributes'],
                ['code' => 'attributes.create', 'name' => 'Create attributes'],
                ['code' => 'attributes.edit', 'name' => 'Edit attributes'],
                ['code' => 'attributes.delete', 'name' => 'Delete attributes'],
            ],

            'reviews' => [
                ['code' => 'reviews.view', 'name' => 'View reviews'],
                ['code' => 'reviews.edit', 'name' => 'Edit reviews'],
                ['code' => 'reviews.delete', 'name' => 'Delete reviews'],
            ],

            'coupons' => [
                ['code' => 'coupons.view', 'name' => 'View coupons'],
                ['code' => 'coupons.create', 'name' => 'Create coupons'],
                ['code' => 'coupons.edit', 'name' => 'Edit coupons'],
                ['code' => 'coupons.delete', 'name' => 'Delete coupons'],
            ],

            'payments' => [
                ['code' => 'payments.view', 'name' => 'View payments'],
            ],

            'refunds' => [
                ['code' => 'refunds.view', 'name' => 'View refunds'],
            ],

            'returns' => [
                ['code' => 'returns.view', 'name' => 'View returns'],
            ],

            'shipping-methods' => [
                ['code' => 'shipping-methods.view', 'name' => 'View shipping methods'],
                ['code' => 'shipping-methods.create', 'name' => 'Create shipping methods'],
                ['code' => 'shipping-methods.edit', 'name' => 'Edit shipping methods'],
                ['code' => 'shipping-methods.delete', 'name' => 'Delete shipping methods'],
            ],

            'shipping-zones' => [
                ['code' => 'shipping-zones.view', 'name' => 'View shipping zones'],
                ['code' => 'shipping-zones.create', 'name' => 'Create shipping zones'],
                ['code' => 'shipping-zones.edit', 'name' => 'Edit shipping zones'],
                ['code' => 'shipping-zones.delete', 'name' => 'Delete shipping zones'],
            ],

            'shipping-rates' => [
                ['code' => 'shipping-rates.view', 'name' => 'View shipping rates'],
                ['code' => 'shipping-rates.create', 'name' => 'Create shipping rates'],
                ['code' => 'shipping-rates.edit', 'name' => 'Edit shipping rates'],
                ['code' => 'shipping-rates.delete', 'name' => 'Delete shipping rates'],
            ],

            'shipments' => [
                ['code' => 'shipments.view', 'name' => 'View shipments'],
                ['code' => 'shipments.create', 'name' => 'Create shipments'],
                ['code' => 'shipments.edit', 'name' => 'Edit shipments'],
                ['code' => 'shipments.delete', 'name' => 'Delete shipments'],
            ],

            'addresses' => [
                ['code' => 'addresses.view', 'name' => 'View addresses'],
                ['code' => 'addresses.edit', 'name' => 'Edit addresses'],
                ['code' => 'addresses.delete', 'name' => 'Delete addresses'],
            ],

            'wishlists' => [
                ['code' => 'wishlists.view', 'name' => 'View wishlists'],
                ['code' => 'wishlists.edit', 'name' => 'Edit wishlists'],
                ['code' => 'wishlists.delete', 'name' => 'Delete wishlists'],
            ],

            'carts' => [
                ['code' => 'carts.view', 'name' => 'View carts'],
                ['code' => 'carts.edit', 'name' => 'Edit carts'],
                ['code' => 'carts.delete', 'name' => 'Delete carts'],
            ],

            'abandoned-cart-recoveries' => [
                ['code' => 'abandoned-cart-recoveries.view', 'name' => 'View abandoned cart recoveries'],
            ],

            'blog-categories' => [
                ['code' => 'blog-categories.view', 'name' => 'View blog categories'],
                ['code' => 'blog-categories.create', 'name' => 'Create blog categories'],
                ['code' => 'blog-categories.edit', 'name' => 'Edit blog categories'],
                ['code' => 'blog-categories.delete', 'name' => 'Delete blog categories'],
            ],

            'blogs' => [
                ['code' => 'blogs.view', 'name' => 'View blogs'],
                ['code' => 'blogs.create', 'name' => 'Create blogs'],
                ['code' => 'blogs.edit', 'name' => 'Edit blogs'],
                ['code' => 'blogs.delete', 'name' => 'Delete blogs'],
            ],

            'blog-comments' => [
                ['code' => 'blog-comments.view', 'name' => 'View blog comments'],
                ['code' => 'blog-comments.delete', 'name' => 'Delete blog comments'],
            ],

            'pages' => [
                ['code' => 'pages.view', 'name' => 'View pages'],
                ['code' => 'pages.create', 'name' => 'Create pages'],
                ['code' => 'pages.edit', 'name' => 'Edit pages'],
                ['code' => 'pages.delete', 'name' => 'Delete pages'],
            ],
        ];
    }
}
