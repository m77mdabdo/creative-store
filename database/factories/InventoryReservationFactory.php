<?php

namespace Database\Factories;

use App\Models\InventoryReservation;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryReservationFactory extends Factory
{
    protected $model = InventoryReservation::class;

    public function definition(): array
    {
        return [
            'cart_id' => Cart::query()->inRandomOrder()->value('id') ?? Cart::factory(),
            'product_id' => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
            'variant_id' => null,
            'quantity' => $this->faker->numberBetween(1, 5),
            'expires_at' => now()->addMinutes($this->faker->numberBetween(10, 120)),
            'released_at' => null,
        ];
    }
}
