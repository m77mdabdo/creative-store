<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Product\StoreProductVariantRequest;
use App\Http\Requests\Dashboard\Product\UpdateProductVariantRequest;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Dashboard\VariantGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function __construct(
        protected VariantGeneratorService $variantGenerator
    ) {}

    public function index(Product $product): View
    {
        $product->load([
            'attributes.activeValues',
            'variants.values.attribute',
            'variants.values.value',
            'variants.images',
        ]);

        $attributes = $product->attributes
            ->filter(fn($attribute) => $attribute->is_active)
            ->values();

        return view('dashboard.product_variants.index', [
            'product' => $product,
            'attributes' => $attributes,
        ]);
    }

    public function generate(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'attribute_ids' => ['required', 'array', 'min:1'],
            'attribute_ids.*' => ['integer', 'exists:attributes,id'],
        ]);

        $created = $this->variantGenerator->generateMissingVariants(
            product: $product,
            attributeIds: $request->input('attribute_ids', [])
        );

        if ($created === 0) {
            return back()->with('error', __('No new variants were generated.'));
        }

        return back()->with('success', "Generated {$created} new variants successfully.");
    }

    public function store(StoreProductVariantRequest $request, Product $product): RedirectResponse
    {
        $payload = $this->normalizePayload($request);

        $combinationKey = $this->buildCombinationKey(array_column($payload['values'], 'attribute_value_id'));

        if ($product->variants()->where('combination_key', $combinationKey)->exists()) {
            return back()->withErrors(['values' => __('This variant combination already exists.')])->withInput();
        }

        $allowedAttributeIds = $product->attributes()->pluck('attributes.id')->map(fn($id) => (int) $id)->all();

        foreach ($payload['values'] as $row) {
            if (! in_array((int) $row['attribute_id'], $allowedAttributeIds, true)) {
                return back()->withErrors([
                    'values' => __('One or more selected attributes are not linked to this product.')
                ])->withInput();
            }
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

            if (! $product->has_variants) {
                $product->update(['has_variants' => true]);
            }
        });

        return back()->with('success', __('Variant created successfully.'));
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $payload = $this->normalizePayload($request);
        $combinationKey = $this->buildCombinationKey(array_column($payload['values'], 'attribute_value_id'));

        $exists = $product->variants()
            ->where('combination_key', $combinationKey)
            ->where('id', '!=', $variant->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['values' => __('This variant combination already exists.')])->withInput();
        }
        $allowedAttributeIds = $product->attributes()->pluck('attributes.id')->map(fn($id) => (int) $id)->all();

        foreach ($payload['values'] as $row) {
            if (! in_array((int) $row['attribute_id'], $allowedAttributeIds, true)) {
                return back()->withErrors([
                    'values' => __('One or more selected attributes are not linked to this product.')
                ])->withInput();
            }
        }

        DB::transaction(function () use ($variant, $payload, $combinationKey) {
            $variant->update([
                'sku' => $payload['sku'],
                'price' => $payload['price'],
                'stock' => $payload['stock'],
                'combination_key' => $combinationKey,
                'weight' => $payload['weight'],
                'version' => (int) $variant->version + 1,
                'is_active' => $payload['is_active'],
            ]);

            $variant->values()->delete();

            foreach ($payload['values'] as $value) {
                $variant->values()->create($value);
            }
        });

        return back()->with('success', __('Variant updated successfully.'));
    }

    public function toggleStatus(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $variant->update([
            'is_active' => ! $variant->is_active,
            'version' => (int) $variant->version + 1,
        ]);

        return back()->with('success', __('Variant status updated successfully.'));
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        DB::transaction(function () use ($variant, $product) {
            $variant->loadMissing('images');

            foreach ($variant->images as $image) {
                if ($image->path && Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->delete();
            }

            $variant->values()->delete();
            $variant->delete();

            if (! $product->variants()->exists()) {
                $product->update(['has_variants' => false]);
            }
        });

        return back()->with('success', __('Variant deleted successfully.'));
    }

private function normalizePayload(Request $request): array
{
    $values = collect($request->input('values', []))
        ->map(fn($row) => [
            'attribute_id' => (int) $row['attribute_id'],
            'attribute_value_id' => (int) $row['attribute_value_id'],
        ])
        ->sortBy('attribute_id')
        ->values()
        ->all();

    return [
        'sku' => trim((string) $request->input('sku')),
        'price' => (float) $request->input('price', 0),
        'stock' => max(0, (int) $request->input('stock', 0)),
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
}
