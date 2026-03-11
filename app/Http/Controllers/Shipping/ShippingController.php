<?php

namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\PlaceOrderRequest;
use App\Http\Requests\Shipping\SelectAddressRequest;
use App\Http\Requests\Shipping\SelectShippingMethodRequest;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderAddressSnapshot;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\ShippingRate;
use App\Models\Shipment;
use App\Services\Shipping\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ShippingController extends Controller
{
    public function __construct(
        protected ShippingService $shippingService
    ) {}

    public function index(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $addresses = Address::where('user_id', auth()->id())->latest()->get();
       $shipping = session('shipping', []);
       $shippingFee = (float) ($shipping['shipping_fee'] ?? 0);

// ده summary اللي جاي من صفحة الكارت
      $cartSummary = session('cart_summary', [
    'subtotal' => 0,
    'discount' => 0,
    'tax_total' => 0,
    'total' => 0,
]);

      $totals = $this->shippingService->orderTotalsFromCartSummary($cartSummary, $shippingFee);
        return view('site.shipping.index', compact('cart', 'addresses', 'shipping', 'totals'));
    }

    public function selectAddress(SelectAddressRequest $request): JsonResponse
    {
        $address = Address::where('user_id', auth()->id())
            ->findOrFail($request->address_id);

        session([
            'shipping.address_id' => $address->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address selected successfully.',
        ]);
    }

    public function shippingOptions(SelectAddressRequest $request): JsonResponse
    {
        $address = Address::where('user_id', auth()->id())
            ->findOrFail($request->address_id);

        $cart = session()->get('cart', []);
        $options = $this->shippingService->availableOptions($address, $cart);

        session([
            'shipping.address_id' => $address->id,
        ]);

        return response()->json([
            'success' => true,
            'options' => $options,
        ]);
    }

    public function selectMethod(SelectShippingMethodRequest $request): JsonResponse
    {
        $cart = session()->get('cart', []);
        $addressId = session('shipping.address_id');

        abort_unless($addressId, 422, 'Please select address first.');

        $address = Address::where('user_id', auth()->id())->findOrFail($addressId);

        $options = collect($this->shippingService->availableOptions($address, $cart));

        $selected = $options->first(function ($option) use ($request) {
            return (int) $option['rate_id'] === (int) $request->shipping_rate_id
                && (int) $option['shipping_method_id'] === (int) $request->shipping_method_id;
        });

        abort_unless($selected, 422, 'Invalid shipping option selected.');

        session([
            'shipping.shipping_method_id' => (int) $selected['shipping_method_id'],
            'shipping.shipping_rate_id' => (int) $selected['rate_id'],
            'shipping.shipping_fee' => (float) $selected['price'],
            'shipping.shipping_label' => $selected['method_name'],
            'shipping.eta_min_days' => $selected['eta_min_days'],
            'shipping.eta_max_days' => $selected['eta_max_days'],
        ]);

       $cartSummary = session('cart_summary', [
    'subtotal' => 0,
    'discount' => 0,
    'tax_total' => 0,
    'total' => 0,
]);

$totals = $this->shippingService->orderTotalsFromCartSummary(
    $cartSummary,
    (float) $selected['price']
);
        return response()->json([
            'success' => true,
            'message' => 'Shipping method selected successfully.',
            'totals' => $totals,
        ]);
    }

    public function placeOrder(PlaceOrderRequest $request): RedirectResponse
    {
        $cart = session()->get('cart', []);
        abort_if(empty($cart), 422, 'Cart is empty.');

        $address = Address::where('user_id', auth()->id())
            ->findOrFail($request->address_id);

        $rate = ShippingRate::with('method')->findOrFail($request->shipping_rate_id);

        $options = collect($this->shippingService->availableOptions($address, $cart));

        $selected = $options->first(function ($option) use ($request) {
            return (int) $option['rate_id'] === (int) $request->shipping_rate_id
                && (int) $option['shipping_method_id'] === (int) $request->shipping_method_id;
        });

        abort_unless($selected, 422, 'Selected shipping option is not valid.');

      $cartSummary = session('cart_summary', [
    'subtotal' => 0,
    'discount' => 0,
    'tax_total' => 0,
    'total' => 0,
]);

$totals = $this->shippingService->orderTotalsFromCartSummary(
    $cartSummary,
    (float) $selected['price']
);
        DB::transaction(function () use ($request, $address, $selected, $totals, $cart, $rate) {
            $order = Order::create([
                'order_number' => 'ORD-' . now()->format('YmdHis') . rand(100, 999),
                'user_id' => auth()->id(),
                'shipping_address_id' => $address->id,
                'shipping_method_id' => $selected['shipping_method_id'],
                'guest_email' => null,
                'guest_phone' => null,
                'currency' => 'SAR',
                'exchange_rate' => 1,
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'shipping_fee' => $totals['shipping_fee'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
                'status' => 'pending',
                'shipping_status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method ?? 'cash_on_delivery',
                'placed_at' => now(),
                'estimated_delivery_from' => now()->addDays((int) ($selected['eta_min_days'] ?? 0)),
                'estimated_delivery_to' => now()->addDays((int) ($selected['eta_max_days'] ?? 0)),
            ]);

            OrderAddressSnapshot::create([
                'order_id' => $order->id,
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
            ]);

            foreach ($cart as $item) {
                $quantity = (int) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['price'] ?? 0);
                $lineBase = $unitPrice * $quantity;
                $taxRate = 15;
                $taxAmount = round($lineBase * 0.15, 2);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name_snapshot' => $item['name'] ?? 'Product',
                    'sku_snapshot' => $item['sku'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'line_total' => round($lineBase + $taxAmount, 2),
                ]);
            }

            Shipment::create([
                'order_id' => $order->id,
                'shipping_method_id' => $selected['shipping_method_id'],
                'carrier' => $rate->method->name ?? null,
                'tracking_number' => null,
                'status' => 'pending',
                'current_location' => null,
                'carrier_status' => null,
                'notes' => null,
            ]);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => 'pending',
                'changed_by' => auth()->id(),
                'note' => 'Order placed by customer',
            ]);

            session()->forget('cart');
            session()->forget('shipping');
            session()->flash('last_order_id', $order->id);
        });

        return redirect()->route('shipping.orders.show', session('last_order_id'))
            ->with('success', 'Order placed successfully.');
    }
}
