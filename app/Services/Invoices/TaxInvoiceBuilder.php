<?php

namespace App\Services\Invoices;

use App\Models\Order;
use App\Support\Zatca\ZatcaQr;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Builds the data for a simplified tax invoice (فاتورة ضريبية مبسطة):
 * seller and buyer details, lines with their VAT share, the VAT breakdown
 * and the ZATCA QR code. One builder feeds both the customer download and
 * the dashboard copy so the two can never disagree.
 */
class TaxInvoiceBuilder
{
    public function build(Order $order): array
    {
        $order->loadMissing(['items', 'addressSnapshot', 'shippingMethod', 'user']);

        $legal = config('store.legal');
        $currency = $order->currency ?: 'SAR';
        $issuedAt = $order->paid_at ?? $order->created_at ?? now();

        $total = round((float) $order->total, 2);
        $vat = round((float) $order->tax_total, 2);
        $taxable = round($total - $vat, 2);
        $vatRate = $taxable > 0 ? round($vat / $taxable, 4) : (float) $legal['vat_rate'];

        // Spread the order VAT across lines by their share of the subtotal;
        // the last line takes the rounding remainder so lines add up exactly.
        $subtotal = max((float) $order->subtotal, 0.01);
        $items = $order->items->values();
        $assigned = 0.0;

        $lines = $items->map(function ($item, $index) use ($items, $subtotal, $vat, &$assigned) {
            $gross = round((float) $item->line_total, 2);
            $lineVat = $index === $items->count() - 1
                ? round($vat - $assigned, 2)
                : round($vat * $gross / $subtotal, 2);
            $assigned += $lineVat;

            return (object) [
                'name'       => $item->product_name_snapshot,
                'sku'        => $item->sku_snapshot,
                'options'    => trim(implode(' / ', array_filter([$item->color, $item->size]))),
                'quantity'   => (int) $item->quantity,
                'unit_price' => round((float) $item->unit_price, 2),
                'net'        => $gross,
                'vat'        => $lineVat,
            ];
        });

        $qrPayload = ZatcaQr::payload($legal['name_ar'], $legal['vat_number'], $issuedAt, $total, $vat);

        return [
            'order'     => $order,
            'number'    => 'INV-' . $order->order_number,
            'issuedAt'  => $issuedAt,
            'currency'  => $currency,
            'seller'    => (object) $legal,
            'buyer'     => (object) [
                'name'    => $order->addressSnapshot?->full_name ?? $order->user?->name ?? $order->guest_email,
                'phone'   => $order->addressSnapshot?->phone ?? $order->user?->phone,
                'email'   => $order->user?->email ?? $order->guest_email,
                'address' => collect([
                    $order->addressSnapshot?->street,
                    $order->addressSnapshot?->district,
                    $order->addressSnapshot?->city,
                    $order->addressSnapshot?->country,
                ])->filter()->implode('، '),
            ],
            'lines'     => $lines,
            'subtotal'  => round((float) $order->subtotal, 2),
            'discount'  => round((float) $order->discount, 2),
            'shipping'  => round((float) $order->shipping_fee, 2),
            'taxable'   => $taxable,
            'vat'       => $vat,
            'vatRate'   => $vatRate,
            'total'     => $total,
            'qrPayload' => $qrPayload,
            'qrSvg'     => $this->qrSvg($qrPayload),
        ];
    }

    private function qrSvg(string $payload): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle(170, 1), new SvgImageBackEnd()));

        return 'data:image/svg+xml;base64,' . base64_encode($writer->writeString($payload));
    }
}
