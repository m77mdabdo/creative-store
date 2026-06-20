<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Services\Catalog\ProductCatalogService;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private const REVIEWS_ON_PAGE = 20;

    public function index(Request $request)
    {
        $request->validate([
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

        $categories = Category::query()
            ->select(['id', 'parent_id', 'slug', 'sort_order'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with(['translations' => function ($q) use ($locale, $fallback) {
                $q->select(['id', 'category_id', 'locale', 'name'])
                    ->whereIn('locale', [$locale, $fallback]);
            }])
            ->orderBy('sort_order')
            ->get();

        $attrs = Attribute::query()
            ->select(['id', 'slug'])
            ->where('is_active', true)
            ->whereIn('slug', ['color', 'colors', 'tag', 'tags'])
            ->get()
            ->keyBy('slug');

        $colorAttr = $attrs->get('color') ?? $attrs->get('colors');
        $tagAttr   = $attrs->get('tag') ?? $attrs->get('tags');

        $colorValues = $colorAttr
            ? AttributeValue::query()
                ->select(['id', 'attribute_id', 'value', 'slug', 'sort_order'])
                ->where('attribute_id', $colorAttr->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
            : collect();

        $tagValues = $tagAttr
            ? AttributeValue::query()
                ->select(['id', 'attribute_id', 'value', 'slug', 'sort_order'])
                ->where('attribute_id', $tagAttr->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
            : collect();

        $productsQuery = Product::query()
            ->select(['id', 'category_id', 'slug', 'base_price', 'currency', 'created_at'])
            ->where('status', 'active')
            ->with([
                'translations' => function ($q) use ($locale, $fallback) {
                    $q->select(['id', 'product_id', 'locale', 'name'])
                        ->whereIn('locale', [$locale, $fallback]);
                },
                'primaryImage' => function ($q) {
                    $q->select(['id', 'product_id', 'path', 'is_primary'])
                        ->where('is_primary', true);
                },
                'category' => function ($q) use ($locale, $fallback) {
                    $q->select(['id', 'slug'])
                        ->with(['translations' => function ($t) use ($locale, $fallback) {
                            $t->select(['id', 'category_id', 'locale', 'name'])
                                ->whereIn('locale', [$locale, $fallback]);
                        }]);
                },
            ]);

        $productsQuery->when($request->filled('category'), function ($q) use ($request) {
            $catSlug = $request->string('category')->toString();

            $q->whereHas('category', function ($c) use ($catSlug) {
                $c->where('slug', $catSlug);
            });
        });

        $productsQuery->when($request->filled('q'), function ($q) use ($request, $locale, $fallback) {
            $term = trim($request->string('q')->toString());

            $q->whereHas('translations', fn ($t) => ProductCatalogService::searchName(
                $t->whereIn('locale', [$locale, $fallback]),
                $term,
            ));
        });

        $productsQuery->when(
            $request->filled('min'),
            fn($q) => $q->where('base_price', '>=', (float) $request->input('min'))
        );

        $productsQuery->when(
            $request->filled('max'),
            fn($q) => $q->where('base_price', '<=', (float) $request->input('max'))
        );

        if ($colorAttr && $request->filled('color')) {
            $colorValueId = (int) $request->input('color');

            $productsQuery->whereHas('variants.values', function ($v) use ($colorAttr, $colorValueId) {
                $v->where('attribute_id', $colorAttr->id)
                    ->where('attribute_value_id', $colorValueId);
            });
        }

        if ($tagAttr && $request->filled('tag')) {
            $tagValueId = (int) $request->input('tag');

            $productsQuery->whereHas('variants.values', function ($v) use ($tagAttr, $tagValueId) {
                $v->where('attribute_id', $tagAttr->id)
                    ->where('attribute_value_id', $tagValueId);
            });
        }

        $sort = $request->input('sort', 'new');

        if ($sort === 'price_asc') {
            $productsQuery->orderBy('base_price', 'asc')
                ->orderBy('created_at', 'desc');
        } elseif ($sort === 'price_desc') {
            $productsQuery->orderBy('base_price', 'desc')
                ->orderBy('created_at', 'desc');
        } else {
            $productsQuery->orderBy('created_at', 'desc');
        }

        $products = $productsQuery->paginate(10)->withQueryString();

        $wishlistProductIds = [];
        if (auth()->check()) {
            $wishlistProductIds = WishlistItem::query()
                ->whereHas('wishlist', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                ->whereNull('variant_id')
                ->pluck('product_id')
                ->map(fn($id) => (int) $id)
                ->all();
        }

        $priceRanges = [
            ['label' => 'All', 'min' => null, 'max' => null],
            ['label' => '$0.00 - $50.00', 'min' => 0, 'max' => 50],
            ['label' => '$50.00 - $100.00', 'min' => 50, 'max' => 100],
            ['label' => '$100.00 - $150.00', 'min' => 100, 'max' => 150],
            ['label' => '$150.00 - $200.00', 'min' => 150, 'max' => 200],
            ['label' => '$200.00+', 'min' => 200, 'max' => null],
        ];

        return view('site.product.product', compact(
            'categories',
            'products',
            'colorValues',
            'tagValues',
            'priceRanges',
            'wishlistProductIds'
        ));
    }

    public function show(string $slug)
    {
        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale');

        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->with([
                'translations' => function ($q) use ($locale, $fallback) {
                    $q->select(['id', 'product_id', 'locale', 'name', 'description'])
                        ->whereIn('locale', [$locale, $fallback]);
                },
                'images' => function ($q) {
                    $q->select([
                        'id',
                        'product_id',
                        'variant_id',
                        'path',
                        'alt_text',
                        'is_primary',
                        'sort_order',
                    ])
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
                'primaryImage:id,product_id,path,is_primary',
                'category:id,slug',
                'category.translations' => function ($q) use ($locale, $fallback) {
                    $q->select(['id', 'category_id', 'locale', 'name'])
                        ->whereIn('locale', [$locale, $fallback]);
                },
                'variants' => function ($q) {
                    $q->select([
                        'id',
                        'product_id',
                        'sku',
                        'price',
                        'stock',
                        'is_active',
                        'combination_key',
                    ])
                        ->where('is_active', true)
                        ->orderByDesc('id');
                },
                'variants.values' => function ($q) {
                    $q->select([
                        'id',
                        'product_variant_id',
                        'attribute_id',
                        'attribute_value_id',
                    ]);
                },
                'variants.values.attribute:id,slug,name,is_active',
                'variants.values.value:id,attribute_id,value,slug,sort_order,is_active',
                'variants.images' => function ($q) {
                    $q->select([
                        'id',
                        'product_id',
                        'variant_id',
                        'path',
                        'alt_text',
                        'is_primary',
                        'sort_order',
                    ])
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
                'coupons' => function ($q) {
                    $q->where('is_active', true)
                        ->where(function ($query) {
                            $query->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', now());
                        })
                        ->where(function ($query) {
                            $query->whereNull('expires_at')
                                ->orWhere('expires_at', '>=', now());
                        });
                },
            ])
            ->withCount(['reviews as approved_reviews_count' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews as approved_reviews_avg' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->firstOrFail();

        // Show the latest reviews only; totals come from the aggregate above,
        // so a product with thousands of reviews stays a single small query.
        $latestReviews = $product->reviews()
            ->where('is_approved', true)
            ->with('user:id,name')
            ->latest('id')
            ->limit(self::REVIEWS_ON_PAGE)
            ->get();

        $fallbackMain    = asset('cozastore/images/product-detail-01.jpg');
        $fallbackThumb   = asset('cozastore/images/product-detail-02.jpg');
        $fallbackRelated = asset('cozastore/images/product-01.jpg');

        $productName        = $product->name ?? $product->slug;
        $productDescription = $product->description ?? '';
        $productPrice       = (float) ($product->variants->min('price') ?? $product->base_price ?? 0);
        $productCurrency    = $product->currency ?? 'SAR';
        $productSku         = $product->variants->first()?->sku ?? $product->sku ?? 'N/A';
        $categoryName       = $product->category?->name ?? $product->category?->slug;
        $categorySlug       = $product->category?->slug;

        $generalGallery = $this->buildCleanImagePayload(
            $product->images->whereNull('variant_id')->values(),
            $productName,
            $fallbackMain,
            $fallbackThumb
        );

        if (empty($generalGallery) && $product->primaryImage?->path) {
            $primaryUrl = $this->resolvePublicImageUrl($product->primaryImage->path, $fallbackMain);

            $generalGallery = [[
                'id'         => $product->primaryImage->id ?? null,
                'path'       => $product->primaryImage->path,
                'url'        => $primaryUrl,
                'thumb'      => $primaryUrl,
                'alt'        => $productName,
                'is_primary' => true,
                'sort_order' => 0,
            ]];
        }

        if (empty($generalGallery)) {
            $generalGallery = [[
                'id'         => null,
                'path'       => null,
                'url'        => $fallbackMain,
                'thumb'      => $fallbackThumb,
                'alt'        => $productName,
                'is_primary' => true,
                'sort_order' => 0,
            ]];
        }

        $optionAttributes = [];
        $specifications   = [];
        $variantMatrix    = [];

        foreach ($product->variants as $variant) {
            $variantImages = $this->buildCleanImagePayload(
                $variant->images,
                $productName,
                $fallbackMain,
                $fallbackThumb
            );

            if (empty($variantImages)) {
                $variantImages = $generalGallery;
            }

            $selectedAttributes = [];

            foreach ($variant->values as $row) {
                $attribute = $row->attribute;
                $value     = $row->value;

                if (!$attribute || !$value || !$value->is_active) {
                    continue;
                }

                $attrSlug = Str::slug($attribute->slug ?: $attribute->name);
                $attrName = $attribute->name ?: ucfirst(str_replace('-', ' ', $attrSlug));

                $optionAttributes[$attrSlug] ??= [
                    'name'   => $attrName,
                    'slug'   => $attrSlug,
                    'values' => [],
                ];

                $optionAttributes[$attrSlug]['values'][$value->id] = [
                    'id'         => (int) $value->id,
                    'value'      => $value->value,
                    'slug'       => $value->slug,
                    'sort_order' => (int) ($value->sort_order ?? 0),
                ];

                $selectedAttributes[$attrSlug] = (int) $value->id;

                $specifications[$attrSlug] ??= [
                    'label'  => $attrName,
                    'values' => [],
                ];

                $specifications[$attrSlug]['values'][$value->id] = $value->value;
            }

            ksort($selectedAttributes);

            $variantKey = collect($selectedAttributes)
                ->map(fn($valueId, $slug) => $slug . ':' . $valueId)
                ->implode('|');

            $variantMatrix[$variantKey] = [
                'id'         => (int) $variant->id,
                'sku'        => $variant->sku,
                'price'      => (float) $variant->price,
                'stock'      => (int) $variant->stock,
                'images'     => $variantImages,
                'attributes' => $selectedAttributes,
            ];
        }

        $optionAttributes = collect($optionAttributes)
            ->map(function ($attribute) {
                $attribute['values'] = collect($attribute['values'])
                    ->sortBy([
                        ['sort_order', 'asc'],
                        ['id', 'asc'],
                    ])
                    ->values()
                    ->all();

                return $attribute;
            })
            ->values()
            ->all();

        $specifications = collect($specifications)
            ->map(function ($spec) {
                $spec['value'] = implode(' / ', collect($spec['values'])->values()->unique()->all());
                unset($spec['values']);
                return $spec;
            })
            ->values()
            ->all();

        $related = Product::query()
            ->select(['id', 'category_id', 'slug', 'base_price', 'currency', 'status', 'created_at'])
            ->where('status', 'active')
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn($q) => $q->where('category_id', $product->category_id))
            ->with([
                'translations' => function ($q) use ($locale, $fallback) {
                    $q->select(['id', 'product_id', 'locale', 'name'])
                        ->whereIn('locale', [$locale, $fallback]);
                },
                'primaryImage:id,product_id,path,is_primary',
            ])
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($rp) use ($fallbackRelated) {
                return [
                    'slug'     => $rp->slug,
                    'name'     => $rp->name ?? $rp->slug,
                    'price'    => number_format((float) ($rp->base_price ?? 0), 2),
                    'currency' => $rp->currency ?? 'SAR',
                    'image'    => $this->resolvePublicImageUrl($rp->primaryImage?->path, $fallbackRelated),
                ];
            })
            ->values()
            ->all();

        $productCoupons = ($product->coupons ?? collect())
            ->map(function ($coupon) use ($productCurrency) {
                return [
                    'code'     => $coupon->code,
                    'type'     => $coupon->type,
                    'value'    => $coupon->value,
                    'currency' => $productCurrency,
                    'label'    => $coupon->type === 'percent'
                        ? 'احصل على ' . rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') . '% خصم على هذا المنتج'
                        : 'احصل على ' . number_format($coupon->value, 2) . ' ' . $productCurrency . ' خصم على هذا المنتج',
                ];
            })
            ->values()
            ->all();

        $reviews = $latestReviews
            ->map(function ($review) {
                return [
                    'user_name' => $review->user->name ?? 'مستخدم',
                    'rating'    => (int) $review->rating,
                    'title'     => $review->title,
                    'comment'   => $review->comment,
                ];
            })
            ->values()
            ->all();

        $reviewsCount  = (int) $product->approved_reviews_count;
        $averageRating = $reviewsCount > 0 ? round((float) $product->approved_reviews_avg, 1) : 0;

        $wishlistExists = false;
        if (auth()->check()) {
            $wishlistExists = WishlistItem::whereHas('wishlist', function ($query) {
                $query->where('user_id', auth()->id());
            })
                ->where('product_id', $product->id)
                ->whereNull('variant_id')
                ->exists();
        }

        return view('site.product.product-detail', [
            'product'            => $product,
            'productName'        => $productName,
            'productDescription' => $productDescription,
            'productPrice'       => $productPrice,
            'productCurrency'    => $productCurrency,
            'productSku'         => $productSku,
            'categoryName'       => $categoryName,
            'categorySlug'       => $categorySlug,
            'generalGallery'     => $generalGallery,
            'optionAttributes'   => $optionAttributes,
            'variantMatrix'      => $variantMatrix,
            'specifications'     => $specifications,
            'related'            => $related,
            'productCoupons'     => $productCoupons,
            'reviews'            => $reviews,
            'reviewsCount'       => $reviewsCount,
            'averageRating'      => $averageRating,
            'wishlistExists'     => $wishlistExists,
        ]);
    }

    private function buildCleanImagePayload($images, string $productName, string $fallbackMain, string $fallbackThumb): array
    {
        if (!$images || count($images) === 0) {
            return [];
        }

        $unique = [];
        $seen   = [];

        foreach ($images as $img) {
            $path = trim((string) ($img->path ?? ''));

            if ($path === '') {
                continue;
            }

            $url = $this->resolvePublicImageUrl($path, $fallbackMain);

            if (isset($seen[$url])) {
                continue;
            }

            $seen[$url] = true;

            $unique[] = [
                'id'         => $img->id ?? null,
                'path'       => $path,
                'url'        => $url,
                'thumb'      => $url,
                'alt'        => $img->alt_text ?: $productName,
                'is_primary' => (bool) ($img->is_primary ?? false),
                'sort_order' => (int) ($img->sort_order ?? 0),
            ];
        }

        if (empty($unique)) {
            return [];
        }

        return $this->uniqueAndSortImages($unique);
    }

    private function uniqueAndSortImages(array $images): array
    {
        $seen   = [];
        $unique = [];

        foreach ($images as $img) {
            $key = trim((string) ($img['path'] ?? $img['url'] ?? ''));

            if ($key === '') {
                continue;
            }

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique[]   = $img;
        }

        usort($unique, function ($a, $b) {
            return [
                !empty($a['is_primary']) ? 0 : 1,
                (int) ($a['sort_order'] ?? 0),
                (int) ($a['id'] ?? 0),
            ] <=> [
                !empty($b['is_primary']) ? 0 : 1,
                (int) ($b['sort_order'] ?? 0),
                (int) ($b['id'] ?? 0),
            ];
        });

        return array_values($unique);
    }

    protected function resolvePublicImageUrl(?string $path, string $fallback): string
    {
        if (!$path) {
            return $fallback;
        }

        $path = trim($path);

        if ($path === '') {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return media_url($path);
    }

    protected function extractVariantOptionData($variant): array
    {
        $size      = null;
        $color     = null;
        $sizeId    = null;
        $colorId   = null;
        $colorName = null;
        $colorSlug = null;

        foreach ($variant->values as $row) {
            $attribute = $row->attribute;
            $value     = $row->value;

            if (!$attribute || !$value || !$value->is_active) {
                continue;
            }

            $attrSlug = strtolower(trim((string) $attribute->slug));
            $attrName = strtolower(trim((string) $attribute->name));

            if ($this->isSizeAttribute($attrSlug, $attrName)) {
                $size = [
                    'id'         => (int) $value->id,
                    'value'      => $value->value,
                    'slug'       => $value->slug,
                    'sort_order' => (int) ($value->sort_order ?? 0),
                ];

                $sizeId = (int) $row->attribute_value_id;
            }

            if ($this->isColorAttribute($attrSlug, $attrName, $attribute->name)) {
                $color = [
                    'id'         => (int) $value->id,
                    'value'      => $value->value,
                    'slug'       => $value->slug,
                    'sort_order' => (int) ($value->sort_order ?? 0),
                ];

                $colorId   = (int) $row->attribute_value_id;
                $colorName = $value->value;
                $colorSlug = $value->slug;
            }
        }

        return [
            'size'       => $size,
            'color'      => $color,
            'size_id'    => $sizeId,
            'color_id'   => $colorId,
            'color_name' => $colorName,
            'color_slug' => $colorSlug,
        ];
    }

    protected function isSizeAttribute(string $slug, string $name): bool
    {
        return in_array($slug, ['size', 'sizes'], true)
            || in_array($name, ['size', 'sizes', 'المقاس', 'الحجم'], true);
    }

    protected function isColorAttribute(string $slug, string $name, ?string $originalName = null): bool
    {
        return in_array($slug, ['color', 'colors'], true)
            || in_array($name, ['color', 'colors', 'اللون', 'الألوان'], true)
            || in_array(trim((string) $originalName), ['اللون', 'الألوان'], true);
    }
}
