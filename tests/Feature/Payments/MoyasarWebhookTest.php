<?php

namespace Tests\Feature\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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
                'id'       => $this->paymentId($order),
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

    private function paymentId(Order $order): string
    {
        return sprintf('7f3c2a10-0000-4000-8000-%012d', $order->id);
    }

    private function gatewayReturns(Order $order, string $status, ?int $amount = null): void
    {
        Http::fake(['api.moyasar.com/*' => Http::response([
            'id'       => $this->paymentId($order),
            'status'   => $status,
            'amount'   => $amount ?? (int) round($order->total * 100),
            'currency' => 'SAR',
            'metadata' => ['order_id' => (string) $order->id],
        ])]);
    }

    public function test_a_paid_event_confirms_the_order_and_redelivery_is_ignored(): void
    {
        $order = $this->pendingOrder();
        $this->gatewayReturns($order, 'paid');

        $this->postJson(route('webhooks.moyasar'), $this->payload($order))->assertOk();
        $this->postJson(route('webhooks.moyasar'), $this->payload($order))->assertOk()->assertJson(['message' => 'already processed']);

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame(1, PaymentWebhookLog::count());
    }

    public function test_a_paid_body_is_not_trusted_when_moyasar_says_otherwise(): void
    {
        $order = $this->pendingOrder();
        $this->gatewayReturns($order, 'failed');

        $this->postJson(route('webhooks.moyasar'), $this->payload($order))->assertOk();

        $this->assertNotSame('paid', $order->fresh()->payment_status);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/payments/' . $this->paymentId($order)));
    }

    public function test_the_amount_comes_from_moyasar_not_from_the_body(): void
    {
        $order = $this->pendingOrder();
        $this->gatewayReturns($order, 'paid', 100);

        $this->postJson(route('webhooks.moyasar'), $this->payload($order))->assertOk();

        $this->assertNotSame('paid', $order->fresh()->payment_status);
    }

    public function test_the_event_is_retried_when_moyasar_cannot_be_reached(): void
    {
        $order = $this->pendingOrder();
        Http::fake(['api.moyasar.com/*' => Http::response([], 500)]);

        $this->postJson(route('webhooks.moyasar'), $this->payload($order))->assertStatus(503);

        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->assertFalse((bool) PaymentWebhookLog::first()->processed);
    }
}
