<?php

namespace Tests\Feature\Invoices;

use App\Models\OrderItem;
use App\Services\Invoices\TaxInvoiceBuilder;
use App\Support\Zatca\ZatcaQr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class TaxInvoiceTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    public function test_the_invoice_carries_the_vat_breakdown_and_a_valid_qr(): void
    {
        $user = $this->customer();
        $order = $this->paidOrderFor($user, total: 135, vat: 15);

        foreach ([[60, 1], [20, 2]] as [$price, $qty]) {
            OrderItem::factory()->create([
                'order_id' => $order->id, 'quantity' => $qty, 'unit_price' => $price,
                'line_total' => $price * $qty, 'tax_rate' => 0, 'tax_amount' => 0,
            ]);
        }

        $invoice = app(TaxInvoiceBuilder::class)->build($order->fresh());

        $this->assertSame(120.0, $invoice['taxable']);
        $this->assertSame(15.0, $invoice['vat']);
        $this->assertSame(135.0, $invoice['total']);
        $this->assertEqualsWithDelta(15.0, $invoice['lines']->sum('vat'), 0.001);

        $fields = ZatcaQr::decode($invoice['qrPayload']);
        $this->assertSame(config('store.legal.vat_number'), $fields[2]);
        $this->assertSame('135.00', $fields[4]);
        $this->assertSame('15.00', $fields[5]);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $invoice['qrSvg']);
    }

    public function test_customers_download_only_their_own_invoice(): void
    {
        $owner = $this->customer();
        $order = $this->paidOrderFor($owner);
        OrderItem::factory()->create(['order_id' => $order->id, 'quantity' => 1, 'unit_price' => 100, 'line_total' => 100]);

        $this->actingAs($this->customer())
            ->get(route('shipping.orders.invoice', $order->id))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('shipping.orders.invoice', $order->id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
