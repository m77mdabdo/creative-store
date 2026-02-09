<?php

namespace Database\Factories;

use App\Models\OrderAddressSnapshot;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderAddressSnapshotFactory extends Factory
{
    protected $model = OrderAddressSnapshot::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::query()->inRandomOrder()->value('id') ?? Order::factory(),
            'full_name' => $this->faker->name(),
            'phone' => $this->faker->numerify('05########'),
            'country' => 'Saudi Arabia',
            'city' => $this->faker->randomElement(['Riyadh','Jeddah','Dammam']),
            'district' => $this->faker->optional()->word(),
            'street' => $this->faker->streetName(),
            'building_no' => $this->faker->optional()->numerify('##'),
            'apartment_no' => $this->faker->optional()->numerify('##'),
            'postal_code' => $this->faker->optional()->postcode(),
            'lat' => $this->faker->optional()->latitude(16, 32),
            'lng' => $this->faker->optional()->longitude(34, 56),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
