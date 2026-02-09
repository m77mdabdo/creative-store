<?php

namespace Database\Factories;

use App\Models\PaymentWebhookLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentWebhookLogFactory extends Factory
{
    protected $model = PaymentWebhookLog::class;

    public function definition(): array
    {
        return [
            'gateway' => $this->faker->randomElement(['hyperpay','moyasar','stripe','tap']),
            'event_id' => $this->faker->optional()->uuid(),
            'event_type' => $this->faker->optional()->word(),
            'payload' => ['event' => 'mock', 'ok' => true],
            'processed' => $this->faker->boolean(60),
            'processed_at' => $this->faker->optional()->dateTimeBetween('-30 days','now'),
        ];
    }
}
