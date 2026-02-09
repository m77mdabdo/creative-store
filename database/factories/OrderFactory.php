<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50, 3000);
        $discount = $this->faker->randomFloat(2, 0, 200);
        $shipping = $this->faker->randomFloat(2, 0, 60);
        $tax = $this->faker->randomFloat(2, 0, 150);
        $total = max(0, $subtotal - $discount) + $shipping + $tax;

        return [
            'order_number' => $this->faker->unique()->bothify('ORD-########'),
            'user_id' => $this->faker->boolean(85) ? (User::query()->inRandomOrder()->value('id') ?? User::factory()) : null,
            'shipping_address_id' => $this->faker->boolean(80) ? (Address::query()->inRandomOrder()->value('id') ?? Address::factory()) : null,
            'coupon_id' => $this->faker->boolean(30) ? (Coupon::query()->inRandomOrder()->value('id') ?? Coupon::factory()) : null,

            'guest_email' => null,
            'guest_phone' => null,

            'currency' => 'SAR',
            'exchange_rate' => 1.000000,

            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping_fee' => $shipping,
            'tax_total' => $tax,
            'total' => $total,

            'status' => $this->faker->randomElement(['pending','confirmed','processing','shipped','delivered','cancelled']),
            'payment_status' => $this->faker->randomElement(['unpaid','paid','failed','refunded','partially_refunded']),
            'payment_method' => $this->faker->optional()->randomElement(['mada','visa','mastercard','applepay','cash']),

            'placed_at' => $this->faker->optional()->dateTimeBetween('-30 days','now'),
            'paid_at' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'cancellation_reason' => null,
        ];
    }
}
