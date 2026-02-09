<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'title' => $this->faker->optional()->sentence(3),
            'comment' => $this->faker->optional()->paragraph(),
            'is_approved' => $this->faker->boolean(70),
        ];
    }
}
