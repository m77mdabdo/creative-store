<?php

namespace App\Http\Controllers\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    //

     public function index()
    {
        $cart = session()->get('cart', []);
        $grandTotal = collect($cart)->sum('total_price');

        return view('site.cart.cart-panel', compact('cart', 'grandTotal'));
    }

     public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required','exists:products,id'],
            'quantity'   => ['required','integer','min:1'],
            'variant_id' => ['nullable','exists:product_variants,id'],
        ]);

        $product = Product::with('primaryImage')->findOrFail($request->product_id);

        // السعر الافتراضي
        $price = (float) $product->base_price;

        // لو فيه variant استخدم سعره
        $variant = null;
        if ($request->filled('variant_id')) {
            $variant = ProductVariant::query()
                ->where('product_id', $product->id)
                ->findOrFail($request->variant_id);

            if (!is_null($variant->price)) {
                $price = (float) $variant->price;
            }
        }

        // صورة المنتج
        $image = $product->primaryImage?->path
            ? Storage::url($product->primaryImage->path)
            : asset('cozastore/images/product-01.jpg');

        // مفتاح مميز لكل منتج + variant
        $cartKey = $variant
            ? $product->id . '_' . $variant->id
            : (string) $product->id;

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += (int) $request->quantity;
        } else {
            $cart[$cartKey] = [
                'product_id'   => $product->id,
                'variant_id'   => $variant?->id,
                'name'         => $product->name ?? $product->slug,
                'price'        => $price,
                'quantity'     => (int) $request->quantity,
                'image'        => $image,
                'currency'     => $product->currency ?? 'SAR',
            ];
        }

        // تحديث إجمالي العنصر
        $cart[$cartKey]['total_price'] =
            $cart[$cartKey]['price'] * $cart[$cartKey]['quantity'];

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart ✅');
    }


    /*
    |--------------------------------------------------------------------------
    | حذف عنصر من الكارت
    |--------------------------------------------------------------------------
    */
    public function remove(string $key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Removed from cart ✅');
    }


    /*
    |--------------------------------------------------------------------------
    | تحديث الكمية
    |--------------------------------------------------------------------------
    */
    public function update(Request $request)
    {
        $request->validate([
            'key'      => ['required'],
            'quantity' => ['required','integer','min:1'],
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->key])) {
            $cart[$request->key]['quantity'] = (int) $request->quantity;

            $cart[$request->key]['total_price'] =
                $cart[$request->key]['price'] * $cart[$request->key]['quantity'];

            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Cart updated ✅');
    }


    /*
    |--------------------------------------------------------------------------
    | تفريغ الكارت بالكامل
    |--------------------------------------------------------------------------
    */
    public function clear()
    {
        session()->forget('cart');

        return redirect()->back()->with('success', 'Cart cleared ✅');
    }
}
