<?php

namespace Tests\Feature\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class PaymentPageTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    private function pendingOrderFor($user): Order
    {
        $order = Order::factory()->create([
            'user_id' => $user->id, 'coupon_id' => null, 'currency' => 'SAR',
            'total' => 230, 'status' => 'pending', 'payment_status' => 'pending',
        ]);
        Payment::factory()->create(['order_id' => $order->id, 'gateway' => 'moyasar', 'amount' => 230, 'status' => 'pending']);

        return $order;
    }

    public function test_the_form_offers_the_configured_methods(): void
    {
        config(['services.moyasar.methods' => ['creditcard', 'applepay', 'stcpay', 'bogus'], 'services.moyasar.publishable_key' => 'pk_test_x']);
        $user = $this->customer();
        $order = $this->pendingOrderFor($user);

        $this->actingAs($user)->get(route('checkout.payment', $order))
            ->assertOk()
            ->assertSee('methods: ["creditcard","applepay","stcpay"]', false)
            ->assertSee('amount: 23000', false)
            ->assertSee("validate_merchant_url", false);
    }

    public function test_paid_or_foreign_orders_cannot_be_paid_again(): void
    {
        $user = $this->customer();
        $order = $this->pendingOrderFor($user);

        $this->actingAs($this->customer())->get(route('checkout.payment', $order))->assertForbidden();

        $order->update(['payment_status' => 'paid', 'status' => 'confirmed']);
        $this->actingAs($user)->get(route('checkout.payment', $order))->assertRedirect();
    }
}
