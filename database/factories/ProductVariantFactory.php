<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
            'sku' => $this->faker->unique()->bothify('VSKU-####-??'),
            'price' => $this->faker->optional()->randomFloat(2, 10, 999),
            'stock' => $this->faker->numberBetween(0, 250),
            'combination_key' => $this->faker->optional()->slug(3),
            'weight' => $this->faker->optional()->randomFloat(2, 0.1, 10),
            'version' => 1,
            'is_active' => true,
        ];
    }
}
