<?php

namespace Database\Factories;

use App\Models\CouponUsage;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponUsageFactory extends Factory
{
    protected $model = CouponUsage::class;

    public function definition(): array
    {
        return [
            'coupon_id' => Coupon::query()->inRandomOrder()->value('id') ?? Coupon::factory(),
            'user_id' => $this->faker->boolean(80) ? (User::query()->inRandomOrder()->value('id') ?? User::factory()) : null,
            'order_id' => Order::query()->inRandomOrder()->value('id') ?? Order::factory(),
            'discount_amount' => $this->faker->randomFloat(2, 1, 200),
        ];
    }
}
