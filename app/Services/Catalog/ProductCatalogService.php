<?php

namespace App\Services\Catalog;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Read-side queries for the storefront catalogue.
 */
class ProductCatalogService
{
    public function topCategories(): Collection
    {
        [$locale, $fallback] = $this->locales();

        return Category::query()
            ->select(['id', 'parent_id', 'slug', 'sort_order'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with(['translations' => fn ($q) => $q
                ->select(['id', 'category_id', 'locale', 'name'])
                ->whereIn('locale', [$locale, $fallback])])
            ->orderBy('sort_order')
            ->get()
            ->each(function (Category $category) use ($locale, $fallback) {
                $category->display_name = self::translated($category->translations, 'name', $locale, $fallback, $category->slug);
            });
    }

    /**
     * Top-level categories that have products on sale, each with a product
     * count and a cover photo taken from one of its products. One query for
     * all tiles (correlated sub-select), cached for ten minutes.
     */
    public function categoryTiles(int $limit = 9): Collection
    {
        [$locale, $fallback] = $this->locales();

        $rows = \Illuminate\Support\Facades\Cache::remember("catalog.category-tiles.{$limit}", now()->addMinutes(10), function () use ($limit) {
            return Category::query()
                ->select(['id', 'slug', 'sort_order'])
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->withCount(['products as active_products_count' => fn ($q) => $q->where('status', 'active')])
                ->addSelect(['cover_path' => \App\Models\ProductImage::query()
                    ->select('product_images.path')
                    ->join('products', 'products.id', '=', 'product_images.product_id')
                    ->whereColumn('products.category_id', 'categories.id')
                    ->where('products.status', 'active')
                    ->whereNull('products.deleted_at')
                    ->where('product_images.is_primary', true)
                    ->orderByDesc('products.is_featured')
                    ->orderBy('products.id')
                    ->limit(1)])
                ->having('active_products_count', '>', 0)
                ->orderBy('sort_order')
                ->limit($limit)
                ->get()
                ->map(fn (Category $c) => ['id' => $c->id, 'slug' => $c->slug, 'count' => $c->active_products_count, 'cover' => $c->cover_path])
                ->all();
        });

        $names = \App\Models\CategoryTranslation::query()
            ->whereIn('category_id', array_column($rows, 'id'))
            ->whereIn('locale', [$locale, $fallback])
            ->get()
            ->groupBy('category_id');

        return collect($rows)->map(fn (array $row) => (object) [
            'slug'  => $row['slug'],
            'count' => $row['count'],
            'cover' => media_url($row['cover'], asset('cozastore/images/banner-01.jpg')),
            'name'  => self::translated($names->get($row['id']), 'name', $locale, $fallback, $row['slug']),
        ]);
    }

    /**
     * Featured products first, then the newest, for the home page grid.
     */
    public function featured(int $limit = 8): Collection
    {
        return $this->listing(['sort' => 'featured'], [], $limit);
    }

    /**
     * @return array{color: ?Attribute, tag: ?Attribute}
     */
    public function filterAttributes(): array
    {
        $attributes = Attribute::query()
            ->select(['id', 'slug'])
            ->where('is_active', true)
            ->whereIn('slug', ['color', 'colors', 'tag', 'tags'])
            ->get()
            ->keyBy('slug');

        return [
            'color' => $attributes->get('color') ?? $attributes->get('colors'),
            'tag'   => $attributes->get('tag') ?? $attributes->get('tags'),
        ];
    }

    public function attributeValues(?int $attributeId): Collection
    {
        if (! $attributeId) {
            return collect();
        }

        return AttributeValue::query()
            ->select(['id', 'attribute_id', 'value', 'slug', 'sort_order'])
            ->where('attribute_id', $attributeId)
            ->where('is_active', true)
            ->whereExists(fn ($q) => self::onSaleValueQuery($q))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function listing(array $filters, array $filterAttributes, int $limit): Collection
    {
        [$locale, $fallback] = $this->locales();

        $query = Product::query()
            ->select(['id', 'category_id', 'slug', 'base_price', 'currency', 'is_featured', 'created_at'])
            ->where('status', 'active')
            ->with([
                'translations' => fn ($q) => $q
                    ->select(['id', 'product_id', 'locale', 'name'])
                    ->whereIn('locale', [$locale, $fallback]),
                'primaryImage' => fn ($q) => $q->select(['id', 'product_id', 'path', 'is_primary']),
                'category' => fn ($q) => $q
                    ->select(['id', 'slug'])
                    ->with(['translations' => fn ($t) => $t
                        ->select(['id', 'category_id', 'locale', 'name'])
                        ->whereIn('locale', [$locale, $fallback])]),
            ]);

        $this->applyFilters($query, $filters, $filterAttributes, $locale, $fallback);

        return $query->limit($limit)->get()->each(function (Product $product) use ($locale, $fallback) {
            $product->display_name = self::translated($product->translations, 'name', $locale, $fallback, $product->slug);
            $product->category_name = $product->category
                ? self::translated($product->category->translations, 'name', $locale, $fallback, $product->category->slug)
                : null;
        });
    }

    /**
     * Product ids in the user's wishlist, fetched once instead of per card.
     *
     * @return array<int, true>
     */
    public function wishlistedProductIds(?User $user): array
    {
        if (! $user) {
            return [];
        }

        return $user->wishlist()
            ->first()
            ?->items()
            ->whereNull('variant_id')
            ->pluck('product_id')
            ->mapWithKeys(fn ($id) => [(int) $id => true])
            ->all() ?? [];
    }

    private function applyFilters(Builder $query, array $filters, array $attributes, string $locale, string $fallback): void
    {
        $query
            ->when($filters['category'] ?? null, fn (Builder $q, $slug) => $q
                ->whereHas('category', fn (Builder $c) => $c->where('slug', $slug)))
            ->when(filled($filters['q'] ?? null), fn (Builder $q) => $q
                ->whereHas('translations', fn (Builder $t) => self::searchName(
                    $t->whereIn('locale', [$locale, $fallback]),
                    $filters['q'],
                )))
            ->when(isset($filters['min']), fn (Builder $q) => $q->where('base_price', '>=', (float) $filters['min']))
            ->when(isset($filters['max']), fn (Builder $q) => $q->where('base_price', '<=', (float) $filters['max']));

        foreach (['color', 'tag'] as $key) {
            $attribute = $attributes[$key] ?? null;

            if ($attribute && ! empty($filters[$key])) {
                $query->whereHas('variants.values', fn (Builder $v) => $v
                    ->where('attribute_id', $attribute->id)
                    ->where('attribute_value_id', (int) $filters[$key]));
            }
        }

        match ($filters['sort'] ?? 'new') {
            'featured'   => $query->orderByDesc('is_featured')->latest(),
            'price_asc'  => $query->orderBy('base_price')->latest(),
            'price_desc' => $query->orderByDesc('base_price')->latest(),
            default      => $query->latest(),
        };
    }

    /**
     * Restricts attribute values to those used by an active variant of an
     * active product, so filters never offer options that return nothing.
     */
    public static function onSaleValueQuery($query): void
    {
        $query->selectRaw('1')
            ->from('product_variant_values as pvv')
            ->join('product_variants as pv', 'pv.id', '=', 'pvv.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->whereColumn('pvv.attribute_value_id', 'attribute_values.id')
            ->where('pv.is_active', true)
            ->where('p.status', 'active')
            ->whereNull('p.deleted_at');
    }

    /**
     * Match product names using the FULLTEXT index on MySQL (fast at any
     * catalogue size) and fall back to LIKE for short terms or other drivers.
     */
    public static function searchName(Builder $translations, string $term): Builder
    {
        $term = trim($term);
        $words = array_filter(preg_split('/\s+/u', preg_replace('/[+\-<>()~*"@]+/u', ' ', $term)));
        $longEnough = $words !== [] && min(array_map('mb_strlen', $words)) >= 3;

        if ($longEnough && $translations->getConnection()->getDriverName() === 'mysql') {
            $query = implode(' ', array_map(fn ($w) => '+' . $w . '*', $words));

            return $translations->whereFullText('name', $query, ['mode' => 'boolean']);
        }

        return $translations->where('name', 'like', '%' . addcslashes($term, '%_\\') . '%');
    }

    public static function translated($translations, string $field, string $locale, string $fallback, ?string $default = null): ?string
    {
        if (! $translations || $translations->isEmpty()) {
            return $default;
        }

        foreach ([$locale, $fallback] as $candidate) {
            $value = $translations->firstWhere('locale', $candidate)?->{$field};

            if (filled($value)) {
                return $value;
            }
        }

        return $translations->first()->{$field} ?? $default;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function locales(): array
    {
        return [app()->getLocale(), (string) config('app.fallback_locale')];
    }
}
