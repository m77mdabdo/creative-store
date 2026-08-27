<?php

namespace Tests\Concerns;

use App\Models\Address;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use App\Models\User;

/**
 * Small, deterministic shop fixtures for feature tests.
 */
trait BuildsShop
{
    protected function customer(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function product(float $price = 100, array $attributes = []): Product
    {
        return Product::factory()->create(array_merge([
            'category_id'  => Category::factory(),
            'base_price'   => $price,
            'status'       => 'active',
            'has_variants' => true,
            'tax_rate'     => 0,
        ], $attributes));
    }

    protected function variant(Product $product, int $stock = 5, ?float $price = null): ProductVariant
    {
        return ProductVariant::factory()->create([
            'product_id' => $product->id,
            'price'      => $price,
            'stock'      => $stock,
            'is_active'  => true,
        ]);
    }

    protected function address(User $user): Address
    {
        return Address::factory()->create([
            'user_id'     => $user->id,
            'city'        => 'Riyadh',
            'district'    => 'Al Olaya',
            'street'      => 'King Fahd Road',
            'country'     => 'Saudi Arabia',
            'postal_code' => '12214',
        ]);
    }

    protected function shippingRate(float $price = 20): ShippingRate
    {
        return ShippingRate::factory()->create([
            'shipping_zone_id'   => ShippingZone::factory(),
            'shipping_method_id' => ShippingMethod::factory(),
            'city'               => 'Riyadh',
            'district'           => null,
            'min_weight'         => null,
            'max_weight'         => null,
            'price'              => $price,
            'eta_min_days'       => 1,
            'eta_max_days'       => 3,
            'free_shipping_threshold' => null,
        ]);
    }

    protected function coupon(array $attributes = []): Coupon
    {
        return Coupon::factory()->create(array_merge([
            'type'                => 'fixed',
            'value'               => 10,
            'min_order_amount'    => null,
            'max_discount_amount' => null,
            'usage_limit'         => null,
            'per_user_limit'      => null,
            'used_count'          => 0,
            'starts_at'           => null,
            'expires_at'          => null,
            'is_active'           => true,
        ], $attributes));
    }

    /**
     * One cart line in the shape the session cart stores.
     */
    protected function cartLine(Product $product, ProductVariant $variant, int $quantity = 1): array
    {
        return [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity'   => $quantity,
            'name'       => 'Test product',
            'price'      => (float) ($variant->price ?? $product->base_price),
        ];
    }

    /**
     * Totals as the cart and shipping steps compute them (VAT 15% on goods).
     */
    protected function totals(float $subtotal, float $discount = 0, float $shipping = 20): array
    {
        $net = round($subtotal - $discount, 2);
        $vat = round($net * 0.15, 2);

        return [
            'subtotal'     => $subtotal,
            'discount'     => $discount,
            'shipping_fee' => $shipping,
            'tax_total'    => $vat,
            'total'        => round($net + $vat + $shipping, 2),
        ];
    }

    protected function paidOrderFor(User $user, float $total = 135, float $vat = 15): Order
    {
        return Order::factory()->create([
            'user_id'        => $user->id,
            'coupon_id'      => null,
            'subtotal'       => 100,
            'discount'       => 0,
            'shipping_fee'   => 20,
            'tax_total'      => $vat,
            'total'          => $total,
            'status'         => 'confirmed',
            'payment_status' => 'paid',
            'paid_at'        => now(),
        ]);
    }
}
