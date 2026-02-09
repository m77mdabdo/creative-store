<?php

namespace Database\Factories;

use App\Models\ShippingZone;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingZoneFactory extends Factory
{
    protected $model = ShippingZone::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['KSA Main','KSA East','KSA West']),
            'country' => 'Saudi Arabia',
            'is_active' => true,
        ];
    }
}
