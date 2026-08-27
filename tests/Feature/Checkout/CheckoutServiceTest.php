<?php

namespace Tests\Feature\Checkout;

use App\Exceptions\CheckoutException;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Checkout\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    private function checkout(): CheckoutService
    {
        return app(CheckoutService::class);
    }

    public function test_placing_an_order_reserves_stock_and_records_a_pending_payment(): void
    {
        $user = $this->customer();
        $product = $this->product(100);
        $variant = $this->variant($product, stock: 5);
        $rate = $this->shippingRate(20);

        $order = $this->checkout()->placeOrder(
            $user,
            $this->address($user),
            $rate,
            $rate->shipping_method_id,
            [$this->cartLine($product, $variant, 2)],
            $this->totals(200),
            null,
        );

        $this->assertSame('pending', $order->status);
        $this->assertSame('200.00', (string) $order->subtotal);
        $this->assertSame('30.00', (string) $order->tax_total);
        $this->assertSame('250.00', (string) $order->total);
        $this->assertSame(3, $variant->fresh()->stock);
        $this->assertCount(1, $order->items);
        $this->assertNotNull($order->addressSnapshot);
        $this->assertSame(Payment::STATUS_PENDING, $order->payments()->first()->status);
    }

    public function test_it_refuses_more_units_than_are_in_stock(): void
    {
        $user = $this->customer();
        $product = $this->product(100);
        $variant = $this->variant($product, stock: 1);
        $rate = $this->shippingRate();

        try {
            $this->checkout()->placeOrder(
                $user, $this->address($user), $rate, $rate->shipping_method_id,
                [$this->cartLine($product, $variant, 2)], $this->totals(200), null,
            );
            $this->fail('Expected a stock error.');
        } catch (CheckoutException) {
            // expected
        }

        $this->assertSame(1, $variant->fresh()->stock);
        $this->assertSame(0, Order::count());
    }

    public function test_it_refuses_a_cart_priced_below_the_current_catalogue_price(): void
    {
        $user = $this->customer();
        $product = $this->product(150);
        $variant = $this->variant($product, stock: 5);
        $rate = $this->shippingRate();

        $this->expectException(CheckoutException::class);

        // The cart still holds the old price of 100.
        $this->checkout()->placeOrder(
            $user, $this->address($user), $rate, $rate->shipping_method_id,
            [$this->cartLine($product, $variant, 1)], $this->totals(100), null,
        );
    }

    public function test_it_refuses_products_that_are_no_longer_on_sale(): void
    {
        $user = $this->customer();
        $product = $this->product(100, ['status' => 'draft']);
        $variant = $this->variant($product);
        $rate = $this->shippingRate();

        $this->expectException(CheckoutException::class);

        $this->checkout()->placeOrder(
            $user, $this->address($user), $rate, $rate->shipping_method_id,
            [$this->cartLine($product, $variant)], $this->totals(100), null,
        );
    }

    public function test_coupon_use_is_recorded_and_its_limit_enforced(): void
    {
        $product = $this->product(100);
        $variant = $this->variant($product, stock: 10);
        $rate = $this->shippingRate();
        $coupon = $this->coupon(['usage_limit' => 1]);

        $first = $this->customer();
        $order = $this->checkout()->placeOrder(
            $first, $this->address($first), $rate, $rate->shipping_method_id,
            [$this->cartLine($product, $variant)], $this->totals(100, 10), $coupon->id, [$coupon->id => 10],
        );

        $this->assertSame(1, $coupon->fresh()->used_count);
        $this->assertSame(1, CouponUsage::where('order_id', $order->id)->count());

        $second = $this->customer();
        $this->expectException(CheckoutException::class);

        $this->checkout()->placeOrder(
            $second, $this->address($second), $rate, $rate->shipping_method_id,
            [$this->cartLine($product, $variant)], $this->totals(100, 10), $coupon->id, [$coupon->id => 10],
        );
    }
}
