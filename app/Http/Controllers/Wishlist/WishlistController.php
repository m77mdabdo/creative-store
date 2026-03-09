<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::with(['items.product', 'items.variant'])
            ->firstOrCreate([
                'user_id' => Auth::id()
            ]);

        return view('site.wishlist.index', compact('wishlist'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->filled('variant_id')) {
            $variant = ProductVariant::findOrFail($request->variant_id);

            if ($variant->product_id != $product->id) {
                return back()->with('error', 'Invalid variant selected.');
            }
        }

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $exists = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $request->variant_id)
            ->exists();

        if ($exists) {
            return back()->with('info', 'Product already exists in wishlist.');
        }

        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
            'variant_id' => $request->variant_id,
        ]);

        return back()->with('success', 'Product added to wishlist successfully.');
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->firstOrFail();

        $item = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return back()->with('success', 'Item removed from wishlist.');
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $item = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($item) {
            $item->delete();
            return back()->with('success', 'Removed from wishlist.');
        }

        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id,
        ]);

        return back()->with('success', 'Added to wishlist.');
    }
}
