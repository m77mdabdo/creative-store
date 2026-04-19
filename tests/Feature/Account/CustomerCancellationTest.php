<?php

namespace Tests\Feature\Account;

use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class CustomerCancellationTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    public function test_cancelling_a_discounted_order_refunds_what_was_paid(): void
    {
        Bus::fake();
        Http::fake(['api.moyasar.com/*' => Http::response(['status' => 'refunded'], 200)]);

        $user = $this->customer();
        // 100 goods - 10 discount + 13.50 VAT + 20 delivery
        $order = $this->paidOrderFor($user, total: 123.50, vat: 13.50);
        $order->forceFill(['discount' => 10, 'shipping_status' => 'pending'])->save();
        OrderItem::factory()->create(['order_id' => $order->id, 'variant_id' => null, 'quantity' => 1, 'unit_price' => 100, 'line_total' => 100]);
        Payment::factory()->create([
            'order_id' => $order->id, 'gateway' => 'moyasar', 'transaction_id' => 'pay_test_123',
            'amount' => 123.50, 'status' => 'paid',
        ]);

        $this->actingAs($user)->post(route('shipping.orders.cancel', $order->id))->assertSessionMissing('error');

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertEquals(123.50, (float) Refund::query()->sole()->amount);
    }
}
