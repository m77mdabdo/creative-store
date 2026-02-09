<?php

namespace Database\Factories;

use App\Models\AttributeValue;
use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeValueFactory extends Factory
{
    protected $model = AttributeValue::class;

    public function definition(): array
    {
        return [
            'attribute_id' => Attribute::query()->inRandomOrder()->value('id') ?? Attribute::factory(),
            'value' => $this->faker->word(),
            'slug' => $this->faker->slug(2),
            'sort_order' => $this->faker->numberBetween(0, 50),
            'is_active' => true,
        ];
    }
}
