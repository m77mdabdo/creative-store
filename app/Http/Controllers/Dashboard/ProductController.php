<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Product\StoreProductRequest;
use App\Http\Requests\Dashboard\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\Attribute;

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
            'attributes' => $this->attributesList(),
            'form' => $this->productFormData(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $payload = $this->normalizedProductPayload($request);

        DB::transaction(function () use ($payload, $request) {
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

            $attributeIds = $payload['has_variants']
                ? collect($request->input('attribute_ids', []))->map(fn($id) => (int) $id)->unique()->values()->all()
                : [];

            $product->attributes()->sync($attributeIds);
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
            'attributes',
            'images' => fn($q) => $q->whereNull('variant_id')->orderBy('sort_order')->orderByDesc('id'),
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
        $product->load(['translations', 'attributes']);

        return view('dashboard.products.edit', [
            'product' => $product,
            'categories' => $this->categoriesList(),
            'attributes' => $this->attributesList(),
            'form' => $this->productFormData($product),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $payload = $this->normalizedProductPayload($request, $product);

        DB::transaction(function () use ($payload, $product, $request) {
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

            $attributeIds = $payload['has_variants']
                ? collect($request->input('attribute_ids', []))->map(fn($id) => (int) $id)->unique()->values()->all()
                : [];

            $product->attributes()->sync($attributeIds);

            if (! $payload['has_variants']) {
                $product->variants()->delete();
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
        // products.status is draft|active|archived; "inactive" was rejected by
        // the column. Variants are left alone: the product status already hides
        // it everywhere, and re-enabling must not leave every variant switched off.
        $product->update([
            'status' => $product->status === 'active' ? 'draft' : 'active',
        ]);

        return back()->with('success', 'Product status updated successfully.');
    }

    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update([
            'is_featured' => ! $product->is_featured,
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
                'deactivate' => $product->update(['status' => 'draft']),
                'feature' => $product->update(['is_featured' => true]),
                'unfeature' => $product->update(['is_featured' => false]),
                'delete' => $product->delete(),
            };
        }

        return back()->with('success', 'Bulk action applied successfully.');
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
        'attribute_ids' => old(
            'attribute_ids',
            $product ? $product->attributes->pluck('id')->map(fn ($id) => (string) $id)->all() : []
        ),
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

    private function deleteImageFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function canCurrentUser(string $permission): bool
    {
        $user = auth()->user();

        if (! $user) {
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
            ->when($ignoreProductId, fn($q) => $q->where('id', '!=', $ignoreProductId))
            ->where('slug', $uniqueSlug)
            ->exists()
        ) {
            $uniqueSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $uniqueSlug;
    }
    private function attributesList()
{
    return Attribute::query()
        ->where('is_active', true)
        ->with(['activeValues'])
        ->orderBy('name')
        ->get();
}
}
