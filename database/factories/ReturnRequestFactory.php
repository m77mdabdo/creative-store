<?php

namespace Database\Factories;

use App\Models\ReturnRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReturnRequestFactory extends Factory
{
    protected $model = ReturnRequest::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::query()->inRandomOrder()->value('id') ?? Order::factory(),
            'user_id' => $this->faker->boolean(80) ? (User::query()->inRandomOrder()->value('id') ?? User::factory()) : null,
            'status' => $this->faker->randomElement(['requested','approved','rejected','received','refunded','partial_refunded']),
            'reason' => $this->faker->optional()->sentence(),
            'refund_shipping' => $this->faker->boolean(30),
            'refund_tax' => $this->faker->boolean(80),
            'shipping_refund_amount' => $this->faker->randomFloat(2, 0, 50),
            'tax_refund_amount' => $this->faker->randomFloat(2, 0, 100),
            'requested_at' => $this->faker->optional()->dateTimeBetween('-30 days','now'),
            'processed_at' => $this->faker->optional()->dateTimeBetween('-20 days','now'),
        ];
    }
}
