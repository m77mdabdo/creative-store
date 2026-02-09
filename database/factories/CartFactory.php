<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->boolean(70)
                ? (User::query()->inRandomOrder()->value('id') ?? User::factory())
                : null,
            'session_id' => $this->faker->optional()->uuid(),
        ];
    }
}
