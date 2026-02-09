<?php

namespace Database\Factories;

use App\Models\DailySalesAggregate;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailySalesAggregateFactory extends Factory
{
    protected $model = DailySalesAggregate::class;

    public function definition(): array
    {
        $orders = $this->faker->numberBetween(0, 100);
        $paid = $this->faker->numberBetween(0, $orders);
        $gross = $this->faker->randomFloat(2, 0, 50000);
        $discount = $this->faker->randomFloat(2, 0, 5000);
        $shipping = $this->faker->randomFloat(2, 0, 2000);
        $tax = $this->faker->randomFloat(2, 0, 4000);
        $net = max(0, $gross - $discount);

        return [
            'day' => $this->faker->unique()->dateTimeBetween('-30 days','now')->format('Y-m-d'),
            'orders_count' => $orders,
            'paid_orders_count' => $paid,
            'gross_sales' => $gross,
            'discount_total' => $discount,
            'shipping_total' => $shipping,
            'tax_total' => $tax,
            'net_sales' => $net,
        ];
    }
}
