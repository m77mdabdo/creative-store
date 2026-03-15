<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Product\StoreProductImageRequest;
use App\Http\Requests\Dashboard\Product\StoreProductRequest;
use App\Http\Requests\Dashboard\Product\StoreProductVariantRequest;
use App\Http\Requests\Dashboard\Product\StoreVariantImageRequest;
use App\Http\Requests\Dashboard\Product\UpdateProductRequest;
use App\Http\Requests\Dashboard\Product\UpdateProductVariantRequest;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'category_id' => (string) $request->input('category_id', ''),
            'status' => (string) $request->input('status', ''),
            'featured' => (string) $request->input('featured', ''),
            'has_variants' => (string) $request->input('has_variants', ''),
        ];

        $query = Product::query()
            ->with(['category.translations', 'translations', 'primaryImage'])
            ->withCount(['variants', 'images', 'allReviews']);

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('translations', function ($t) use ($search) {
                        $t->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
            });
        }

        if ($filters['category_id'] !== '' && is_numeric($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['featured'] === 'yes') {
            $query->where('is_featured', true);
        } elseif ($filters['featured'] === 'no') {
            $query->where('is_featured', false);
        }

        if ($filters['has_variants'] === 'yes') {
            $query->where('has_variants', true);
        } elseif ($filters['has_variants'] === 'no') {
            $query->where('has_variants', false);
        }

        $products = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.products.index', [
            'products' => $products,
            'filters' => $filters,
            'categories' => $this->categoriesList(),
            'canCreate' => $this->canCurrentUser('products.create'),
            'canEdit' => $this->canCurrentUser('products.edit'),
            'canDelete' => $this->canCurrentUser('products.delete'),
            'canViewTrash' => $this->canCurrentUser('products.view'),
        ]);
    }

    public function trash(): View
    {
        $products = Product::onlyTrashed()
            ->with(['category.translations', 'translations', 'primaryImage'])
            ->withCount(['variants', 'images', 'allReviews'])
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('dashboard.products.trash', [
            'products' => $products,
            'canEdit' => $this->canCurrentUser('products.edit'),
            'canDelete' => $this->canCurrentUser('products.delete'),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.products.create', [
            'categories' => $this->categoriesList(),
            'form' => $this->productFormData(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $payload = $this->normalizedProductPayload($request);

        DB::transaction(function () use ($payload) {
            $product = Product::create([
                'category_id' => $payload['category_id'],
                'user_id' => auth()->id(),
                'slug' => $payload['slug'],
                'sku' => $payload['sku'],
                'base_price' => $payload['base_price'],
                'currency' => $payload['currency'],
                'status' => $payload['status'],
                'has_variants' => $payload['has_variants'],
                'tax_rate' => $payload['tax_rate'],
                'weight' => $payload['weight'],
                'is_featured' => $payload['is_featured'],
                'meta_title' => $payload['meta_title'],
                'meta_description' => $payload['meta_description'],
            ]);

            foreach ($payload['translations'] as $locale => $data) {
                $product->translations()->create([
                    'locale' => $locale,
                    'name' => $data['name'] ?: null,
                    'description' => $data['description'] ?: null,
                ]);
            }
        });

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load([
            'category.translations',
            'translations',
            'images' => fn ($q) => $q->whereNull('variant_id')->orderBy('sort_order')->orderByDesc('id'),
            'variants.values.attribute',
            'variants.values.value',
            'variants.images',
            'primaryImage',
        ])->loadCount(['variants', 'images', 'allReviews']);

        return view('dashboard.products.show', [
            'product' => $product,
            'canEdit' => $this->canCurrentUser('products.edit'),
        ]);
    }

    public function edit(Product $product): View
    {
        $product->load('translations');

        return view('dashboard.products.edit', [
            'product' => $product,
            'categories' => $this->categoriesList(),
            'form' => $this->productFormData($product),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $payload = $this->normalizedProductPayload($request, $product);

        DB::transaction(function () use ($payload, $product) {
            $product->update([
                'category_id' => $payload['category_id'],
                'slug' => $payload['slug'],
                'sku' => $payload['sku'],
                'base_price' => $payload['base_price'],
                'currency' => $payload['currency'],
                'status' => $payload['status'],
                'has_variants' => $payload['has_variants'],
                'tax_rate' => $payload['tax_rate'],
                'weight' => $payload['weight'],
                'is_featured' => $payload['is_featured'],
                'meta_title' => $payload['meta_title'],
                'meta_description' => $payload['meta_description'],
            ]);

            foreach ($payload['translations'] as $locale => $data) {
                $product->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $data['name'] ?: null,
                        'description' => $data['description'] ?: null,
                    ]
                );
            }
        });

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Product moved to trash successfully.');
    }

    public function restore(int $id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()
            ->route('dashboard.products.trash')
            ->with('success', 'Product restored successfully.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $product = Product::onlyTrashed()
            ->with(['images', 'variants.images', 'variants.values', 'translations'])
            ->findOrFail($id);

        DB::transaction(function () use ($product) {
            foreach ($product->images as $image) {
                $this->deleteImageFile($image->path);
                $image->delete();
            }

            foreach ($product->variants as $variant) {
                foreach ($variant->images as $image) {
                    $this->deleteImageFile($image->path);
                    $image->delete();
                }

                $variant->values()->delete();
                $variant->delete();
            }

            $product->translations()->delete();
            $product->forceDelete();
        });

        return redirect()
            ->route('dashboard.products.trash')
            ->with('success', 'Product permanently deleted.');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';

        $product->update([
            'status' => $newStatus,
        ]);

        if ($newStatus !== 'active') {
            $product->variants()->update(['is_active' => false]);
        }

        return back()->with('success', 'Product status updated successfully.');
    }

    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update([
            'is_featured' => !$product->is_featured,
        ]);

        return back()->with('success', 'Product featured flag updated successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:activate,deactivate,feature,unfeature,delete'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:products,id'],
        ]);

        $products = Product::whereIn('id', $request->ids)->get();

        foreach ($products as $product) {
            match ($request->action) {
                'activate' => $product->update(['status' => 'active']),
                'deactivate' => $product->update(['status' => 'inactive']),
                'feature' => $product->update(['is_featured' => true]),
                'unfeature' => $product->update(['is_featured' => false]),
                'delete' => $product->delete(),
            };
        }

        return back()->with('success', 'Bulk action applied successfully.');
    }

    public function variants(Product $product): View
    {
        $product->load([
            'variants.values.attribute',
            'variants.values.value',
            'variants.images',
        ]);

        $attributes = Attribute::query()
            ->where('is_active', true)
            ->with(['values' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('id')])
            ->orderBy('name')
            ->get();

        return view('dashboard.products.variants', [
            'product' => $product,
            'attributes' => $attributes,
        ]);
    }

    public function generateVariants(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'attribute_ids' => ['required', 'array', 'min:1'],
            'attribute_ids.*' => ['integer', 'exists:attributes,id'],
        ]);

        $attributes = Attribute::query()
            ->whereIn('id', $request->attribute_ids)
            ->with(['values' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('id')])
            ->get();

        if ($attributes->isEmpty() || $attributes->contains(fn ($attr) => $attr->values->isEmpty())) {
            return back()->with('error', 'Each selected attribute must have active values.');
        }

        $combinations = $this->buildCombinations(
            $attributes->map(fn ($attribute) => $attribute->values->map(fn ($value) => [
                'attribute_id' => $attribute->id,
                'attribute_value_id' => $value->id,
                'value_label' => $value->value,
            ])->all())->all()
        );

        DB::transaction(function () use ($product, $combinations) {
            foreach ($combinations as $combo) {
                $key = $this->buildCombinationKey(collect($combo)->pluck('attribute_value_id')->all());

                $exists = $product->variants()->where('combination_key', $key)->exists();

                if ($exists) {
                    continue;
                }

                $variant = $product->variants()->create([
                    'sku' => $product->sku . '-' . strtoupper(Str::random(5)),
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
            }
        });

        $product->update(['has_variants' => true]);

        return back()->with('success', 'Variants generated successfully.');
    }

    public function storeVariant(StoreProductVariantRequest $request, Product $product): RedirectResponse
    {
        $payload = $this->normalizedVariantPayload($request);

        $combinationKey = $this->buildCombinationKey(array_column($payload['values'], 'attribute_value_id'));

        if ($product->variants()->where('combination_key', $combinationKey)->exists()) {
            return back()->withErrors(['values' => 'This variant combination already exists.'])->withInput();
        }

        DB::transaction(function () use ($product, $payload, $combinationKey) {
            $variant = $product->variants()->create([
                'sku' => $payload['sku'],
                'price' => $payload['price'],
                'stock' => $payload['stock'],
                'combination_key' => $combinationKey,
                'weight' => $payload['weight'],
                'version' => 1,
                'is_active' => $payload['is_active'],
            ]);

            foreach ($payload['values'] as $value) {
                $variant->values()->create($value);
            }

            $product->update(['has_variants' => true]);
        });

        return back()->with('success', 'Variant created successfully.');
    }

    public function updateVariant(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $payload = $this->normalizedVariantPayload($request);
        $combinationKey = $this->buildCombinationKey(array_column($payload['values'], 'attribute_value_id'));

        $exists = $product->variants()
            ->where('combination_key', $combinationKey)
            ->where('id', '!=', $variant->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['values' => 'This variant combination already exists.'])->withInput();
        }

        DB::transaction(function () use ($variant, $payload, $combinationKey) {
            $variant->update([
                'sku' => $payload['sku'],
                'price' => $payload['price'],
                'stock' => $payload['stock'],
                'combination_key' => $combinationKey,
                'weight' => $payload['weight'],
                'version' => $variant->version + 1,
                'is_active' => $payload['is_active'],
            ]);

            $variant->values()->delete();

            foreach ($payload['values'] as $value) {
                $variant->values()->create($value);
            }
        });

        return back()->with('success', 'Variant updated successfully.');
    }

    public function toggleVariantStatus(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $variant->update([
            'is_active' => !$variant->is_active,
        ]);

        return back()->with('success', 'Variant status updated successfully.');
    }

    public function destroyVariant(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        DB::transaction(function () use ($variant, $product) {
            foreach ($variant->images as $image) {
                $this->deleteImageFile($image->path);
                $image->delete();
            }

            $variant->values()->delete();
            $variant->delete();

            if (!$product->variants()->exists()) {
                $product->update(['has_variants' => false]);
            }
        });

        return back()->with('success', 'Variant deleted successfully.');
    }

    public function images(Product $product): View
    {
        $product->load([
            'images' => fn ($q) => $q->orderBy('variant_id')->orderBy('sort_order')->orderByDesc('id'),
            'variants.values.value',
            'variants.images' => fn ($q) => $q->orderBy('sort_order')->orderByDesc('id'),
        ]);

        $generalImages = $product->images->whereNull('variant_id')->values();

        return view('dashboard.products.images', [
            'product' => $product,
            'generalImages' => $generalImages,
        ]);
    }

    public function storeImage(StoreProductImageRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product) {
            $hasPrimary = $product->images()->whereNull('variant_id')->where('is_primary', true)->exists();
            $sortOrder = (int) $product->images()->whereNull('variant_id')->max('sort_order');

            foreach ($request->file('images', []) as $index => $file) {
                $path = $file->store('products/general', 'public');

                $product->images()->create([
                    'variant_id' => null,
                    'path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => !$hasPrimary && $index === 0,
                    'sort_order' => $sortOrder + $index + 1,
                ]);
            }
        });

        return back()->with('success', 'Product images uploaded successfully.');
    }

    public function setPrimaryImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id && $image->variant_id === null, 404);

        DB::transaction(function () use ($product, $image) {
            $product->images()->whereNull('variant_id')->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return back()->with('success', 'Primary image updated successfully.');
    }

    public function sortImage(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $request->validate([
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $image->update([
            'sort_order' => (int) $request->sort_order,
        ]);

        return back()->with('success', 'Image order updated successfully.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $this->deleteImageFile($image->path);
        $image->delete();

        return back()->with('success', 'Image deleted successfully.');
    }

    public function storeVariantImage(StoreVariantImageRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        DB::transaction(function () use ($request, $product, $variant) {
            $hasPrimary = $variant->images()->where('is_primary', true)->exists();
            $sortOrder = (int) $variant->images()->max('sort_order');

            foreach ($request->file('images', []) as $index => $file) {
                $path = $file->store('products/variants', 'public');

                $product->images()->create([
                    'variant_id' => $variant->id,
                    'path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => !$hasPrimary && $index === 0,
                    'sort_order' => $sortOrder + $index + 1,
                ]);
            }
        });

        return back()->with('success', 'Variant images uploaded successfully.');
    }

    public function setPrimaryVariantImage(Product $product, ProductVariant $variant, ProductImage $image): RedirectResponse
    {
        abort_unless(
            $variant->product_id === $product->id
            && $image->product_id === $product->id
            && $image->variant_id === $variant->id,
            404
        );

        DB::transaction(function () use ($variant, $image) {
            $variant->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return back()->with('success', 'Primary variant image updated successfully.');
    }

    public function destroyVariantImage(Product $product, ProductVariant $variant, ProductImage $image): RedirectResponse
    {
        abort_unless(
            $variant->product_id === $product->id
            && $image->product_id === $product->id
            && $image->variant_id === $variant->id,
            404
        );

        $this->deleteImageFile($image->path);
        $image->delete();

        return back()->with('success', 'Variant image deleted successfully.');
    }

    private function categoriesList()
    {
        return Category::query()
            ->with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();
    }

    private function productFormData(?Product $product = null): array
    {
        $ar = $product?->translations->firstWhere('locale', 'ar');
        $en = $product?->translations->firstWhere('locale', 'en');

        return [
            'category_id' => old('category_id', $product->category_id ?? ''),
            'slug' => old('slug', $product->slug ?? ''),
            'sku' => old('sku', $product->sku ?? ''),
            'base_price' => old('base_price', $product->base_price ?? 0),
            'currency' => old('currency', $product->currency ?? 'SAR'),
            'status' => old('status', $product->status ?? 'active'),
            'has_variants' => old('has_variants', $product?->has_variants ?? false),
            'tax_rate' => old('tax_rate', $product->tax_rate ?? 0),
            'weight' => old('weight', $product->weight ?? 0),
            'is_featured' => old('is_featured', $product?->is_featured ?? false),
            'meta_title' => old('meta_title', $product->meta_title ?? ''),
            'meta_description' => old('meta_description', $product->meta_description ?? ''),
            'translations' => [
                'ar' => [
                    'name' => old('translations.ar.name', $ar?->name ?? ''),
                    'description' => old('translations.ar.description', $ar?->description ?? ''),
                ],
                'en' => [
                    'name' => old('translations.en.name', $en?->name ?? ''),
                    'description' => old('translations.en.description', $en?->description ?? ''),
                ],
            ],
        ];
    }

    private function normalizedProductPayload(Request $request, ?Product $product = null): array
    {
        $arName = trim((string) data_get($request->input('translations'), 'ar.name', ''));
        $enName = trim((string) data_get($request->input('translations'), 'en.name', ''));
        $slug = trim((string) $request->input('slug', ''));

        if ($slug === '') {
            $slug = $enName !== '' ? $enName : $arName;
        }

        $slug = $this->generateUniqueSlug($slug, $product?->id);

        return [
            'category_id' => (int) $request->category_id,
            'slug' => $slug,
            'sku' => trim((string) $request->sku),
            'base_price' => (float) $request->base_price,
            'currency' => strtoupper(trim((string) $request->currency)),
            'status' => (string) $request->status,
            'has_variants' => $request->boolean('has_variants'),
            'tax_rate' => (float) $request->input('tax_rate', 0),
            'weight' => (float) $request->input('weight', 0),
            'is_featured' => $request->boolean('is_featured'),
            'meta_title' => trim((string) $request->input('meta_title', '')),
            'meta_description' => trim((string) $request->input('meta_description', '')),
            'translations' => [
                'ar' => [
                    'name' => $arName,
                    'description' => trim((string) data_get($request->input('translations'), 'ar.description', '')),
                ],
                'en' => [
                    'name' => $enName,
                    'description' => trim((string) data_get($request->input('translations'), 'en.description', '')),
                ],
            ],
        ];
    }

    private function normalizedVariantPayload(Request $request): array
    {
        $values = collect($request->input('values', []))
            ->map(fn ($row) => [
                'attribute_id' => (int) $row['attribute_id'],
                'attribute_value_id' => (int) $row['attribute_value_id'],
            ])
            ->sortBy('attribute_id')
            ->values()
            ->all();

        return [
            'sku' => trim((string) $request->sku),
            'price' => (float) $request->price,
            'stock' => (int) $request->stock,
            'weight' => (float) $request->input('weight', 0),
            'is_active' => $request->boolean('is_active'),
            'values' => $values,
        ];
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

    private function deleteImageFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function canCurrentUser(string $permission): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->hasRole('owner')
            || $user->hasRole('admin')
            || $user->hasPermission($permission);
    }

    private function generateUniqueSlug(string $slug, ?int $ignoreProductId = null): string
    {
        $baseSlug = Str::slug(Str::lower(trim($slug)));

        if ($baseSlug === '') {
            $baseSlug = 'product';
        }

        $uniqueSlug = $baseSlug;
        $counter = 1;

        while (
            Product::query()
                ->when($ignoreProductId, fn ($q) => $q->where('id', '!=', $ignoreProductId))
                ->where('slug', $uniqueSlug)
                ->exists()
        ) {
            $uniqueSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $uniqueSlug;
    }
}
