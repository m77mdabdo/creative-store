<?php

namespace Database\Factories;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['Color','Size','Material']);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
        ];
    }
}
