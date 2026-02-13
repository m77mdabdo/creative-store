<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Services\Checkout\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class PlaceOrderTotalsTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    public function test_a_session_discount_without_a_redeemed_coupon_is_ignored(): void
    {
        $user = $this->customer();
        $product = $this->product(100);
        $variant = $this->variant($product);
        $rate = $this->shippingRate(20);

        $order = app(CheckoutService::class)->placeOrder(
            $user, $this->address($user), $rate, $rate->shipping_method_id,
            [$this->cartLine($product, $variant)], $this->totals(100, 90), null,
        );

        $this->assertSame('0.00', (string) $order->discount);
        $this->assertSame('15.00', (string) $order->tax_total);
        $this->assertSame('135.00', (string) $order->total);
    }

    public function test_tax_is_worked_out_again_instead_of_taken_from_the_session(): void
    {
        $user = $this->customer();
        $product = $this->product(100);
        $variant = $this->variant($product);
        $rate = $this->shippingRate(20);
        $totals = array_merge($this->totals(100), ['tax_total' => 0]);

        $order = app(CheckoutService::class)->placeOrder(
            $user, $this->address($user), $rate, $rate->shipping_method_id,
            [$this->cartLine($product, $variant)], $totals, null,
        );

        $this->assertSame('15.00', (string) $order->tax_total);
    }

    public function test_the_delivery_fee_is_priced_again_for_the_address_being_used(): void
    {
        $user = $this->customer();
        $product = $this->product(100);
        $variant = $this->variant($product);
        $rate = $this->shippingRate(45);
        $address = $this->address($user);

        $this->actingAs($user)
            ->withSession([
                'cart'            => [$this->cartLine($product, $variant)],
                'checkout_totals' => $this->totals(100, 0, 0),
                'shipping'        => [
                    'address_id'         => $address->id,
                    'shipping_method_id' => $rate->shipping_method_id,
                    'shipping_rate_id'   => $rate->id,
                    'shipping_fee'       => 0,
                ],
            ])
            ->post(route('shipping.place-order'), [
                'address_id'         => $address->id,
                'shipping_method_id' => $rate->shipping_method_id,
                'shipping_rate_id'   => $rate->id,
                'payment_method'     => 'moyasar',
            ])
            ->assertRedirect();

        $order = Order::query()->sole();
        $this->assertSame('45.00', (string) $order->shipping_fee);
        $this->assertSame('160.00', (string) $order->total);
    }

    public function test_an_order_cannot_use_a_different_address_than_the_one_priced(): void
    {
        $user = $this->customer();
        $product = $this->product(100);
        $variant = $this->variant($product);
        $rate = $this->shippingRate(20);
        $priced = $this->address($user);
        $other = $this->address($user);

        $this->actingAs($user)
            ->withSession([
                'cart'            => [$this->cartLine($product, $variant)],
                'checkout_totals' => $this->totals(100),
                'shipping'        => [
                    'address_id'         => $priced->id,
                    'shipping_method_id' => $rate->shipping_method_id,
                    'shipping_rate_id'   => $rate->id,
                ],
            ])
            ->post(route('shipping.place-order'), [
                'address_id'         => $other->id,
                'shipping_method_id' => $rate->shipping_method_id,
                'shipping_rate_id'   => $rate->id,
                'payment_method'     => 'moyasar',
            ])
            ->assertSessionHas('error');

        $this->assertSame(0, Order::query()->count());
    }
}
