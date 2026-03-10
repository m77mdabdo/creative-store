<?php

namespace App\Http\Controllers\Wishlist;

use App\Http\Controllers\Controller;
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

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $query = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $request->product_id);

        if ($request->filled('variant_id')) {
            $query->where('variant_id', $request->variant_id);
        } else {
            $query->whereNull('variant_id');
        }

        $item = $query->first();

        if ($item) {
            $item->delete();
            return back()->with('success', 'Removed from wishlist');
        }

        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $request->product_id,
            'variant_id' => $request->filled('variant_id') ? $request->variant_id : null,
        ]);

        return back()->with('success', 'Added to wishlist');
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->firstOrFail();

        $item = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return back()->with('success', 'Item removed');
    }
}
