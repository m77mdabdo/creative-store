<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Address;
use App\Models\Coupon;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddressSnapshot;
use App\Models\OrderStatusLog;
use App\Models\CouponUsage;

use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ShippingMethod;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        /* =========================
         * Preconditions
         * ========================= */
        if (User::count() === 0) {
            $this->command->warn('No users found. Run AuthSeeder first.');
            return;
        }

        if (Product::count() === 0) {
            $this->command->warn('No products found. Run CatalogSeeder first.');
            return;
        }

        // Shipping method (لازم واحد على الأقل)
        $shippingMethod = ShippingMethod::query()->first()
            ?: ShippingMethod::factory()->create([
                'name' => 'Standard',
                'code' => 'standard',
                'is_active' => true,
            ]);

        // Coupon اختياري
        $coupon = Coupon::query()->inRandomOrder()->first();

        $users = User::query()->inRandomOrder()->take(20)->get();
        $products = Product::query()->inRandomOrder()->take(80)->get();

        /* =========================
         * Create Orders
         * ========================= */
        foreach ($users as $user) {

            // تأكد عنده عنوان واحد على الأقل (علشان snapshot)
            $address = Address::where('user_id', $user->id)->inRandomOrder()->first();
            if (!$address) {
                $address = Address::factory()->create([
                    'user_id' => $user->id,
                    'is_default' => true,
                    'country' => 'Saudi Arabia',
                ]);
            }

            $ordersCount = rand(1, 3);

            for ($o = 0; $o < $ordersCount; $o++) {

                /* -------------------------
                 * Order base
                 * ------------------------- */
                $order = Order::factory()->create([
                    'user_id' => $user->id,
                    'shipping_address_id' => $address->id,
                    'coupon_id' => (rand(1, 100) <= 25 && $coupon) ? $coupon->id : null,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'placed_at' => now()->subDays(rand(0, 20)),
                    'paid_at' => now()->subDays(rand(0, 20)),
                ]);

                /* -------------------------
                 * Order items
                 * ------------------------- */
                $itemsCount = rand(1, 4);

                $subtotal = 0;
                $taxTotal = 0;

                // علشان ما يتكرر نفس المنتج في نفس الأوردر كتير
                $picked = $products->random($itemsCount);

                foreach ($picked as $product) {
                    $qty = rand(1, 3);

                    // لو المنتج عنده variants اختار واحد
                    $variantId = null;
                    if ($product->has_variants) {
                        $variantId = ProductVariant::where('product_id', $product->id)
                            ->where('is_active', true)
                            ->inRandomOrder()
                            ->value('id');
                    }

                    $unitPrice = $variantId
                        ? (ProductVariant::find($variantId)->price ?? $product->base_price)
                        : $product->base_price;

                    $lineBase = $unitPrice * $qty;
                    $taxRate = $product->tax_rate ?? 0;
                    $lineTax = $lineBase * ($taxRate / 100);
                    $lineTotal = $lineBase + $lineTax;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variant_id' => $variantId,
                        'product_name_snapshot' => optional($product->translations()->where('locale','en')->first())->name
                            ?? $product->slug,
                        'sku_snapshot' => $variantId
                            ? (ProductVariant::find($variantId)->sku ?? $product->sku)
                            : $product->sku,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'tax_rate' => $taxRate,
                        'tax_amount' => $lineTax,
                        'line_total' => $lineTotal,
                    ]);

                    $subtotal += $lineBase;
                    $taxTotal += $lineTax;
                }

                /* -------------------------
                 * Discount + totals
                 * ------------------------- */
                $discount = 0;

                if ($order->coupon_id) {
                    $couponModel = Coupon::find($order->coupon_id);
                    if ($couponModel) {
                        if ($couponModel->type === 'percent') {
                            $discount = ($subtotal * ($couponModel->value / 100));
                            if ($couponModel->max_discount_amount) {
                                $discount = min($discount, $couponModel->max_discount_amount);
                            }
                        } else { // fixed
                            $discount = $couponModel->value;
                        }

                        if ($couponModel->min_order_amount && $subtotal < $couponModel->min_order_amount) {
                            $discount = 0; // لا يطبق
                        }
                    }
                }

                $discount = max(0, min($discount, $subtotal));

                $shippingFee = rand(0, 1) ? 0 : rand(15, 45);
                $total = max(0, $subtotal - $discount) + $shippingFee + $taxTotal;

                // Update order totals
                $order->update([
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'shipping_fee' => $shippingFee,
                    'tax_total' => $taxTotal,
                    'total' => $total,
                    'currency' => 'SAR',
                    'exchange_rate' => 1.000000,
                    'payment_method' => 'mada',
                ]);

                /* -------------------------
                 * Order Address Snapshot (unique per order)
                 * ------------------------- */
                OrderAddressSnapshot::firstOrCreate(
                    ['order_id' => $order->id],
                    [
                        'full_name' => $address->full_name,
                        'phone' => $address->phone,
                        'country' => $address->country,
                        'city' => $address->city,
                        'district' => $address->district,
                        'street' => $address->street,
                        'building_no' => $address->building_no,
                        'apartment_no' => $address->apartment_no,
                        'postal_code' => $address->postal_code,
                        'lat' => $address->lat,
                        'lng' => $address->lng,
                        'notes' => $address->notes,
                    ]
                );

                /* -------------------------
                 * Status logs
                 * ------------------------- */
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'from_status' => null,
                    'to_status' => 'confirmed',
                    'changed_by' => $user->id,
                    'note' => 'Seeded order created',
                ]);

                /* -------------------------
                 * Payment
                 * ------------------------- */
                Payment::factory()->create([
                    'order_id' => $order->id,
                    'amount' => $order->total,
                    'currency' => 'SAR',
                    'exchange_rate' => 1.000000,
                    'status' => 'captured',
                    'paid_at' => now(),
                ]);

                /* -------------------------
                 * Shipment
                 * ------------------------- */
                Shipment::factory()->create([
                    'order_id' => $order->id,
                    'shipping_method_id' => $shippingMethod->id,
                    'status' => 'in_transit',
                    'shipped_at' => now()->subDays(1),
                ]);

                /* -------------------------
                 * Coupon Usage (unique coupon_id + order_id)
                 * ------------------------- */
                if ($order->coupon_id && $discount > 0) {
                    CouponUsage::firstOrCreate(
                        [
                            'coupon_id' => $order->coupon_id,
                            'order_id' => $order->id,
                        ],
                        [
                            'user_id' => $order->user_id,
                            'discount_amount' => $discount,
                        ]
                    );

                    // تحديث used_count (اختياري)
                    Coupon::where('id', $order->coupon_id)->increment('used_count');
                }
            }
        }
    }
}
