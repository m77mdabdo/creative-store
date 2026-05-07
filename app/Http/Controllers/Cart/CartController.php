<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private const VAT_RATE = 0.15;
    private const MAX_QTY_PER_ITEM = 20;

    public function index()
    {
        $cart = session()->get('cart', []);
        $cart = $this->syncCartWithDatabase($cart);

        if (empty($cart)) {
            session()->forget('cart');
            session()->forget('cart_coupon');
            session()->forget('cart_product_coupons');
        } else {
            session()->put('cart', $cart);
        }

        $this->refreshCoupons();

        $subtotal = $this->getCartSubtotal($cart);

        $cartCouponData = session()->get('cart_coupon');
        $productCouponsData = session()->get('cart_product_coupons', []);

        $productDiscount = $this->calculateAllProductCouponsDiscount($cart, $productCouponsData);
        $cartDiscount = (float) ($cartCouponData['discount'] ?? 0);
        $discount = $productDiscount + $cartDiscount;

        $netTotal = max($subtotal - $discount, 0);
        $vatValue = $netTotal * self::VAT_RATE;
        $grandTotal = $netTotal + $vatValue;

        session([
            'cart_summary' => [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax_total' => $vatValue,
                'total' => $grandTotal,
            ]
        ]);

        return view('site.cart.cart-panel', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'cartDiscount' => $cartDiscount,
            'productDiscount' => $productDiscount,
            'discount' => $discount,
            'netTotal' => $netTotal,
            'vatValue' => $vatValue,
            'grandTotal' => $grandTotal,
            'cartCouponData' => $cartCouponData,
            'productCouponsData' => $productCouponsData,
        ]);
    }

public function add(Request $request)
{
    $validated = $request->validate([
        'product_id' => ['required', 'integer', 'exists:products,id'],
        'quantity' => ['required', 'integer', 'min:1', 'max:' . self::MAX_QTY_PER_ITEM],
        'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
        'coupon_code' => ['nullable', 'string', 'max:100'],
    ]);

    $product = Product::with('primaryImage')->findOrFail($validated['product_id']);

    $variant = null;
    $price = (float) $product->base_price;
    $image = $product->primaryImage?->path ?? null;

    $selectedOptions = [
        'color' => null,
        'size' => null,
        'options' => [],
    ];

    if (!empty($validated['variant_id'])) {
        $variant = ProductVariant::query()
            ->with([
                'values.attribute:id,slug,name,is_active',
                'values.value:id,attribute_id,value,slug,sort_order,is_active',
                'images:id,product_id,variant_id,path,alt_text,is_primary,sort_order',
            ])
            ->where('id', $validated['variant_id'])
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->firstOrFail();

        if ((int) $variant->stock < 1) {
            return back()->with('error', 'Selected variant is out of stock.');
        }

        if ((int) $validated['quantity'] > (int) $variant->stock) {
            return back()->with('error', 'Requested quantity exceeds available stock.');
        }

        if (!is_null($variant->price)) {
            $price = (float) $variant->price;
        }

        $variantImage = $variant->images
            ? $variant->images->sortBy([
                ['is_primary', 'desc'],
                ['sort_order', 'asc'],
                ['id', 'asc'],
            ])->first()
            : null;

        if ($variantImage?->path) {
            $image = $variantImage->path;
        }

        $selectedOptions = $this->extractVariantOptions($variant);
    }

    $cartKey = $variant
        ? $product->id . '_' . $variant->id
        : (string) $product->id;

    $cart = session()->get('cart', []);
    $newQuantity = (int) $validated['quantity'];

    if (isset($cart[$cartKey])) {
        $newQuantity += (int) ($cart[$cartKey]['quantity'] ?? 0);
    }

    $newQuantity = min($newQuantity, self::MAX_QTY_PER_ITEM);

    if ($variant && $newQuantity > (int) $variant->stock) {
        return back()->with('error', 'Requested quantity exceeds available stock.');
    }

    $cart[$cartKey] = [
        'product_id' => $product->id,
        'variant_id' => $variant?->id,
        'slug' => $product->slug,
        'name' => $product->name ?? $product->slug,
        'sku' => $variant?->sku ?? $product->sku ?? ('SKU-' . $product->id),
        'price' => $price,
        'quantity' => $newQuantity,
        'image' => $image,
        'currency' => $product->currency ?? 'SAR',
        'color' => $selectedOptions['color'],
        'size' => $selectedOptions['size'],
        'variant_options' => $selectedOptions['options'],
        'tax_rate' => 0,
        'total_price' => $price * $newQuantity,
    ];

    session()->put('cart', $cart);

    if (!empty($validated['coupon_code'])) {
        $code = strtoupper(trim($validated['coupon_code']));
        $coupon = Coupon::query()->where('code', $code)->first();

        if (!$coupon) {
            $this->refreshCoupons();
            return back()->with('error', 'Invalid coupon code.');
        }

        $subtotal = $this->getCartSubtotal($cart);

        if (!$this->isCouponValid($coupon, $subtotal)) {
            return back()->with('error', $this->getCouponErrorMessage($coupon, $subtotal));
        }

        if (!is_null($coupon->product_id)) {
            if ((int) $coupon->product_id !== (int) $product->id) {
                return back()->with('error', 'This coupon is not valid for this product.');
            }

            $this->storeProductCoupon($coupon, $cart);
            $this->refreshCoupons();

            return back()->with('success', 'Product added to cart and product coupon applied successfully.');
        }

        $discount = $this->calculateCouponDiscount($coupon, $subtotal);

        session()->put('cart_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'discount' => $discount,
        ]);

        $this->refreshCoupons();

        return back()->with('success', 'Product added to cart and cart coupon applied successfully.');
    }

    $this->refreshCoupons();

    return back()->with('success', 'Product added to cart successfully.');
}

    public function remove(string $key)
    {
        $key = urldecode($key);
        $cart = session()->get('cart', []);

        if (!array_key_exists($key, $cart)) {
            return back()->with('error', 'Cart item not found.');
        }

        unset($cart[$key]);

        if (empty($cart)) {
            session()->forget('cart');
            session()->forget('cart_coupon');
            session()->forget('cart_product_coupons');
        } else {
            session()->put('cart', $cart);
        }

        $this->refreshCoupons();

        return back()->with('success', 'Item removed from cart successfully.');
    }

  public function update(Request $request)
{
    $validated = $request->validate([
        'key' => ['required', 'string'],
        'quantity' => ['required', 'integer', 'min:1', 'max:' . self::MAX_QTY_PER_ITEM],
    ]);

    $cart = session()->get('cart', []);

    if (!array_key_exists($validated['key'], $cart)) {
        return back()->with('error', 'Cart item not found.');
    }

    $item = $cart[$validated['key']];
    $product = Product::with('primaryImage')->find($item['product_id'] ?? null);

    if (!$product) {
        unset($cart[$validated['key']]);
        session()->put('cart', $cart);
        $this->refreshCoupons();

        return back()->with('error', 'Product no longer exists and was removed from cart.');
    }

    $variant = null;
    $price = (float) $product->base_price;
    $image = $product->primaryImage?->path ?? null;

    $selectedOptions = [
        'color' => null,
        'size' => null,
        'options' => [],
    ];

    if (!empty($item['variant_id'])) {
        $variant = ProductVariant::query()
            ->with([
                'values.attribute:id,slug,name,is_active',
                'values.value:id,attribute_id,value,slug,sort_order,is_active',
                'images:id,product_id,variant_id,path,alt_text,is_primary,sort_order',
            ])
            ->where('id', $item['variant_id'])
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (!$variant) {
            unset($cart[$validated['key']]);
            session()->put('cart', $cart);
            $this->refreshCoupons();

            return back()->with('error', 'Variant no longer exists and was removed from cart.');
        }

        if ((int) $variant->stock < 1) {
            unset($cart[$validated['key']]);
            session()->put('cart', $cart);
            $this->refreshCoupons();

            return back()->with('error', 'Selected variant is out of stock and was removed from cart.');
        }

        if (!is_null($variant->price)) {
            $price = (float) $variant->price;
        }

        $variantImage = $variant->images
            ? $variant->images->sortBy([
                ['is_primary', 'desc'],
                ['sort_order', 'asc'],
                ['id', 'asc'],
            ])->first()
            : null;

        if ($variantImage?->path) {
            $image = $variantImage->path;
        }

        $selectedOptions = $this->extractVariantOptions($variant);
    }

    $quantity = max(1, min((int) $validated['quantity'], self::MAX_QTY_PER_ITEM));

    if ($variant && $quantity > (int) $variant->stock) {
        return back()->with('error', 'Requested quantity exceeds available stock.');
    }

    $cart[$validated['key']]['price'] = $price;
    $cart[$validated['key']]['quantity'] = $quantity;
    $cart[$validated['key']]['image'] = $image;
    $cart[$validated['key']]['sku'] = $variant?->sku ?? $product->sku ?? ('SKU-' . $product->id);
    $cart[$validated['key']]['color'] = $selectedOptions['color'];
    $cart[$validated['key']]['size'] = $selectedOptions['size'];
    $cart[$validated['key']]['variant_options'] = $selectedOptions['options'];
    $cart[$validated['key']]['total_price'] = $price * $quantity;

    session()->put('cart', $cart);
    $this->refreshCoupons();

    return back()->with('success', 'Cart updated successfully.');
}

    public function clear()
    {
        session()->forget('cart');
        session()->forget('cart_coupon');
        session()->forget('cart_product_coupons');

        return back()->with('success', 'Cart cleared successfully.');
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon' => ['required', 'string', 'max:100'],
        ]);

        $code = strtoupper(trim($validated['coupon']));
        $cart = session()->get('cart', []);
        $subtotal = $this->getCartSubtotal($cart);

        if ($subtotal <= 0) {
            return back()->with('error', 'Cart is empty.');
        }

        $coupon = Coupon::query()->where('code', $code)->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        if (!$this->isCouponValid($coupon, $subtotal)) {
            return back()->with('error', $this->getCouponErrorMessage($coupon, $subtotal));
        }

        if (!is_null($coupon->product_id)) {
            $targetSubtotal = $this->getTargetSubtotalForProduct($cart, (int) $coupon->product_id);

            if ($targetSubtotal <= 0) {
                return back()->with('error', 'This coupon is valid only for a specific product that is not in your cart.');
            }

            $this->storeProductCoupon($coupon, $cart);
            $this->refreshCoupons();

            return back()->with('success', 'Product coupon applied successfully.');
        }

        $discount = $this->calculateCouponDiscount($coupon, $subtotal);

        session()->put('cart_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'discount' => $discount,
        ]);

        $this->refreshCoupons();

        return back()->with('success', 'Cart coupon applied successfully.');
    }

    public function removeCoupon()
    {
        session()->forget('cart_coupon');
        return back()->with('success', 'Cart coupon removed successfully.');
    }

    public function removeProductCoupon(int $productId)
    {
        $productCoupons = session()->get('cart_product_coupons', []);

        if (isset($productCoupons[$productId])) {
            unset($productCoupons[$productId]);
            session()->put('cart_product_coupons', $productCoupons);
        }

        return back()->with('success', 'Product coupon removed successfully.');
    }

    public function previewCoupon(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'coupon_code' => ['required', 'string', 'max:100'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $variant = null;
        $price = (float) $product->base_price;

        if (!empty($validated['variant_id'])) {
            $variant = ProductVariant::where('id', $validated['variant_id'])
                ->where('product_id', $product->id)
                ->firstOrFail();

            if (!is_null($variant->price)) {
                $price = (float) $variant->price;
            }
        }

        $code = strtoupper(trim($validated['coupon_code']));
        $coupon = Coupon::query()->where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid coupon code.',
            ]);
        }

        if (!is_null($coupon->product_id) && (int) $coupon->product_id !== (int) $product->id) {
            return response()->json([
                'ok' => false,
                'message' => 'This coupon is not valid for this product.',
            ]);
        }

        if (!$coupon->is_active) {
            return response()->json(['ok' => false, 'message' => 'This coupon is not active.']);
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return response()->json(['ok' => false, 'message' => 'This coupon is not available yet.']);
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return response()->json(['ok' => false, 'message' => 'This coupon has expired.']);
        }

        $discount = 0;

        if ($coupon->type === 'fixed') {
            $discount = (float) $coupon->value;
        } elseif ($coupon->type === 'percent') {
            $discount = ($price * (float) $coupon->value) / 100;
        }

        if (!is_null($coupon->max_discount_amount)) {
            $discount = min($discount, (float) $coupon->max_discount_amount);
        }

        $discount = min($discount, $price);
        $final = max($price - $discount, 0);

        return response()->json([
            'ok' => true,
            'message' => 'Coupon applied successfully.',
            'original' => round($price, 2),
            'discount' => round($discount, 2),
            'final' => round($final, 2),
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'scope' => is_null($coupon->product_id) ? 'cart' : 'product',
        ]);
    }

    private function storeProductCoupon(Coupon $coupon, array $cart): void
    {
        $targetSubtotal = $this->getTargetSubtotalForProduct($cart, (int) $coupon->product_id);

        if ($targetSubtotal <= 0) {
            return;
        }

        $discount = $this->calculateCouponDiscount($coupon, $targetSubtotal);
        $productCoupons = session()->get('cart_product_coupons', []);

        $productCoupons[(int) $coupon->product_id] = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'discount' => $discount,
            'product_id' => (int) $coupon->product_id,
        ];

        session()->put('cart_product_coupons', $productCoupons);
    }

    private function getTargetSubtotalForProduct(array $cart, int $productId): float
    {
        return (float) collect($cart)->sum(function ($item) use ($productId) {
            return ((int) ($item['product_id'] ?? 0) === $productId)
                ? (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0)
                : 0;
        });
    }

    private function calculateAllProductCouponsDiscount(array $cart, array $productCouponsData): float
    {
        $totalDiscount = 0;

        foreach ($productCouponsData as $productId => $couponData) {
            $coupon = Coupon::find($couponData['id'] ?? null);

            if (!$coupon || is_null($coupon->product_id)) {
                continue;
            }

            $targetSubtotal = $this->getTargetSubtotalForProduct($cart, (int) $productId);

            if ($targetSubtotal <= 0) {
                continue;
            }

            if (!$this->isCouponValid($coupon, $targetSubtotal)) {
                continue;
            }

            $totalDiscount += $this->calculateCouponDiscount($coupon, $targetSubtotal);
        }

        return $totalDiscount;
    }

    private function getCartSubtotal(array $cart): float
    {
        return (float) collect($cart)->sum(function ($item) {
            return (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0);
        });
    }

    private function calculateCouponDiscount(Coupon $coupon, float $subtotal): float
    {
        $discount = 0.0;

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

    private function refreshCoupons(): void
    {
        $cart = session()->get('cart', []);
        $subtotal = $this->getCartSubtotal($cart);

        if ($subtotal <= 0) {
            session()->forget('cart_coupon');
            session()->forget('cart_product_coupons');
            return;
        }

        $this->refreshCartCoupon($subtotal);
        $this->refreshProductCoupons($cart);
    }

    private function refreshCartCoupon(float $subtotal): void
    {
        $couponData = session()->get('cart_coupon');

        if (!$couponData) {
            return;
        }

        $coupon = Coupon::find($couponData['id'] ?? null);

        if (!$coupon || !$this->isCouponValid($coupon, $subtotal) || !is_null($coupon->product_id)) {
            session()->forget('cart_coupon');
            return;
        }

        $discount = $this->calculateCouponDiscount($coupon, $subtotal);

        session()->put('cart_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'discount' => $discount,
        ]);
    }

    private function refreshProductCoupons(array $cart): void
    {
        $productCoupons = session()->get('cart_product_coupons', []);

        if (empty($productCoupons)) {
            return;
        }

        $refreshedCoupons = [];

        foreach ($productCoupons as $productId => $couponData) {
            $coupon = Coupon::find($couponData['id'] ?? null);

            if (!$coupon || is_null($coupon->product_id)) {
                continue;
            }

            $targetSubtotal = $this->getTargetSubtotalForProduct($cart, (int) $productId);

            if ($targetSubtotal <= 0) {
                continue;
            }

            if (!$this->isCouponValid($coupon, $targetSubtotal)) {
                continue;
            }

            $discount = $this->calculateCouponDiscount($coupon, $targetSubtotal);

            $refreshedCoupons[(int) $productId] = [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => (float) $coupon->value,
                'discount' => $discount,
                'product_id' => (int) $coupon->product_id,
            ];
        }

        if (empty($refreshedCoupons)) {
            session()->forget('cart_product_coupons');
            return;
        }

        session()->put('cart_product_coupons', $refreshedCoupons);
    }

    private function isCouponValid(Coupon $coupon, float $subtotal): bool
    {
        if (!$coupon->is_active) {
            return false;
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return false;
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return false;
        }

        if (!is_null($coupon->usage_limit) && $coupon->used_count >= $coupon->usage_limit) {
            return false;
        }

        if (!is_null($coupon->min_order_amount) && $subtotal < (float) $coupon->min_order_amount) {
            return false;
        }

        return true;
    }

    private function getCouponErrorMessage(Coupon $coupon, float $subtotal): string
    {
        if (!$coupon->is_active) {
            return 'This coupon is not active.';
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return 'This coupon is not available yet.';
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return 'This coupon has expired.';
        }

        if (!is_null($coupon->usage_limit) && $coupon->used_count >= $coupon->usage_limit) {
            return 'This coupon reached its usage limit.';
        }

        if (!is_null($coupon->min_order_amount) && $subtotal < (float) $coupon->min_order_amount) {
            return 'Minimum order amount for this coupon is ' . number_format((float) $coupon->min_order_amount, 2);
        }

        return 'Invalid coupon code.';
    }

    private function syncCartWithDatabase(array $cart): array
    {
        foreach ($cart as $key => $item) {
            $product = Product::with('primaryImage')->find($item['product_id'] ?? null);

            if (!$product) {
                unset($cart[$key]);
                continue;
            }

            $variant = null;
            $price = (float) $product->base_price;
            $image = $product->primaryImage?->path ?? null;

            $selectedOptions = [
                'color' => null,
                'size' => null,
                'options' => [],
            ];

            if (!empty($item['variant_id'])) {
                $variant = ProductVariant::query()
                    ->with([
                        'values.attribute:id,slug,name,is_active',
                        'values.value:id,attribute_id,value,slug,sort_order,is_active',
                        'images:id,product_id,variant_id,path,alt_text,is_primary,sort_order',
                    ])
                    ->where('id', $item['variant_id'])
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->first();

                if (!$variant) {
                    unset($cart[$key]);
                    continue;
                }

                if (!is_null($variant->price)) {
                    $price = (float) $variant->price;
                }

                $variantImage = $variant->images
                    ? $variant->images->sortBy([
                        ['is_primary', 'desc'],
                        ['sort_order', 'asc'],
                        ['id', 'asc'],
                    ])->first()
                    : null;

                if ($variantImage?->path) {
                    $image = $variantImage->path;
                }

                $selectedOptions = $this->extractVariantOptions($variant);
            }

            $quantity = (int) ($item['quantity'] ?? 1);
            $quantity = max(1, min($quantity, self::MAX_QTY_PER_ITEM));

            if ($variant && isset($variant->stock)) {
                $quantity = min($quantity, (int) $variant->stock);
            }

            if ($quantity < 1) {
                unset($cart[$key]);
                continue;
            }

            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'slug' => $product->slug,
                'name' => $product->name ?? $product->slug,
                'sku' => $variant?->sku ?? $product->sku ?? ('SKU-' . $product->id),
                'price' => $price,
                'quantity' => $quantity,
                'image' => $image,
                'currency' => $product->currency ?? 'SAR',
                'color' => $selectedOptions['color'],
                'size' => $selectedOptions['size'],
                'variant_options' => $selectedOptions['options'],
                'tax_rate' => (float) ($item['tax_rate'] ?? 0),
                'total_price' => $price * $quantity,
            ];
        }

        return $cart;
    }

    private function extractVariantOptions(ProductVariant $variant): array
    {
        $color = null;
        $size = null;
        $options = [];

        foreach ($variant->values ?? [] as $row) {
            $attribute = $row->attribute;
            $value = $row->value;

            if (!$attribute || !$value || !$value->is_active) {
                continue;
            }

            $attributeName = trim((string) ($attribute->name ?? $attribute->slug ?? 'Option'));
            $attributeSlug = strtolower(trim((string) ($attribute->slug ?? '')));
            $valueText = trim((string) ($value->value ?? ''));

            if ($valueText === '') {
                continue;
            }

            $options[] = [
                'attribute_id' => $attribute->id,
                'attribute_value_id' => $value->id,
                'name' => $attributeName,
                'slug' => $attributeSlug,
                'value' => $valueText,
            ];

            if ($this->isColorAttribute($attributeSlug, $attributeName)) {
                $color = $valueText;
            }

            if ($this->isSizeAttribute($attributeSlug, $attributeName)) {
                $size = $valueText;
            }
        }

        return [
            'color' => $color,
            'size' => $size,
            'options' => $options,
        ];
    }

    private function isColorAttribute(string $slug, string $name): bool
    {
        $name = strtolower(trim($name));

        return in_array($slug, ['color', 'colors'], true)
            || in_array($name, ['color', 'colors', 'اللون', 'الألوان'], true);
    }

    private function isSizeAttribute(string $slug, string $name): bool
    {
        $name = strtolower(trim($name));

        return in_array($slug, ['size', 'sizes'], true)
            || in_array($name, ['size', 'sizes', 'المقاس', 'الحجم'], true);
    }


}
