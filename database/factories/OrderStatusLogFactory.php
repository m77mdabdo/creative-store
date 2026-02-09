<?php

namespace Database\Factories;

use App\Models\OrderStatusLog;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusLogFactory extends Factory
{
    protected $model = OrderStatusLog::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::query()->inRandomOrder()->value('id') ?? Order::factory(),
            'from_status' => $this->faker->optional()->randomElement(['pending','confirmed','processing','shipped']),
            'to_status' => $this->faker->randomElement(['confirmed','processing','shipped','delivered','cancelled']),
            'changed_by' => $this->faker->boolean(70) ? (User::query()->inRandomOrder()->value('id') ?? User::factory()) : null,
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}
