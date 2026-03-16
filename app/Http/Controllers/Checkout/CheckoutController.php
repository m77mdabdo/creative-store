<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderAddressSnapshot;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\ShippingRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id' => ['required', 'exists:addresses,id'],
            'shipping_method_id' => ['required', 'exists:shipping_methods,id'],
            'shipping_rate_id' => ['required', 'exists:shipping_rates,id'],
            'payment_method' => ['required', 'in:moyasar'],
        ]);

        $user = Auth::user();
        $cart = session()->get('cart', []);

        if (!$user) {
            return back()->with('error', 'You must be logged in first.');
        }

        if (empty($cart)) {
            return back()->with('error', 'Your cart is empty.');
        }

        $address = Address::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return back()->with('error', 'Invalid address selected.');
        }

        $shipping = session()->get('shipping');
        $totals = session()->get('checkout_totals');

        if (!$shipping || !$totals) {
            return back()->with('error', 'Shipping data not found. Please select shipping method again.');
        }

        if (
            (int) ($shipping['shipping_method_id'] ?? 0) !== (int) $request->shipping_method_id ||
            (int) ($shipping['shipping_rate_id'] ?? 0) !== (int) $request->shipping_rate_id
        ) {
            return back()->with('error', 'Shipping selection mismatch. Please reselect shipping method.');
        }

        $shippingRate = ShippingRate::find($request->shipping_rate_id);

        if (!$shippingRate) {
            return back()->with('error', 'Shipping rate not found.');
        }

        DB::beginTransaction();

        try {
            $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'shipping_address_id' => $address->id,
                'shipping_method_id' => $request->shipping_method_id,
                'coupon_id' => session('cart_coupon.id'),
                'guest_email' => null,
                'guest_phone' => null,
                'currency' => 'SAR',
                'exchange_rate' => 1,
                'subtotal' => $totals['subtotal'] ?? 0,
                'discount' => $totals['discount'] ?? 0,
                'shipping_fee' => $totals['shipping_fee'] ?? 0,
                'tax_total' => $totals['tax_total'] ?? 0,
                'total' => $totals['total'] ?? 0,
                'status' => 'pending',
                'shipping_status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'moyasar',
                'estimated_delivery_from' => $shippingRate->eta_min_days ? Carbon::now()->addDays($shippingRate->eta_min_days) : null,
                'estimated_delivery_to' => $shippingRate->eta_max_days ? Carbon::now()->addDays($shippingRate->eta_max_days) : null,
                'placed_at' => now(),
            ]);

            OrderAddressSnapshot::create([
                'order_id' => $order->id,
                'full_name' => $address->full_name,
                'phone' => $address->phone,
                'country' => $address->country,
                'city' => $address->city,
                'district' => $address->district,
                'street' => $address->street,
                'building_no' => $address->building_no ?? null,
                'apartment_no' => $address->apartment_no ?? null,
                'postal_code' => $address->postal_code ?? null,
                'lat' => $address->lat ?? null,
                'lng' => $address->lng ?? null,
                'notes' => $address->notes ?? null,
            ]);

            foreach ($cart as $item) {
                $productId = $item['product_id'] ?? null;

                if (!$productId) {
                    throw new \Exception('Cart item is missing product_id.');
                }

                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $unitPrice = (float) ($item['price'] ?? 0);
                $taxRate = (float) ($item['tax_rate'] ?? 0);

                $lineSubtotal = $quantity * $unitPrice;
                $taxAmount = ($lineSubtotal * $taxRate) / 100;
                $lineTotal = $lineSubtotal + $taxAmount;

                $skuSnapshot = $item['sku'] ?? ('SKU-' . $productId);
                $variantOptions = $item['variant_options'] ?? [];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'variant_id' => $item['variant_id'] ?? null,
                    'color' => $item['color'] ?? null,
                    'size' => $item['size'] ?? null,
                    'variant_options' => !empty($variantOptions) ? $variantOptions : null,
                    'product_name_snapshot' => $item['name'] ?? 'Product',
                    'sku_snapshot' => $skuSnapshot,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'line_total' => $lineTotal,
                ]);
            }

            OrderStatusLog::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => 'pending',
                'changed_by' => $user->id,
                'note' => 'Order created and waiting for online payment.',
            ]);

            Payment::create([
                'order_id' => $order->id,
                'gateway' => 'moyasar',
                'transaction_id' => null,
                'idempotency_key' => (string) Str::uuid(),
                'amount' => $order->total,
                'currency' => $order->currency,
                'exchange_rate' => 1,
                'status' => 'pending',
                'raw_response' => null,
                'paid_at' => null,
            ]);

            DB::commit();

            return redirect()->route('checkout.payment', $order->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }
}
