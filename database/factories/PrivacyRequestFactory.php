<?php

namespace Database\Factories;

use App\Models\PrivacyRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrivacyRequestFactory extends Factory
{
    protected $model = PrivacyRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->boolean(70) ? (User::query()->inRandomOrder()->value('id') ?? User::factory()) : null,
            'email' => $this->faker->optional()->safeEmail(),
            'type' => $this->faker->randomElement(['export','anonymize','delete']),
            'status' => $this->faker->randomElement(['pending','approved','rejected','completed']),
            'requested_at' => now()->subDays($this->faker->numberBetween(0, 30)),
            'processed_at' => $this->faker->optional()->dateTimeBetween('-30 days','now'),
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}
