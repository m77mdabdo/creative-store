<?php

namespace Database\Factories;

use App\Models\ErrorLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ErrorLogFactory extends Factory
{
    protected $model = ErrorLog::class;

    public function definition(): array
    {
        return [
            'level' => $this->faker->randomElement(['info','warning','error','critical']),
            'service' => $this->faker->optional()->word(),
            'message' => $this->faker->sentence(),
            'context' => $this->faker->boolean(50) ? ['ctx' => true] : null,
            'trace' => $this->faker->optional()->text(400),
            'resolved_at' => $this->faker->optional()->dateTimeBetween('-30 days','now'),
        ];
    }
}
