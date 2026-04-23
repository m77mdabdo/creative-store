<?php

namespace App\Services\Checkout;

use App\Exceptions\CheckoutException;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderAddressSnapshot;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    private const CURRENCY = 'SAR';

    private const PRICE_TOLERANCE = 0.01;

    /**
     * Create a pending order from the session cart.
     *
     * Prices are re-read and stock is locked inside the transaction, so the
     * order can never be created for less than the current catalogue price
     * or for more units than are in stock.
     *
     * @param  array<string, array<string, mixed>>  $cart
     * @param  array<string, float>  $totals
     */
    public function placeOrder(
        User $user,
        Address $address,
        ShippingRate $rate,
        int $shippingMethodId,
        array $cart,
        array $totals,
        ?int $couponId,
    ): Order {
        if ($cart === []) {
            throw new CheckoutException(__('Your cart is empty.'));
        }

        return DB::transaction(function () use ($user, $address, $rate, $shippingMethodId, $cart, $totals, $couponId) {
            $lines = $this->priceLines($cart);

            $subtotal = round(array_sum(array_column($lines, 'line_subtotal')), 2);

            if (abs($subtotal - (float) ($totals['subtotal'] ?? 0)) > self::PRICE_TOLERANCE) {
                throw new CheckoutException(__('Some prices have changed. Please review your cart before paying.'));
            }

            $discount = min((float) ($totals['discount'] ?? 0), $subtotal);
            $shippingFee = (float) ($totals['shipping_fee'] ?? 0);
            $taxTotal = (float) ($totals['tax_total'] ?? 0);
            $total = round($subtotal - $discount + $taxTotal + $shippingFee, 2);

            $order = Order::create([
                'order_number'            => $this->generateOrderNumber(),
                'user_id'                 => $user->id,
                'shipping_address_id'     => $address->id,
                'shipping_method_id'      => $shippingMethodId,
                'coupon_id'               => $couponId,
                'currency'                => self::CURRENCY,
                'exchange_rate'           => 1,
                'subtotal'                => $subtotal,
                'discount'                => round($discount, 2),
                'shipping_fee'            => round($shippingFee, 2),
                'tax_total'               => round($taxTotal, 2),
                'total'                   => $total,
                'status'                  => 'pending',
                'shipping_status'         => 'pending',
                'payment_status'          => 'pending',
                'payment_method'          => 'moyasar',
                'estimated_delivery_from' => $rate->eta_min_days ? now()->addDays($rate->eta_min_days) : null,
                'estimated_delivery_to'   => $rate->eta_max_days ? now()->addDays($rate->eta_max_days) : null,
                'placed_at'               => now(),
            ]);

            $this->snapshotAddress($order, $address);

            foreach ($lines as $line) {
                $line['variant']?->decrement('stock', $line['quantity']);

                OrderItem::create([
                    'order_id'              => $order->id,
                    'product_id'            => $line['product']->id,
                    'variant_id'            => $line['variant']?->id,
                    'color'                 => $line['item']['color'] ?? null,
                    'size'                  => $line['item']['size'] ?? null,
                    'variant_options'       => ($line['item']['variant_options'] ?? []) ?: null,
                    'product_name_snapshot' => $line['item']['name'] ?? $line['product']->slug,
                    'sku_snapshot'          => $line['variant']?->sku ?? $line['item']['sku'] ?? ('SKU-' . $line['product']->id),
                    'quantity'              => $line['quantity'],
                    'unit_price'            => $line['unit_price'],
                    'tax_rate'              => 0,
                    'tax_amount'            => 0,
                    'line_total'            => $line['line_subtotal'],
                ]);
            }

            OrderStatusLog::create([
                'order_id'    => $order->id,
                'from_status' => null,
                'to_status'   => 'pending',
                'changed_by'  => $user->id,
                'note'        => 'Order created and waiting for online payment.',
            ]);

            Payment::create([
                'order_id'        => $order->id,
                'gateway'         => 'moyasar',
                'idempotency_key' => (string) Str::uuid(),
                'amount'          => $order->total,
                'currency'        => $order->currency,
                'exchange_rate'   => 1,
                'status'          => Payment::STATUS_PENDING,
            ]);

            return $order;
        });
    }

    /**
     * @return list<array{item: array, product: Product, variant: ?ProductVariant, quantity: int, unit_price: float, line_subtotal: float}>
     */
    private function priceLines(array $cart): array
    {
        $productIds = collect($cart)->pluck('product_id')->filter()->unique()->all();
        $variantIds = collect($cart)->pluck('variant_id')->filter()->unique()->all();

        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        $variants = ProductVariant::query()
            ->whereIn('id', $variantIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $lines = [];

        foreach ($cart as $item) {
            $product = $products->get($item['product_id'] ?? null);

            if (! $product || $product->status !== 'active') {
                throw new CheckoutException(__('A product in your cart is no longer available.'));
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $variant = null;
            $unitPrice = (float) $product->base_price;

            if (! empty($item['variant_id'])) {
                $variant = $variants->get($item['variant_id']);

                if (! $variant || (int) $variant->product_id !== (int) $product->id || ! $variant->is_active) {
                    throw new CheckoutException(__('A selected product option is no longer available.'));
                }

                if ((int) $variant->stock < $quantity) {
                    throw new CheckoutException(__('Not enough stock for :sku.', ['sku' => $variant->sku]));
                }

                if (! is_null($variant->price)) {
                    $unitPrice = (float) $variant->price;
                }
            }

            $lines[] = [
                'item'          => $item,
                'product'       => $product,
                'variant'       => $variant,
                'quantity'      => $quantity,
                'unit_price'    => round($unitPrice, 2),
                'line_subtotal' => round($unitPrice * $quantity, 2),
            ];
        }

        return $lines;
    }

    private function snapshotAddress(Order $order, Address $address): void
    {
        OrderAddressSnapshot::create([
            'order_id'     => $order->id,
            'full_name'    => $address->full_name,
            'phone'        => $address->phone,
            'country'      => $address->country,
            'city'         => $address->city,
            'district'     => $address->district,
            'street'       => $address->street,
            'building_no'  => $address->building_no,
            'apartment_no' => $address->apartment_no,
            'postal_code'  => $address->postal_code,
            'lat'          => $address->lat,
            'lng'          => $address->lng,
            'notes'        => $address->notes,
        ]);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . now()->format('ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
