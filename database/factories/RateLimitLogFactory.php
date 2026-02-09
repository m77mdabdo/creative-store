<?php

namespace Database\Factories;

use App\Models\RateLimitLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class RateLimitLogFactory extends Factory
{
    protected $model = RateLimitLog::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->bothify('ip:###.###.###.###'),
            'endpoint' => $this->faker->randomElement(['/api/login','/api/orders','/api/products']),
            'hits' => $this->faker->numberBetween(1, 500),
            'window_start' => now()->subMinutes($this->faker->numberBetween(1, 1000)),
            'blocked_until' => $this->faker->optional()->dateTimeBetween('now','+2 hours'),
        ];
    }
}
