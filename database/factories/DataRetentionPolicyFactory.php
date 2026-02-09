<?php

namespace Database\Factories;

use App\Models\DataRetentionPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataRetentionPolicyFactory extends Factory
{
    protected $model = DataRetentionPolicy::class;

    public function definition(): array
    {
        return [
            'target_table' => $this->faker->unique()->randomElement([
                'audit_logs','error_logs','rate_limit_logs','payment_webhook_logs'
            ]),
            'retain_days' => $this->faker->numberBetween(7, 365),
            'is_anonymize' => $this->faker->boolean(30),
            'is_active' => true,
        ];
    }
}
