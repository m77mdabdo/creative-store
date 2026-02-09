<?php

namespace Database\Factories;

use App\Models\ProductVariantValue;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantValueFactory extends Factory
{
    protected $model = ProductVariantValue::class;

    public function definition(): array
    {
        return [
            'product_variant_id' => ProductVariant::query()->inRandomOrder()->value('id') ?? ProductVariant::factory(),
            'attribute_id' => Attribute::query()->inRandomOrder()->value('id') ?? Attribute::factory(),
            'attribute_value_id' => AttributeValue::query()->inRandomOrder()->value('id') ?? AttributeValue::factory(),
        ];
    }
}
