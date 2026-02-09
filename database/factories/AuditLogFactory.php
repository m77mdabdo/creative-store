<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->boolean(70) ? (User::query()->inRandomOrder()->value('id') ?? User::factory()) : null,
            'action' => $this->faker->randomElement(['create','update','delete','login']),
            'auditable_type' => $this->faker->randomElement(['Product','Order','User']),
            'auditable_id' => $this->faker->numberBetween(1, 5000),
            'old_values' => $this->faker->boolean(50) ? ['old' => true] : null,
            'new_values' => $this->faker->boolean(50) ? ['new' => true] : null,
            'ip_address' => $this->faker->optional()->ipv4(),
        ];
    }
}
