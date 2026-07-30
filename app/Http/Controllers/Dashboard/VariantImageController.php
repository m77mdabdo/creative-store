<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Product\StoreVariantImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class VariantImageController extends Controller
{
    public function store(StoreVariantImageRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        DB::transaction(function () use ($request, $product, $variant) {
            $hasPrimary = $variant->images()->where('is_primary', true)->exists();
            $sortOrder = (int) $variant->images()->max('sort_order');

            foreach ($request->file('images', []) as $index => $file) {
                $path = $file->store('products/variants', config('filesystems.media_disk'));

                $product->images()->create([
                    'variant_id' => $variant->id,
                    'path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => ! $hasPrimary && $index === 0,
                    'sort_order' => $sortOrder + $index + 1,
                ]);
            }
        });

        return back()->with('success', __('Variant images uploaded successfully.'));
    }

    public function setPrimary(Product $product, ProductVariant $variant, ProductImage $image): RedirectResponse
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

        return back()->with('success', __('Primary variant image updated successfully.'));
    }

    public function destroy(Product $product, ProductVariant $variant, ProductImage $image): RedirectResponse
    {
        abort_unless(
            $variant->product_id === $product->id
            && $image->product_id === $product->id
            && $image->variant_id === $variant->id,
            404
        );

        if ($image->path && media_disk()->exists($image->path)) {
            media_disk()->delete($image->path);
        }

        $image->delete();

        return back()->with('success', __('Variant image deleted successfully.'));
    }
}
