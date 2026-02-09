<?php

namespace Database\Factories;

use App\Models\Refund;
use App\Models\Payment;
use App\Models\ReturnRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefundFactory extends Factory
{
    protected $model = Refund::class;

    public function definition(): array
    {
        return [
            'payment_id' => Payment::query()->inRandomOrder()->value('id') ?? Payment::factory(),
            'return_id' => $this->faker->boolean(60) ? (ReturnRequest::query()->inRandomOrder()->value('id') ?? ReturnRequest::factory()) : null,
            'amount' => $this->faker->randomFloat(2, 1, 3000),
            'status' => $this->faker->randomElement(['pending','processed','failed']),
            'transaction_id' => $this->faker->optional()->uuid(),
            'processed_at' => $this->faker->optional()->dateTimeBetween('-30 days','now'),
        ];
    }
}
