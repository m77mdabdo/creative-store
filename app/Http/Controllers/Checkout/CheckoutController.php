<?php

namespace App\Http\Controllers\Checkout;

use App\Exceptions\CheckoutException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\PlaceOrderRequest;
use App\Models\Address;
use App\Models\ShippingRate;
use App\Services\Checkout\CheckoutService;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkout,
    ) {
    }

    public function placeOrder(PlaceOrderRequest $request): RedirectResponse
    {
        $user = $request->user();
        $session = $request->session();

        $address = Address::query()
            ->whereBelongsTo($user)
            ->find($request->integer('address_id'));

        if (! $address) {
            return back()->with('error', __('Invalid address selected.'));
        }

        $shipping = $session->get('shipping', []);
        $totals = $session->get('checkout_totals');

        $selectionMatches = (int) ($shipping['shipping_method_id'] ?? 0) === $request->integer('shipping_method_id')
            && (int) ($shipping['shipping_rate_id'] ?? 0) === $request->integer('shipping_rate_id');

        if (! $totals || ! $selectionMatches) {
            return back()->with('error', __('Please select your shipping method again.'));
        }

        $rate = ShippingRate::query()->findOrFail($request->integer('shipping_rate_id'));

        try {
            $order = $this->checkout->placeOrder(
                user: $user,
                address: $address,
                rate: $rate,
                shippingMethodId: $request->integer('shipping_method_id'),
                cart: $session->get('cart', []),
                totals: $totals,
                couponId: $session->get('cart_coupon.id'),
            );
        } catch (CheckoutException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        return redirect()->route('checkout.payment', $order);
    }
}
