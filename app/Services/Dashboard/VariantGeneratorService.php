<?php

namespace App\Services\Dashboard;

use App\Models\Attribute;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VariantGeneratorService
{
 public function generateMissingVariants(Product $product, array $attributeIds): int
{
    $allowedAttributeIds = $product->attributes()->pluck('attributes.id')->map(fn ($id) => (int) $id)->all();

    $attributeIds = collect($attributeIds)
        ->map(fn ($id) => (int) $id)
        ->filter(fn ($id) => in_array($id, $allowedAttributeIds, true))
        ->values()
        ->all();

    $attributes = Attribute::query()
        ->whereIn('id', $attributeIds)
        ->where('is_active', true)
        ->with([
            'values' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('id')
        ])
        ->get();

    if ($attributes->isEmpty() || $attributes->contains(fn ($attr) => $attr->values->isEmpty())) {
        return 0;
    }

    $groups = $attributes->map(fn ($attribute) => $attribute->values->map(fn ($value) => [
        'attribute_id' => $attribute->id,
        'attribute_value_id' => $value->id,
    ])->all())->all();

    $combinations = $this->buildCombinations($groups);
    $created = 0;

    DB::transaction(function () use ($product, $combinations, &$created) {
        foreach ($combinations as $combo) {
            $key = $this->buildCombinationKey(collect($combo)->pluck('attribute_value_id')->all());

            $exists = $product->variants()->where('combination_key', $key)->exists();

            if ($exists) {
                continue;
            }

            $sku = $this->generateUniqueVariantSku($product);

            $variant = $product->variants()->create([
                'sku' => $sku,
                'price' => $product->base_price,
                'stock' => 0,
                'combination_key' => $key,
                'weight' => $product->weight,
                'version' => 1,
                'is_active' => $product->status === 'active',
            ]);

            foreach ($combo as $row) {
                $variant->values()->create([
                    'attribute_id' => $row['attribute_id'],
                    'attribute_value_id' => $row['attribute_value_id'],
                ]);
            }

            $created++;
        }

        if ($created > 0 && ! $product->has_variants) {
            $product->update(['has_variants' => true]);
        }
    });

    return $created;
}

    private function buildCombinationKey(array $attributeValueIds): string
    {
        sort($attributeValueIds);
        return implode('-', $attributeValueIds);
    }

    private function buildCombinations(array $groups): array
    {
        $result = [[]];

        foreach ($groups as $group) {
            $append = [];

            foreach ($result as $base) {
                foreach ($group as $item) {
                    $append[] = array_merge($base, [$item]);
                }
            }

            $result = $append;
        }

        return $result;
    }
    private function generateUniqueVariantSku(Product $product): string
{
    do {
        $sku = strtoupper($product->sku . '-' . \Illuminate\Support\Str::random(6));
    } while (\App\Models\ProductVariant::where('sku', $sku)->exists());

    return $sku;
}
}
