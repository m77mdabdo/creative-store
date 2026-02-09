<?php

namespace Database\Factories;

use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        $productId = Product::query()->inRandomOrder()->value('id') ?? Product::factory();
        return [
            'cart_id' => Cart::query()->inRandomOrder()->value('id') ?? Cart::factory(),
            'product_id' => $productId,
            'variant_id' => null, // هنسيبها null غالباً
            'quantity' => $this->faker->numberBetween(1, 5),
            'price_snapshot' => $this->faker->randomFloat(2, 10, 999),
            'tax_rate_snapshot' => $this->faker->randomFloat(2, 0, 15),
        ];
    }
}
