<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $code = $this->faker->unique()->randomElement([
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
        ]);

        return [
            'code' => $code,
            'name' => Str::headline(str_replace(['.', '-'], ' ', $code)),
        ];
    }
}
