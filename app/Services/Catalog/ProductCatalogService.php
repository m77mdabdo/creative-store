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
            ->orderBy('sort_order')
            ->get();
    }

    public function listing(array $filters, array $filterAttributes, int $limit): Collection
    {
        [$locale, $fallback] = $this->locales();

        $query = Product::query()
            ->select(['id', 'category_id', 'slug', 'base_price', 'currency', 'created_at'])
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
            'price_asc'  => $query->orderBy('base_price')->latest(),
            'price_desc' => $query->orderByDesc('base_price')->latest(),
            default      => $query->latest(),
        };
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
