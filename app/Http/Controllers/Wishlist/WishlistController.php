<?php

namespace App\Http\Controllers\Wishlist;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::with([
            'items' => function ($query) {
                $query->latest();
            },
            'items.product.primaryImage',
            'items.product.translations',
            'items.variant',
        ])->firstOrCreate([
            'user_id' => Auth::id()
        ]);

        // تنظيف العناصر المكسورة: لو المنتج اتحذف من قاعدة البيانات
        $validItems = $wishlist->items
            ->filter(fn ($item) => !is_null($item->product))
            ->values();

        $wishlist->setRelation('items', $validItems);

        return view('site.wishlist.index', compact('wishlist'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
        ]);

        $wishlist = Wishlist::createOrFirst(['user_id' => Auth::id()]);
        $variantId = $request->filled('variant_id') ? (int) $request->variant_id : null;

        // Lock the customer's wishlist row so a double click toggles once, not twice.
        $added = DB::transaction(function () use ($wishlist, $request, $variantId) {
            Wishlist::query()->whereKey($wishlist->id)->lockForUpdate()->first();

            $item = WishlistItem::query()
                ->where('wishlist_id', $wishlist->id)
                ->where('product_id', $request->product_id)
                ->when($variantId, fn ($q) => $q->where('variant_id', $variantId), fn ($q) => $q->whereNull('variant_id'))
                ->first();

            if ($item) {
                $item->delete();

                return false;
            }

            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id'  => $request->product_id,
                'variant_id'  => $variantId,
            ]);

            return true;
        });

        if (! $added) {
            return back()->with('success', __('Removed from wishlist'));
        }

        return back()->with('success', __('Added to wishlist'));
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->firstOrFail();

        $item = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return back()->with('success', __('Item removed'));
    }
}
