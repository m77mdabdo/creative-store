<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'category' => ['nullable', 'string', 'max:120'],
            'q'        => ['nullable', 'string', 'max:200'],
            'min'      => ['nullable', 'numeric', 'min:0'],
            'max'      => ['nullable', 'numeric', 'min:0'],
            'color'    => ['nullable', 'integer', 'min:1'],
            'tag'      => ['nullable', 'integer', 'min:1'],
            'sort'     => ['nullable', 'in:new,price_asc,price_desc'],
        ]);

        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale');

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        $categories = Category::query()
            ->select(['id', 'parent_id', 'slug', 'sort_order'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with([
                'translations' => function ($query) use ($locale, $fallback) {
                    $query->select(['id', 'category_id', 'locale', 'name'])
                        ->whereIn('locale', [$locale, $fallback]);
                }
            ])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($category) use ($locale, $fallback) {
                $category->display_name = $this->translatedValue(
                    $category->translations,
                    'name',
                    $locale,
                    $fallback,
                    $category->slug
                );

                return $category;
            });

        /*
        |--------------------------------------------------------------------------
        | Attributes (Color / Tags)
        |--------------------------------------------------------------------------
        */
        $attributes = Attribute::query()
            ->select(['id', 'slug'])
            ->where('is_active', true)
            ->whereIn('slug', ['color', 'colors', 'tag', 'tags'])
            ->get()
            ->keyBy('slug');

        $colorAttr = $attributes->get('color') ?? $attributes->get('colors');
        $tagAttr   = $attributes->get('tag') ?? $attributes->get('tags');

        $colorValues = $this->attributeValues($colorAttr?->id);
        $tagValues   = $this->attributeValues($tagAttr?->id);

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */
        $productsQuery = Product::query()
            ->select([
                'id',
                'category_id',
                'slug',
                'base_price',
                'currency',
                'created_at',
            ])
            ->where('status', 'active')
            ->with([
                'translations' => function ($query) use ($locale, $fallback) {
                    $query->select(['id', 'product_id', 'locale', 'name'])
                        ->whereIn('locale', [$locale, $fallback]);
                },
                'primaryImage' => function ($query) {
                    $query->select(['id', 'product_id', 'path', 'is_primary'])
                        ->where('is_primary', true);
                },
                'category' => function ($query) use ($locale, $fallback) {
                    $query->select(['id', 'slug'])
                        ->with([
                            'translations' => function ($translationQuery) use ($locale, $fallback) {
                                $translationQuery->select(['id', 'category_id', 'locale', 'name'])
                                    ->whereIn('locale', [$locale, $fallback]);
                            }
                        ]);
                },
            ]);

        $this->applyProductFilters(
            query: $productsQuery,
            filters: $filters,
            locale: $locale,
            fallback: $fallback,
            colorAttributeId: $colorAttr?->id,
            tagAttributeId: $tagAttr?->id,
        );

        // get بدل paginate علشان ما يظهرش ترقيم جانبي
        $products = $productsQuery->get();

        $products = $products->map(function ($product) use ($locale, $fallback) {
            $product->display_name = $this->translatedValue(
                $product->translations,
                'name',
                $locale,
                $fallback,
                $product->slug
            );

            $product->category_name = $product->category
                ? $this->translatedValue(
                    $product->category->translations,
                    'name',
                    $locale,
                    $fallback,
                    $product->category->slug
                )
                : null;

            $product->image_url = $product->primaryImage?->path
                ? asset('storage/' . ltrim($product->primaryImage->path, '/'))
                : asset('cozastore/images/product-01.jpg');

            return $product;
        });

        /*
        |--------------------------------------------------------------------------
        | Price Ranges
        |--------------------------------------------------------------------------
        */
        $priceRanges = [
            ['label' => 'All', 'min' => null, 'max' => null],
            ['label' => '$0.00 - $50.00', 'min' => 0, 'max' => 50],
            ['label' => '$50.00 - $100.00', 'min' => 50, 'max' => 100],
            ['label' => '$100.00 - $150.00', 'min' => 100, 'max' => 150],
            ['label' => '$150.00 - $200.00', 'min' => 150, 'max' => 200],
            ['label' => '$200.00+', 'min' => 200, 'max' => null],
        ];

        /*
        |--------------------------------------------------------------------------
        | Blogs
        |--------------------------------------------------------------------------
        */
        $blogsQuery = Blog::query()->with('category');

        if (Schema::hasColumn('blogs', 'status')) {
            $blogsQuery->whereIn('status', ['published', 'active', 1]);
        }

        if (Schema::hasColumn('blogs', 'is_active')) {
            $blogsQuery->where('is_active', 1);
        }

        if (Schema::hasColumn('blogs', 'published_at')) {
            $blogsQuery->orderByDesc('published_at');
        } else {
            $blogsQuery->latest();
        }

        $blogs = $blogsQuery->take(6)->get();

        return view('site.home', compact(
            'categories',
            'products',
            'colorValues',
            'tagValues',
            'priceRanges',
            'blogs'
        ));
    }

    protected function applyProductFilters(
        $query,
        array $filters,
        string $locale,
        string $fallback,
        ?int $colorAttributeId,
        ?int $tagAttributeId
    ): void {
        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($categoryQuery) use ($filters) {
                $categoryQuery->where('slug', $filters['category']);
            });
        }

        if (!empty($filters['q'])) {
            $term = trim($filters['q']);

            $query->whereHas('translations', function ($translationQuery) use ($term, $locale, $fallback) {
                $translationQuery->whereIn('locale', [$locale, $fallback])
                    ->where('name', 'like', '%' . $term . '%');
            });
        }

        if (isset($filters['min']) && $filters['min'] !== null && $filters['min'] !== '') {
            $query->where('base_price', '>=', (float) $filters['min']);
        }

        if (isset($filters['max']) && $filters['max'] !== null && $filters['max'] !== '') {
            $query->where('base_price', '<=', (float) $filters['max']);
        }

        if ($colorAttributeId && !empty($filters['color'])) {
            $query->whereHas('variants.values', function ($valueQuery) use ($colorAttributeId, $filters) {
                $valueQuery->where('attribute_id', $colorAttributeId)
                    ->where('attribute_value_id', (int) $filters['color']);
            });
        }

        if ($tagAttributeId && !empty($filters['tag'])) {
            $query->whereHas('variants.values', function ($valueQuery) use ($tagAttributeId, $filters) {
                $valueQuery->where('attribute_id', $tagAttributeId)
                    ->where('attribute_value_id', (int) $filters['tag']);
            });
        }

        $sort = $filters['sort'] ?? 'new';

        match ($sort) {
            'price_asc'  => $query->orderBy('base_price', 'asc')->orderBy('created_at', 'desc'),
            'price_desc' => $query->orderBy('base_price', 'desc')->orderBy('created_at', 'desc'),
            default      => $query->orderBy('created_at', 'desc'),
        };
    }

    protected function attributeValues(?int $attributeId)
    {
        if (!$attributeId) {
            return collect();
        }

        return AttributeValue::query()
            ->select(['id', 'attribute_id', 'value', 'slug', 'sort_order'])
            ->where('attribute_id', $attributeId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    protected function translatedValue($translations, string $field, string $locale, string $fallback, ?string $default = null): ?string
    {
        if (!$translations || $translations->isEmpty()) {
            return $default;
        }

        $current = $translations->firstWhere('locale', $locale);
        if ($current && filled($current->{$field})) {
            return $current->{$field};
        }

        $fallbackTranslation = $translations->firstWhere('locale', $fallback);
        if ($fallbackTranslation && filled($fallbackTranslation->{$field})) {
            return $fallbackTranslation->{$field};
        }

        $first = $translations->first();
        return $first->{$field} ?? $default;
    }
}
