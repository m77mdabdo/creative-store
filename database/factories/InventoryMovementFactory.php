<?php

namespace Database\Factories;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryMovementFactory extends Factory
{
    protected $model = InventoryMovement::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
            'variant_id' => null,
            'type' => $this->faker->randomElement(['restock','sale','return','adjustment','reservation','release']),
            'quantity' => $this->faker->numberBetween(1, 50),
            'reference_type' => $this->faker->optional()->randomElement(['orders','returns','carts']),
            'reference_id' => $this->faker->optional()->numberBetween(1, 9999),
            'note' => $this->faker->optional()->sentence(),
            'created_by' => $this->faker->boolean(60) ? (User::query()->inRandomOrder()->value('id') ?? User::factory()) : null,
        ];
    }
}
