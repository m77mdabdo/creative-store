<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('SALE##??')),
            'type' => $this->faker->randomElement(['fixed','percent']),
            'value' => $this->faker->randomFloat(2, 5, 50),
            'min_order_amount' => $this->faker->optional()->randomFloat(2, 50, 500),
            'max_discount_amount' => $this->faker->optional()->randomFloat(2, 20, 200),
            'usage_limit' => $this->faker->optional()->numberBetween(10, 500),
            'per_user_limit' => $this->faker->optional()->numberBetween(1, 5),
            'used_count' => 0,
            'starts_at' => $this->faker->optional()->dateTimeBetween('-10 days','now'),
            'expires_at' => $this->faker->optional()->dateTimeBetween('now','+30 days'),
            'is_active' => true,
        ];
    }
}
