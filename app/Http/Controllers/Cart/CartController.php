<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $subtotal = collect($cart)->sum(function ($item) {
            return (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0);
        });

        $couponData = session()->get('cart_coupon');
        $discount = 0;

        if ($couponData) {
            $discount = (float) ($couponData['discount'] ?? 0);
        }

        $grandTotal = max($subtotal - $discount, 0);

        return view('site.cart.cart-panel', compact(
            'cart',
            'subtotal',
            'discount',
            'grandTotal',
            'couponData'
        ));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
        ]);

        $product = Product::with('primaryImage')->findOrFail($request->product_id);

        $price = (float) $product->base_price;

        $variant = null;
        if ($request->filled('variant_id')) {
            $variant = ProductVariant::query()
                ->where('product_id', $product->id)
                ->findOrFail($request->variant_id);

            if (!is_null($variant->price)) {
                $price = (float) $variant->price;
            }
        }

        $image = $product->primaryImage?->path
            ? Storage::url($product->primaryImage->path)
            : asset('cozastore/images/product-01.jpg');

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

        $cart[$cartKey]['total_price'] =
            $cart[$cartKey]['price'] * $cart[$cartKey]['quantity'];

        session()->put('cart', $cart);

        // ✅ لو فيه كوبون مطبق، راجع صلاحيته بعد الإضافة
        $this->refreshCoupon();

        return redirect()->back()->with('success', 'Product added to cart ✅');
    }

    public function remove(string $key)
    {
        $key = urldecode($key);

        $cart = session()->get('cart', []);

        if (!isset($cart[$key])) {
            return redirect()->back()->with('error', 'Item not found in cart.');
        }

        unset($cart[$key]);

        if (empty($cart)) {
            session()->forget('cart');
        } else {
            session()->put('cart', $cart);
        }

        // ✅ راجع الكوبون بعد الحذف
        $this->refreshCoupon();

        return redirect()->back()->with('success', 'Removed from cart ✅');
    }

    public function update(Request $request)
    {
        $request->validate([
            'key'      => ['required'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->key])) {
            $cart[$request->key]['quantity'] = (int) $request->quantity;

            $cart[$request->key]['total_price'] =
                $cart[$request->key]['price'] * $cart[$request->key]['quantity'];

            session()->put('cart', $cart);
        }

        // ✅ راجع الكوبون بعد تعديل الكمية
        $this->refreshCoupon();

        return redirect()->back()->with('success', 'Cart updated ✅');
    }

    public function clear()
    {
        session()->forget('cart');
        session()->forget('cart_coupon');

        return redirect()->back()->with('success', 'Cart cleared ✅');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon' => ['required', 'string', 'max:100'],
        ]);

        $code = strtoupper(trim($request->coupon));
        $subtotal = $this->getCartSubtotal();

        if ($subtotal <= 0) {
            return back()->with('error', 'Cart is empty.');
        }

        $coupon = Coupon::whereRaw('UPPER(code) = ?', [$code])->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        if (!$coupon->is_active) {
            return back()->with('error', 'This coupon is not active.');
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return back()->with('error', 'This coupon is not available yet.');
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return back()->with('error', 'This coupon has expired.');
        }

        if (!is_null($coupon->usage_limit) && $coupon->used_count >= $coupon->usage_limit) {
            return back()->with('error', 'This coupon reached its usage limit.');
        }

        if (!is_null($coupon->min_order_amount) && $subtotal < (float) $coupon->min_order_amount) {
            return back()->with('error', 'Minimum order amount for this coupon is ' . number_format((float) $coupon->min_order_amount, 2));
        }

        $discount = $this->calculateCouponDiscount($coupon, $subtotal);

        session()->put('cart_coupon', [
            'id'       => $coupon->id,
            'code'     => $coupon->code,
            'type'     => $coupon->type,
            'value'    => (float) $coupon->value,
            'discount' => $discount,
        ]);

        return back()->with('success', 'Coupon applied successfully ✅');
    }

    public function removeCoupon()
    {
        session()->forget('cart_coupon');

        return back()->with('success', 'Coupon removed successfully ✅');
    }

    private function getCartSubtotal(): float
    {
        $cart = session()->get('cart', []);

        return collect($cart)->sum(function ($item) {
            return (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0);
        });
    }

    private function calculateCouponDiscount(Coupon $coupon, float $subtotal): float
    {
        $discount = 0;

        if ($coupon->type === 'fixed') {
            $discount = (float) $coupon->value;
        }

        if ($coupon->type === 'percent') {
            $discount = ($subtotal * (float) $coupon->value) / 100;
        }

        if (!is_null($coupon->max_discount_amount)) {
            $discount = min($discount, (float) $coupon->max_discount_amount);
        }

        if ($discount > $subtotal) {
            $discount = $subtotal;
        }

        return max($discount, 0);
    }

    private function refreshCoupon(): void
    {
        $couponData = session()->get('cart_coupon');

        if (!$couponData) {
            return;
        }

        $subtotal = $this->getCartSubtotal();

        if ($subtotal <= 0) {
            session()->forget('cart_coupon');
            return;
        }

        $coupon = Coupon::find($couponData['id'] ?? null);

        if (!$coupon || !$coupon->is_active) {
            session()->forget('cart_coupon');
            return;
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            session()->forget('cart_coupon');
            return;
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            session()->forget('cart_coupon');
            return;
        }

        if (!is_null($coupon->min_order_amount) && $subtotal < (float) $coupon->min_order_amount) {
            session()->forget('cart_coupon');
            return;
        }

        $discount = $this->calculateCouponDiscount($coupon, $subtotal);

        session()->put('cart_coupon', [
            'id'       => $coupon->id,
            'code'     => $coupon->code,
            'type'     => $coupon->type,
            'value'    => (float) $coupon->value,
            'discount' => $discount,
        ]);
    }
}
