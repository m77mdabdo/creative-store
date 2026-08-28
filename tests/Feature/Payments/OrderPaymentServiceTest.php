<?php

namespace Tests\Feature\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\Moyasar\MoyasarPayment;
use App\Services\Payments\OrderPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class OrderPaymentServiceTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    private function pendingOrder(float $total = 250): Order
    {
        $order = Order::factory()->create([
            'user_id'        => $this->customer()->id,
            'coupon_id'      => null,
            'currency'       => 'SAR',
            'total'          => $total,
            'status'         => 'pending',
            'payment_status' => 'pending',
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'gateway'  => 'moyasar',
            'amount'   => $total,
            'status'   => Payment::STATUS_PENDING,
            'paid_at'  => null,
        ]);

        return $order;
    }

    private function remote(Order $order, string $status = 'paid', ?int $amount = null): MoyasarPayment
    {
        return MoyasarPayment::fromArray([
            'id'       => 'pay_' . $order->id,
            'status'   => $status,
            'amount'   => $amount ?? (int) round($order->total * 100),
            'currency' => 'SAR',
            'metadata' => ['order_id' => (string) $order->id],
        ]);
    }

    public function test_a_successful_payment_confirms_the_order_once(): void
    {
        $order = $this->pendingOrder();
        $service = app(OrderPaymentService::class);

        $this->assertSame(OrderPaymentService::RESULT_PAID, $service->apply($order, $this->remote($order), 'callback'));
        $this->assertSame(OrderPaymentService::RESULT_ALREADY_PAID, $service->apply($order, $this->remote($order), 'webhook'));

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('confirmed', $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame(Payment::STATUS_CAPTURED, $order->payments()->first()->status);
    }

    public function test_an_amount_mismatch_does_not_mark_the_order_paid(): void
    {
        $order = $this->pendingOrder(250);

        $result = app(OrderPaymentService::class)->apply($order, $this->remote($order, amount: 100), 'callback');

        $this->assertSame(OrderPaymentService::RESULT_MISMATCH, $result);
        $this->assertSame('pending', $order->fresh()->payment_status);
    }

    public function test_a_payment_for_a_cancelled_order_is_not_applied(): void
    {
        $order = $this->pendingOrder();
        $order->update(['status' => 'cancelled']);

        $result = app(OrderPaymentService::class)->apply($order, $this->remote($order), 'callback');

        $this->assertSame(OrderPaymentService::RESULT_ORDER_CLOSED, $result);
        $this->assertNotSame('paid', $order->fresh()->payment_status);
    }

    public function test_a_failed_payment_is_recorded_as_failed(): void
    {
        $order = $this->pendingOrder();

        $result = app(OrderPaymentService::class)->apply($order, $this->remote($order, 'failed'), 'callback');

        $this->assertSame(OrderPaymentService::RESULT_FAILED, $result);
        $this->assertSame('failed', $order->fresh()->payment_status);
    }
}
