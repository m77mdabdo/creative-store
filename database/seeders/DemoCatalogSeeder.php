<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * A realistic storefront catalogue: 9 categories, ~90 products in Arabic and
 * English, colour / size variants with stock, and product photography.
 *
 * Photos come from Unsplash (free for commercial use under the Unsplash
 * License). They are downloaded once into the media disk; if a download
 * fails the CDN URL is stored instead, so the seeder never blocks.
 *
 * Safe to run repeatedly: everything is keyed by slug / combination.
 *
 *   php artisan db:seed --class=DemoCatalogSeeder
 */
class DemoCatalogSeeder extends Seeder
{
    private const IMAGE_URL = 'https://images.unsplash.com/%s?w=900&h=1125&fit=crop&crop=entropy&q=80&fm=jpg';

    private const IMAGE_DIR = 'products/catalog';

    private const CATEGORIES = [
        'women'      => ['en' => 'Women',      'ar' => 'ملابس نسائية',   'sizes' => 'apparel'],
        'men'        => ['en' => 'Men',        'ar' => 'ملابس رجالية',   'sizes' => 'apparel'],
        'outerwear'  => ['en' => 'Outerwear',  'ar' => 'جاكيتات وهوديز', 'sizes' => 'apparel'],
        'shoes'      => ['en' => 'Shoes',      'ar' => 'أحذية',          'sizes' => 'shoes'],
        'bags'       => ['en' => 'Bags',       'ar' => 'حقائب',          'sizes' => null],
        'watches'    => ['en' => 'Watches',    'ar' => 'ساعات',          'sizes' => null],
        'sunglasses' => ['en' => 'Sunglasses', 'ar' => 'نظارات شمسية',   'sizes' => null],
        'jewelry'    => ['en' => 'Jewelry',    'ar' => 'مجوهرات',        'sizes' => null],
        'fragrances' => ['en' => 'Fragrances', 'ar' => 'عطور',           'sizes' => 'volume'],
    ];

    private const SIZE_SETS = [
        'apparel' => ['XS', 'S', 'M', 'L', 'XL'],
        'shoes'   => ['37', '38', '39', '40', '41', '42', '43', '44'],
        'volume'  => ['50ml', '100ml'],
    ];

    private const DESCRIPTIONS = [
        'women'      => ['Thoughtfully cut and easy to style, made to move with you from day to evening.', 'قصة مدروسة وسهلة التنسيق، مصممة لتناسبك من النهار حتى المساء.'],
        'men'        => ['Tailored for a clean fit in breathable fabric that keeps its shape wash after wash.', 'تفصيل أنيق وقماش يسمح بمرور الهواء ويحافظ على شكله بعد كل غسلة.'],
        'outerwear'  => ['A layering essential built for comfort, with durable stitching and a relaxed silhouette.', 'قطعة أساسية للطبقات، مريحة بخياطة متينة وقصة مريحة.'],
        'shoes'      => ['Cushioned for all-day wear with a durable outsole and a modern profile.', 'نعل مبطن للراحة طوال اليوم، بتصميم عصري ونعل خارجي متين.'],
        'bags'       => ['Spacious interior, quality hardware and a finish that ages beautifully.', 'مساحة داخلية واسعة، إكسسوارات معدنية عالية الجودة وخامة تزداد جمالًا مع الوقت.'],
        'watches'    => ['Precision movement, scratch-resistant crystal and water resistance for daily wear.', 'حركة دقيقة، زجاج مقاوم للخدش ومقاومة للماء للاستخدام اليومي.'],
        'sunglasses' => ['UV400 lenses with a lightweight frame for comfortable all-day protection.', 'عدسات UV400 بإطار خفيف لحماية مريحة طوال اليوم.'],
        'jewelry'    => ['Hypoallergenic finish, hand-polished and presented in a gift box.', 'طلاء آمن للبشرة الحساسة، مصقول يدويًا ويأتي في علبة هدية.'],
        'fragrances' => ['A long-lasting composition with a refined trail, crafted for every occasion.', 'تركيبة ثابتة بأثر راقٍ، مصممة لكل المناسبات.'],
    ];

    private array $valueCache = [];

    public function run(): void
    {
        $color = $this->attribute('color', 'Color');
        $size = $this->attribute('size', 'Size');
        $categories = $this->seedCategories();
        $products = DemoCatalogData::products();

        $this->command?->info('Seeding ' . count($products) . ' products (downloading photos)...');

        foreach ($products as $index => [$categoryKey, $photo, $nameEn, $nameAr, $price, $colors]) {
            $sizeSet = self::CATEGORIES[$categoryKey]['sizes'];
            $imagePath = $this->downloadImage($photo);

            DB::transaction(fn () => $this->seedProduct(
                category: $categories[$categoryKey],
                key: $categoryKey,
                position: $index + 1,
                imagePath: $imagePath,
                nameEn: $nameEn,
                nameAr: $nameAr,
                price: $price,
                groups: array_values(array_filter([
                    $colors !== [] ? [$color, $colors] : null,
                    $sizeSet ? [$size, self::SIZE_SETS[$sizeSet]] : null,
                ])),
            ));

            $this->command?->getOutput()->write('.');
        }

        $this->command?->newLine();

        $archived = $this->archiveFactoryPlaceholders(array_keys(self::CATEGORIES));

        $this->command?->info("Catalogue ready. Archived {$archived} placeholder product(s) from the old faker seed.");
    }

    /**
     * Hide the lorem-ipsum products generated by ProductFactory (SKU-1234-ab)
     * so the storefront shows the real catalogue. Products are archived, not
     * deleted, and categories left without active products are deactivated.
     *
     * @param  list<string>  $keepCategorySlugs
     */
    private function archiveFactoryPlaceholders(array $keepCategorySlugs): int
    {
        $ids = Product::query()
            ->where('sku', 'like', 'SKU-%')
            ->where('status', '!=', 'archived')
            ->pluck('sku', 'id')
            ->filter(fn (string $sku) => (bool) preg_match('/^SKU-\d{4}-[a-z]{2}$/i', $sku))
            ->keys();

        Product::query()->whereKey($ids)->update(['status' => 'archived', 'is_featured' => false]);

        Category::query()
            ->whereNotIn('slug', $keepCategorySlugs)
            ->whereDoesntHave('products', fn ($q) => $q->where('status', 'active'))
            ->update(['is_active' => false]);

        return $ids->count();
    }

    /**
     * @return array<string, Category>
     */
    private function seedCategories(): array
    {
        $categories = [];
        $order = 1;

        foreach (self::CATEGORIES as $slug => $names) {
            $category = Category::withTrashed()->updateOrCreate(
                ['slug' => $slug],
                ['is_active' => true, 'sort_order' => $order++, 'parent_id' => null],
            );

            if ($category->trashed()) {
                $category->restore();
            }

            foreach (['en', 'ar'] as $locale) {
                $category->translateOrNew($locale)->name = $names[$locale];
            }

            $category->save();
            $categories[$slug] = $category;
        }

        return $categories;
    }

    /**
     * @param  list<array{0: Attribute, 1: list<string>}>  $groups
     */
    private function seedProduct(
        Category $category,
        string $key,
        int $position,
        string $imagePath,
        string $nameEn,
        string $nameAr,
        int $price,
        array $groups,
    ): void {
        $slug = Str::slug($nameEn);
        $sku = sprintf('CS-%s-%03d', strtoupper(substr($key, 0, 3)), $position);
        [$descriptionEn, $descriptionAr] = self::DESCRIPTIONS[$key];

        $product = Product::withTrashed()->updateOrCreate(['slug' => $slug], [
            'category_id'  => $category->id,
            'sku'          => $sku,
            'base_price'   => $price,
            'currency'     => 'SAR',
            'status'       => 'active',
            'has_variants' => $groups !== [],
            'is_featured'  => $position % 7 === 0,
            'weight'       => in_array($key, ['shoes', 'bags', 'outerwear'], true) ? 1.2 : 0.4,
        ]);

        if ($product->trashed()) {
            $product->restore();
        }

        $product->translateOrNew('en')->fill(['name' => $nameEn, 'description' => $descriptionEn]);
        $product->translateOrNew('ar')->fill(['name' => $nameAr, 'description' => $descriptionAr]);
        $product->save();

        ProductImage::query()->updateOrCreate(
            ['product_id' => $product->id, 'variant_id' => null, 'is_primary' => true],
            ['path' => $imagePath, 'alt_text' => $nameEn, 'sort_order' => 0],
        );

        foreach ($groups as [$attribute]) {
            ProductAttribute::query()->firstOrCreate(['product_id' => $product->id, 'attribute_id' => $attribute->id]);
        }

        foreach ($this->combinations($groups) as $combo) {
            $values = array_map(fn ($pair) => $this->value($pair[0], $pair[1]), $combo);
            $ids = array_map(fn (AttributeValue $v) => $v->id, $values);
            sort($ids);

            $suffix = implode('-', array_map(fn ($pair) => Str::upper(Str::slug($pair[1])), $combo));

            $variant = ProductVariant::query()->updateOrCreate(
                ['product_id' => $product->id, 'combination_key' => implode('-', $ids)],
                [
                    'sku'       => "{$sku}-{$suffix}",
                    'price'     => $price,
                    'stock'     => random_int(0, 9) === 0 ? 0 : random_int(5, 60),
                    'weight'    => $product->weight,
                    'is_active' => true,
                ],
            );

            foreach ($values as $value) {
                ProductVariantValue::query()->updateOrCreate(
                    ['product_variant_id' => $variant->id, 'attribute_id' => $value->attribute_id],
                    ['attribute_value_id' => $value->id],
                );
            }
        }
    }

    /**
     * Cartesian product of [attribute, labels] groups.
     *
     * @return list<list<array{0: Attribute, 1: string}>>
     */
    private function combinations(array $groups): array
    {
        if ($groups === []) {
            return [];
        }

        $result = [[]];

        foreach ($groups as [$attribute, $labels]) {
            $next = [];

            foreach ($result as $partial) {
                foreach ($labels as $label) {
                    $next[] = [...$partial, [$attribute, $label]];
                }
            }

            $result = $next;
        }

        return $result;
    }

    private function attribute(string $slug, string $name): Attribute
    {
        return Attribute::query()->firstOrCreate(['slug' => $slug], ['name' => $name, 'is_active' => true]);
    }

    private function value(Attribute $attribute, string $label): AttributeValue
    {
        $slug = Str::slug($label);

        return $this->valueCache["{$attribute->id}:{$slug}"] ??= AttributeValue::query()->firstOrCreate(
            ['attribute_id' => $attribute->id, 'slug' => $slug],
            ['value' => $label, 'sort_order' => 0, 'is_active' => true],
        );
    }

    /**
     * Store the photo on the media disk once; fall back to the CDN URL.
     */
    private function downloadImage(string $photoId): string
    {
        $path = self::IMAGE_DIR . "/{$photoId}.jpg";

        if (media_disk()->exists($path)) {
            return $path;
        }

        $url = sprintf(self::IMAGE_URL, $photoId);

        try {
            $response = Http::timeout(30)->retry(2, 500, throw: false)->get($url);

            if ($response->successful() && str_starts_with((string) $response->header('Content-Type'), 'image/')) {
                media_disk()->put($path, $response->body());

                return $path;
            }
        } catch (\Throwable) {
            // Offline or blocked: keep the remote URL so the product still shows a photo.
        }

        return $url;
    }
}
