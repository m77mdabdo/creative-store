<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AuthSeeder::class,      // Users + Roles + Permissions
            CatalogSeeder::class,   // Categories + Products
            VariantSeeder::class,   // Attributes + Variants
            CustomerSeeder::class,  // Carts + Wishlists + Reviews + Addresses
            OrderSeeder::class,     // Orders + Payments + Shipping
            OpsSeeder::class,       // Settings + CMS + Logs + Ops
            paymentPermissionSeeder::class, // Payment Permissions
             AssignPaymentPermissionsSeeder::class,
             RefundReturnSeeder::class,

        ]);
    }
}


