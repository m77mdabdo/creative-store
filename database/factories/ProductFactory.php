<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::query()->inRandomOrder()->value('id') ?? Category::factory(),
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'slug' => $this->faker->unique()->slug(3),
            'sku' => $this->faker->unique()->bothify('SKU-####-??'),
            'base_price' => $this->faker->randomFloat(2, 10, 999),
            'currency' => 'SAR',
            'status' => $this->faker->randomElement(['draft','active','archived']),
            'has_variants' => $this->faker->boolean(45),
            'tax_rate' => $this->faker->randomFloat(2, 0, 15),
            'weight' => $this->faker->optional()->randomFloat(2, 0.1, 10),
            'is_featured' => $this->faker->boolean(15),
            'meta_title' => $this->faker->optional()->sentence(4),
            'meta_description' => $this->faker->optional()->sentence(12),
        ];
    }
}
