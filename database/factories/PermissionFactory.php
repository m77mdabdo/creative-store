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
            'users.view','users.create','users.update','users.delete',
            'categories.view','categories.create','categories.update','categories.delete',
            'products.view','products.create','products.update','products.delete',
            'orders.view','orders.update_status',
            'payments.view','refunds.process',
            'shipping.manage','inventory.manage','returns.manage','settings.manage'
        ]);

        return [
            'code' => $code,
            'name' => Str::headline(str_replace('.', ' ', $code)),
        ];
    }
}
