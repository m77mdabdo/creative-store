<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;

class ProductController extends Controller
{
    //

     public function index(Request $request)
    {
        // ✅ Validation للـ query params (بيمنع مدخلات غلط + يحمي من بطء)
        $request->validate([
            'category' => ['nullable','string','max:120'],
            'q'        => ['nullable','string','max:200'],
            'min'      => ['nullable','numeric','min:0'],
            'max'      => ['nullable','numeric','min:0'],
            'color'    => ['nullable','integer','min:1'],
            'tag'      => ['nullable','integer','min:1'],
            'sort'     => ['nullable','in:new,price_asc,price_desc'],
        ]);

        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale');

        // ===================== Categories (Tabs) =====================
        $categories = Category::query()
            ->select(['id','parent_id','slug','sort_order'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with(['translations' => function ($q) use ($locale, $fallback) {
                $q->select(['id','category_id','locale','name'])
                  ->whereIn('locale', [$locale, $fallback]);
            }])
            ->orderBy('sort_order')
            ->get();

        // ===================== Attributes (Color/Tags) =====================
        // ملاحظة: لو slug عندك ثابت "color" و "tag" يبقى تمام
        $attrs = Attribute::query()
            ->select(['id','slug'])
            ->where('is_active', true)
            ->whereIn('slug', ['color','colors','tag','tags'])
            ->get()
            ->keyBy('slug');

        $colorAttr = $attrs->get('color') ?? $attrs->get('colors');
        $tagAttr   = $attrs->get('tag')   ?? $attrs->get('tags');

        $colorValues = $colorAttr
            ? AttributeValue::query()
                ->select(['id','attribute_id','value','slug','sort_order'])
                ->where('attribute_id', $colorAttr->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
            : collect();

        $tagValues = $tagAttr
            ? AttributeValue::query()
                ->select(['id','attribute_id','value','slug','sort_order'])
                ->where('attribute_id', $tagAttr->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
            : collect();

        // ===================== Products Query + Eager Loading =====================
        $productsQuery = Product::query()
            ->select(['id','category_id','slug','base_price','currency','created_at'])
            ->where('status', 'active')
            ->with([
                'translations' => function ($q) use ($locale, $fallback) {
                    $q->select(['id','product_id','locale','name'])
                      ->whereIn('locale', [$locale, $fallback]);
                },
                'primaryImage' => function ($q) {
                    $q->select(['id','product_id','path','is_primary'])
                      ->where('is_primary', true);
                },
                'category' => function ($q) use ($locale, $fallback) {
                    $q->select(['id','slug'])
                      ->with(['translations' => function ($t) use ($locale, $fallback) {
                          $t->select(['id','category_id','locale','name'])
                            ->whereIn('locale', [$locale, $fallback]);
                      }]);
                },
            ]);

        // 1) Category filter
        $productsQuery->when($request->filled('category'), function ($q) use ($request) {
            $catSlug = $request->string('category')->toString();
            $q->whereHas('category', fn($c) => $c->where('slug', $catSlug));
        });

        // 2) Search by product name (translations)
        $productsQuery->when($request->filled('q'), function ($q) use ($request, $locale, $fallback) {
            $term = trim($request->string('q')->toString());
            $q->whereHas('translations', function ($t) use ($term, $locale, $fallback) {
                $t->whereIn('locale', [$locale, $fallback])
                  ->where('name', 'like', "%{$term}%");
            });
        });

        // 3) Price range
        $productsQuery->when($request->filled('min'), fn($q) => $q->where('base_price', '>=', (float)$request->input('min')));
        $productsQuery->when($request->filled('max'), fn($q) => $q->where('base_price', '<=', (float)$request->input('max')));

        // 4) Color filter عبر variants.values
        if ($colorAttr && $request->filled('color')) {
            $colorValueId = (int) $request->input('color');

            $productsQuery->whereHas('variants.values', function ($v) use ($colorAttr, $colorValueId) {
                $v->where('attribute_id', $colorAttr->id)
                  ->where('attribute_value_id', $colorValueId);
            });
        }

        // 5) Tag filter عبر variants.values
        if ($tagAttr && $request->filled('tag')) {
            $tagValueId = (int) $request->input('tag');

            $productsQuery->whereHas('variants.values', function ($v) use ($tagAttr, $tagValueId) {
                $v->where('attribute_id', $tagAttr->id)
                  ->where('attribute_value_id', $tagValueId);
            });
        }

        // 6) Sort (مع ترتيب ثابت)
        $sort = $request->input('sort', 'new');

        if ($sort === 'price_asc') {
            $productsQuery->orderBy('base_price', 'asc')
                          ->orderBy('created_at', 'desc'); // ترتيب ثابت للصفحات
        } elseif ($sort === 'price_desc') {
            $productsQuery->orderBy('base_price', 'desc')
                          ->orderBy('created_at', 'desc');
        } else {
            $productsQuery->orderBy('created_at', 'desc');
        }

        // Pagination
        $products = $productsQuery->paginate(10)->withQueryString();

        // Ranges للـ UI
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
            'priceRanges'
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
                    $q->select(['id','product_id','locale','name','description'])
                        ->whereIn('locale', [$locale, $fallback]);
                },
                'images:id,product_id,variant_id,path,alt_text,is_primary,sort_order',
                'primaryImage:id,product_id,path,is_primary',
                'category:id,slug',
                'category.translations' => function ($q) use ($locale, $fallback) {
                    $q->select(['id','category_id','locale','name'])
                        ->whereIn('locale', [$locale, $fallback]);
                },
                'variants' => function ($q) {
                    $q->select(['id','product_id','sku','price','stock','is_active'])
                        ->where('is_active', true);
                },
                'variants.values' => function ($q) {
                    $q->select(['id','product_variant_id','attribute_id','attribute_value_id']);
                },
                'variants.values.attribute:id,slug,name,is_active',
                'variants.values.value:id,attribute_id,value,slug,sort_order,is_active',
            ])
            ->firstOrFail();

        // =========================
        // Gallery Images (primary first)
        // =========================
        $galleryImages = $product->images
            ->sortBy(function ($img) {
                return [(int)!$img->is_primary, (int)$img->sort_order, (int)$img->id];
            })
            ->values();

        // =========================
        // Extract Size / Color values from variants
        // =========================
        $sizes  = collect();
        $colors = collect();

        foreach ($product->variants as $variant) {
            foreach ($variant->values as $vv) {
                $attrSlug = strtolower($vv->attribute?->slug ?? '');

                if (in_array($attrSlug, ['size', 'sizes'], true)) {
                    if ($vv->value) $sizes->push($vv->value);
                }

                if (in_array($attrSlug, ['color', 'colors'], true)) {
                    if ($vv->value) $colors->push($vv->value);
                }
            }
        }

        $sizes  = $sizes->filter()->unique('id')->sortBy('sort_order')->values();
        $colors = $colors->filter()->unique('id')->sortBy('sort_order')->values();

        // =========================
        // Build Variant Map
        // key => "sizeId-colorId" => variant_id
        // =========================
        $variantMap = [];

        foreach ($product->variants as $variant) {

            $sizeId  = null;
            $colorId = null;

            foreach ($variant->values as $vv) {

                $attrSlug = strtolower($vv->attribute?->slug ?? '');

                if (in_array($attrSlug, ['size', 'sizes'], true)) {
                    $sizeId = (int) $vv->attribute_value_id;
                }

                if (in_array($attrSlug, ['color', 'colors'], true)) {
                    $colorId = (int) $vv->attribute_value_id;
                }
            }

            // لو المنتج فيه Size + Color
            if (!empty($sizeId) && !empty($colorId)) {
                $variantMap["{$sizeId}-{$colorId}"] = $variant->id;
            }
            // لو المنتج فيه Size بس
            elseif (!empty($sizeId) && empty($colorId)) {
                $variantMap["{$sizeId}"] = $variant->id;
            }
            // لو المنتج فيه Color بس
            elseif (!empty($colorId) && empty($sizeId)) {
                $variantMap["{$colorId}"] = $variant->id;
            }
        }

        // =========================
        // Related Products (same category)
        // =========================
        $related = Product::query()
            ->select(['id','category_id','slug','base_price','currency','status','created_at'])
            ->where('status', 'active')
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with([
                'translations' => function ($q) use ($locale, $fallback) {
                    $q->select(['id','product_id','locale','name'])
                        ->whereIn('locale', [$locale, $fallback]);
                },
                'primaryImage:id,product_id,path,is_primary',
            ])
            ->latest()
            ->take(8)
            ->get();

        return view('site.product.product-detail', compact(
            'product',
            'galleryImages',
            'sizes',
            'colors',
            'variantMap',
            'related'
        ));
    }

}
