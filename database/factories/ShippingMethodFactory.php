<?php

namespace Database\Factories;

use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingMethodFactory extends Factory
{
    protected $model = ShippingMethod::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Standard','Express']),
            'code' => $this->faker->unique()->slug(2),
            'is_active' => true,
        ];
    }
}
