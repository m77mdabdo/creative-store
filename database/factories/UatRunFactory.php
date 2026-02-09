<?php

namespace Database\Factories;

use App\Models\UatRun;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UatRunFactory extends Factory
{
    protected $model = UatRun::class;

    public function definition(): array
    {
        $started = $this->faker->optional()->dateTimeBetween('-30 days','now');
        return [
            'name' => $this->faker->words(3, true),
            'environment' => $this->faker->randomElement(['local','staging','production']),
            'status' => $this->faker->randomElement(['pending','running','passed','failed']),
            'started_at' => $started,
            'finished_at' => $started ? (clone $started)->modify('+'.rand(1,120).' minutes') : null,
            'executed_by' => $this->faker->boolean(70) ? (User::query()->inRandomOrder()->value('id') ?? User::factory()) : null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
