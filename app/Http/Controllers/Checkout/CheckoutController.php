<?php

namespace App\Http\Controllers\Checkout;

use App\Exceptions\CheckoutException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\PlaceOrderRequest;
use App\Models\Address;
use App\Models\ShippingRate;
use App\Services\Checkout\CheckoutService;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

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

        // One checkout at a time per customer: a double click or a second tab
        // waits for the first request instead of creating a duplicate order.
        $lock = Cache::lock("checkout:user:{$user->id}", 30);

        try {
            $order = $lock->block(5, fn () => $this->checkout->placeOrder(
                user: $user,
                address: $address,
                rate: $rate,
                shippingMethodId: $request->integer('shipping_method_id'),
                cart: $session->get('cart', []),
                totals: $totals,
                couponId: $session->get('cart_coupon.id'),
                couponDiscounts: $this->couponDiscounts($session->get('cart_coupon'), $session->get('cart_product_coupons', [])),
            ));
        } catch (LockTimeoutException) {
            return back()->with('error', __('Your order is already being processed. Please wait a moment.'));
        } catch (CheckoutException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        // The cart now belongs to the order; a resubmit must not create another one.
        $session->forget(['cart', 'cart_coupon', 'cart_product_coupons', 'checkout_totals']);

        return redirect()->route('checkout.payment', $order);
    }

    /**
     * @return array<int, float>
     */
    private function couponDiscounts(?array $cartCoupon, array $productCoupons): array
    {
        $discounts = [];

        foreach (array_filter([$cartCoupon, ...array_values($productCoupons)]) as $coupon) {
            if (! empty($coupon['id'])) {
                $id = (int) $coupon['id'];
                $discounts[$id] = ($discounts[$id] ?? 0) + (float) ($coupon['discount'] ?? 0);
            }
        }

        return $discounts;
    }
}
