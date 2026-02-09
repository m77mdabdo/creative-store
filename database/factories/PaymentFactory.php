<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::query()->inRandomOrder()->value('id') ?? Order::factory(),
            'gateway' => $this->faker->randomElement(['hyperpay','moyasar','stripe','tap']),
            'transaction_id' => $this->faker->optional()->uuid(),
            'idempotency_key' => $this->faker->uuid(),
            'amount' => $this->faker->randomFloat(2, 10, 3000),
            'currency' => 'SAR',
            'exchange_rate' => 1.000000,
            'status' => $this->faker->randomElement(['pending','authorized','captured','failed','refunded','partially_refunded']),
            'raw_response' => ['mock' => true],
            'paid_at' => $this->faker->optional()->dateTimeBetween('-30 days','now'),
        ];
    }
}
