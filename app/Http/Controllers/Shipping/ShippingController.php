<?php

namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\SelectAddressRequest;
use App\Http\Requests\Shipping\SelectShippingMethodRequest;
use App\Models\Address;
use App\Services\Shipping\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
            return redirect()->route('cart.index')->with('error', __('Your cart is empty.'));
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
      session([
    'checkout_totals' => $totals,
     ]);
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
            'message' => __('Address selected successfully.'),
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

    session([
        'checkout_totals' => $totals,
    ]);

    return response()->json([
        'success' => true,
        'message' => __('Shipping method selected successfully.'),
        'totals' => $totals,
    ]);
}
}
