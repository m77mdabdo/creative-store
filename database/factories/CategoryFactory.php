<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'slug' => $this->faker->unique()->slug(2),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 50),
            'meta_title' => $this->faker->optional()->sentence(4),
            'meta_description' => $this->faker->optional()->sentence(12),
        ];
    }
}
