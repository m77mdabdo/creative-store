<?php

namespace Tests\Feature\Cart;

use App\Models\CouponUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class CouponLimitTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    public function test_a_customer_cannot_apply_a_coupon_they_have_used_up(): void
    {
        $user = $this->customer();
        $product = $this->product(100);
        $variant = $this->variant($product);
        $coupon = $this->coupon(['per_user_limit' => 1]);
        CouponUsage::create([
            'coupon_id' => $coupon->id, 'user_id' => $user->id,
            'order_id' => $this->paidOrderFor($user)->id, 'discount_amount' => 10,
        ]);

        $this->actingAs($user)
            ->withSession(['cart' => [$this->cartLine($product, $variant)]])
            ->post(route('cart.coupon'), ['coupon' => $coupon->code])
            ->assertSessionHas('error');

        $this->assertNull(session('cart_coupon'));
    }

    public function test_another_customer_can_still_use_it(): void
    {
        $product = $this->product(100);
        $variant = $this->variant($product);
        $coupon = $this->coupon(['per_user_limit' => 1]);

        $this->actingAs($this->customer())
            ->withSession(['cart' => [$this->cartLine($product, $variant)]])
            ->post(route('cart.coupon'), ['coupon' => $coupon->code])
            ->assertSessionHas('success');
    }
}
