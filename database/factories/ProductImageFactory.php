<?php

namespace Database\Factories;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
            'variant_id' => null,
            'path' => 'products/'.$this->faker->uuid().'.jpg',
            'alt_text' => $this->faker->optional()->sentence(3),
            'is_primary' => false,
            'sort_order' => $this->faker->numberBetween(0, 20),
        ];
    }
}
