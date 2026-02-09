<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\Order;
use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::query()->inRandomOrder()->value('id') ?? Order::factory(),
            'shipping_method_id' => $this->faker->boolean(80) ? (ShippingMethod::query()->inRandomOrder()->value('id') ?? ShippingMethod::factory()) : null,
            'carrier' => $this->faker->optional()->randomElement(['SMSA','Aramex','DHL']),
            'tracking_number' => $this->faker->optional()->bothify('TRK-########'),
            'status' => $this->faker->randomElement(['pending','picked','in_transit','delivered','failed_return']),
            'shipped_at' => $this->faker->optional()->dateTimeBetween('-15 days','now'),
            'delivered_at' => $this->faker->optional()->dateTimeBetween('-10 days','now'),
        ];
    }
}
