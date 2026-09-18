<?php

namespace Tests\Unit\Payments;

use App\Models\Payment;
use App\Services\Payments\Moyasar\MoyasarGateway;
use App\Services\Payments\Moyasar\MoyasarPayment;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MoyasarPaymentTest extends TestCase
{
    public function test_it_reads_the_gateway_payload(): void
    {
        $payment = MoyasarPayment::fromArray([
            'id'       => 'pay_123',
            'status'   => 'PAID',
            'amount'   => 15050,
            'currency' => 'sar',
            'metadata' => ['order_id' => '42'],
        ]);

        $this->assertSame('pay_123', $payment->id);
        $this->assertSame(15050, $payment->amount);
        $this->assertSame('SAR', $payment->currency);
        $this->assertSame(42, $payment->orderId);
        $this->assertTrue($payment->isSuccessful());
    }

    #[DataProvider('statusMap')]
    public function test_gateway_statuses_map_to_valid_local_statuses(string $remote, string $local): void
    {
        $this->assertSame($local, MoyasarPayment::fromArray(['status' => $remote])->localStatus());
    }

    public static function statusMap(): array
    {
        return [
            ['paid', Payment::STATUS_CAPTURED],
            ['captured', Payment::STATUS_CAPTURED],
            ['authorized', Payment::STATUS_AUTHORIZED],
            ['refunded', Payment::STATUS_REFUNDED],
            ['failed', Payment::STATUS_FAILED],
            ['voided', Payment::STATUS_FAILED],
            ['initiated', Payment::STATUS_PENDING],
            ['something-new', Payment::STATUS_PENDING],
        ];
    }

    public function test_non_numeric_order_id_is_ignored(): void
    {
        $this->assertNull(MoyasarPayment::fromArray(['metadata' => ['order_id' => '1 OR 1=1']])->orderId);
    }

    public function test_webhook_secret_must_match_exactly(): void
    {
        $gateway = new MoyasarGateway('sk_test', 'top-secret');

        $this->assertTrue($gateway->hasValidWebhookSecret('top-secret'));
        $this->assertFalse($gateway->hasValidWebhookSecret('top-secre'));
        $this->assertFalse($gateway->hasValidWebhookSecret(null));
        $this->assertFalse((new MoyasarGateway('sk_test', null))->hasValidWebhookSecret(''));
    }

    public function test_payment_ids_are_validated_before_building_the_url(): void
    {
        $gateway = new MoyasarGateway('sk_test', 'secret');

        $this->assertTrue($gateway->isValidPaymentId('3b3c1e5a-7f2d-4c1e-9b8a-1a2b3c4d5e6f'));
        $this->assertFalse($gateway->isValidPaymentId('../../v1/invoices'));
        $this->assertFalse($gateway->isValidPaymentId('abc'));
    }

    public function test_it_reports_how_the_customer_paid(): void
    {
        $this->assertSame('mada', MoyasarPayment::fromArray(['source' => ['type' => 'creditcard', 'company' => 'mada']])->method());
        $this->assertSame('applepay', MoyasarPayment::fromArray(['source' => ['type' => 'applepay']])->method());
        $this->assertSame('stcpay', MoyasarPayment::fromArray(['source' => ['type' => 'stcpay']])->method());
        $this->assertNull(MoyasarPayment::fromArray([])->method());
    }
}
