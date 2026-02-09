<?php

namespace Database\Factories;

use App\Models\ProductTranslation;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductTranslationFactory extends Factory
{
    protected $model = ProductTranslation::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
            'locale' => $this->faker->randomElement(['ar','en']),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->paragraph(),
        ];
    }
}
