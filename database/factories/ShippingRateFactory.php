<?php

namespace Database\Factories;

use App\Models\ShippingRate;
use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingRateFactory extends Factory
{
    protected $model = ShippingRate::class;

    public function definition(): array
    {
        return [
            'shipping_zone_id' => ShippingZone::query()->inRandomOrder()->value('id') ?? ShippingZone::factory(),
            'shipping_method_id' => ShippingMethod::query()->inRandomOrder()->value('id') ?? ShippingMethod::factory(),
            'city' => $this->faker->optional()->randomElement(['Riyadh','Jeddah','Dammam']),
            'district' => $this->faker->optional()->word(),
            'min_weight' => $this->faker->optional()->randomFloat(2, 0, 2),
            'max_weight' => $this->faker->optional()->randomFloat(2, 2, 20),
            'price' => $this->faker->randomFloat(2, 10, 80),
            'eta_min_days' => $this->faker->optional()->numberBetween(1, 3),
            'eta_max_days' => $this->faker->optional()->numberBetween(3, 10),
            'free_shipping_threshold' => $this->faker->optional()->randomFloat(2, 200, 2000),
        ];
    }
}
