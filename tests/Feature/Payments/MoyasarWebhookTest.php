<?php

namespace Tests\Feature\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class MoyasarWebhookTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        config(['services.moyasar.webhook_secret' => 'test-secret', 'services.moyasar.secret_key' => 'sk_test_x']);
    }

    private function payload(Order $order, string $secret = 'test-secret'): array
    {
        return [
            'id'           => 'evt_' . $order->id,
            'type'         => 'payment_paid',
            'secret_token' => $secret,
            'data'         => [
                'id'       => 'pay_' . $order->id,
                'status'   => 'paid',
                'amount'   => (int) round($order->total * 100),
                'currency' => 'SAR',
                'metadata' => ['order_id' => (string) $order->id],
            ],
        ];
    }

    private function pendingOrder(): Order
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer()->id, 'coupon_id' => null, 'currency' => 'SAR',
            'total' => 115, 'status' => 'pending', 'payment_status' => 'pending',
        ]);

        Payment::factory()->create(['order_id' => $order->id, 'gateway' => 'moyasar', 'amount' => 115, 'status' => 'pending', 'paid_at' => null]);

        return $order;
    }

    public function test_a_wrong_secret_is_rejected(): void
    {
        $order = $this->pendingOrder();

        $this->postJson(route('webhooks.moyasar'), $this->payload($order, 'wrong'))->assertForbidden();

        $this->assertSame('pending', $order->fresh()->payment_status);
    }

    public function test_a_paid_event_confirms_the_order_and_redelivery_is_ignored(): void
    {
        $order = $this->pendingOrder();

        $this->postJson(route('webhooks.moyasar'), $this->payload($order))->assertOk();
        $this->postJson(route('webhooks.moyasar'), $this->payload($order))->assertOk()->assertJson(['message' => 'already processed']);

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame(1, PaymentWebhookLog::count());
    }
}
