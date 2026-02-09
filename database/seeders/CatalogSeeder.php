<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductImage;
use App\Models\User;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        /* =========================
         * تأكيد وجود Users
         * ========================= */
        if (User::count() === 0) {
            User::factory(10)->create();
        }

        $users = User::all();

        /* =========================
         * Categories
         * ========================= */
        $categories = Category::factory()->count(8)->create();

        foreach ($categories as $category) {
            // English
            CategoryTranslation::factory()->create([
                'category_id' => $category->id,
                'locale' => 'en',
                'name' => fake()->words(2, true),
                'description' => fake()->optional()->paragraph(),
            ]);

            // Arabic
            CategoryTranslation::factory()->create([
                'category_id' => $category->id,
                'locale' => 'ar',
                'name' => fake()->words(2, true),
                'description' => fake()->optional()->paragraph(),
            ]);
        }

        /* =========================
         * Products
         * ========================= */
        $products = Product::factory()->count(40)->create([
            'user_id' => $users->random()->id,
            'category_id' => $categories->random()->id,
        ]);

        foreach ($products as $product) {
            // Product Translations
            ProductTranslation::factory()->create([
                'product_id' => $product->id,
                'locale' => 'en',
                'name' => fake()->words(3, true),
                'description' => fake()->optional()->paragraph(),
            ]);

            ProductTranslation::factory()->create([
                'product_id' => $product->id,
                'locale' => 'ar',
                'name' => fake()->words(3, true),
                'description' => fake()->optional()->paragraph(),
            ]);

            // Primary Image
            ProductImage::factory()->create([
                'product_id' => $product->id,
                'variant_id' => null,
                'is_primary' => true,
                'sort_order' => 0,
                'path' => 'products/'.fake()->uuid().'.jpg',
            ]);

            // Additional Images
            ProductImage::factory()->count(2)->create([
                'product_id' => $product->id,
                'variant_id' => null,
                'is_primary' => false,
                'sort_order' => fake()->numberBetween(1, 10),
                'path' => 'products/'.fake()->uuid().'.jpg',
            ]);
        }
    }
}
