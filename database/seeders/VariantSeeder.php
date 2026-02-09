<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        /* =========================
         * Preconditions
         * ========================= */
        if (Product::count() === 0) {
            $this->command->warn('No products found. Run CatalogSeeder first.');
            return;
        }

        /* =========================
         * Attributes
         * ========================= */
        $attributesData = [
            'color' => ['Red', 'Blue', 'Black', 'White'],
            'size'  => ['S', 'M', 'L', 'XL'],
            'material' => ['Cotton', 'Leather', 'Polyester'],
        ];

        $attributes = [];

        foreach ($attributesData as $attrSlug => $values) {
            $attribute = Attribute::firstOrCreate(
                ['slug' => $attrSlug],
                [
                    'name' => ucfirst($attrSlug),
                    'is_active' => true,
                ]
            );

            foreach ($values as $i => $value) {
                AttributeValue::firstOrCreate(
                    [
                        'attribute_id' => $attribute->id,
                        'slug' => strtolower($value),
                    ],
                    [
                        'value' => $value,
                        'sort_order' => $i,
                        'is_active' => true,
                    ]
                );
            }

            $attributes[$attrSlug] = $attribute;
        }

        /* =========================
         * Attach attributes to products
         * ========================= */
        $products = Product::inRandomOrder()->take(20)->get();

        foreach ($products as $product) {
            // المنتج ده هيبقى له variants
            $product->update(['has_variants' => true]);

            // ربط attributes بالمنتج
            foreach ($attributes as $attribute) {
                ProductAttribute::firstOrCreate([
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                ]);
            }

            /* =========================
             * Create variants
             * ========================= */
            $colors = AttributeValue::where('attribute_id', $attributes['color']->id)->get();
            $sizes  = AttributeValue::where('attribute_id', $attributes['size']->id)->get();

            foreach ($colors as $color) {
                foreach ($sizes as $size) {

                    $combinationKey = 'color='.$color->slug.'|size='.$size->slug;

                    $variant = ProductVariant::firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'combination_key' => $combinationKey,
                        ],
                        [
                            'sku' => strtoupper($product->slug).'-'.$color->slug.'-'.$size->slug,
                            'price' => $product->base_price + rand(5, 25),
                            'stock' => rand(0, 50),
                            'weight' => rand(1, 5),
                            'version' => 1,
                            'is_active' => true,
                        ]
                    );

                    /* =========================
                     * Variant values
                     * ========================= */
                    ProductVariantValue::firstOrCreate([
                        'product_variant_id' => $variant->id,
                        'attribute_id' => $attributes['color']->id,
                        'attribute_value_id' => $color->id,
                    ]);

                    ProductVariantValue::firstOrCreate([
                        'product_variant_id' => $variant->id,
                        'attribute_id' => $attributes['size']->id,
                        'attribute_value_id' => $size->id,
                    ]);
                }
            }
        }
    }
}
