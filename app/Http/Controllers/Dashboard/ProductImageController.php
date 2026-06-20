<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Product\StoreProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductImageController extends Controller
{
    public function index(Product $product): View
    {
        $product->load([
            'images' => fn ($q) => $q->orderBy('variant_id')->orderBy('sort_order')->orderByDesc('id'),
            'variants.values.value',
            'variants.images' => fn ($q) => $q->orderBy('sort_order')->orderByDesc('id'),
        ]);

        $generalImages = $product->images->whereNull('variant_id')->values();

        return view('dashboard.product_images.index', [
            'product' => $product,
            'generalImages' => $generalImages,
        ]);
    }

    public function store(StoreProductImageRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product) {
            $hasPrimary = $product->images()->whereNull('variant_id')->where('is_primary', true)->exists();
            $sortOrder = (int) $product->images()->whereNull('variant_id')->max('sort_order');

            foreach ($request->file('images', []) as $index => $file) {
                $path = $file->store('products/general', config('filesystems.media_disk'));

                $product->images()->create([
                    'variant_id' => null,
                    'path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => ! $hasPrimary && $index === 0,
                    'sort_order' => $sortOrder + $index + 1,
                ]);
            }
        });

        return back()->with('success', 'Product images uploaded successfully.');
    }

    public function setPrimary(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id && $image->variant_id === null, 404);

        DB::transaction(function () use ($product, $image) {
            $product->images()->whereNull('variant_id')->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return back()->with('success', 'Primary image updated successfully.');
    }

    public function sort(Request $request, Product $product, ProductImage $image): RedirectResponse
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

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        if ($image->path && media_disk()->exists($image->path)) {
            media_disk()->delete($image->path);
        }

        $image->delete();

        return back()->with('success', 'Image deleted successfully.');
    }
}
