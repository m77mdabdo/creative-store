<?php

namespace App\Services\Shipping\Carriers;

use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Simulated carrier for development and demos.
 *
 * Waybill numbers are stable for an order, the label is a real printable
 * PDF, and tracking advances with time since booking (configurable in
 * config/carriers.php), ending in "delivered".
 */
class SandboxCarrier implements Carrier
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function name(): string
    {
        return $this->config['name'] ?? 'Sandbox Carrier';
    }

    public function book(Shipment $shipment): Waybill
    {
        $shipment->loadMissing('order.addressSnapshot', 'order.items');

        $number = 'SBX' . str_pad((string) $shipment->order_id, 6, '0', STR_PAD_LEFT)
            . substr((string) crc32($shipment->order?->order_number ?? (string) $shipment->id), -4);

        $label = Pdf::loadView('shipping.sandbox-label', [
            'shipment'       => $shipment,
            'order'          => $shipment->order,
            'trackingNumber' => $number,
            'carrier'        => $this->name(),
        ])->setPaper([0, 0, 288, 432])->output();

        return new Waybill($number, $label);
    }

    public function track(Shipment $shipment): array
    {
        if (! $shipment->tracking_number || ! $shipment->shipped_at) {
            return [];
        }

        $booked = $shipment->shipped_at;
        $city = $shipment->order?->addressSnapshot?->city ?? 'Riyadh';
        $timeline = $this->config['timeline'] ?? ['in_transit' => 6, 'out_for_delivery' => 24, 'delivered' => 30];

        $steps = [
            [0, Shipment::STATUS_SHIPPED, 'Shipment picked up', 'Riyadh hub'],
            [$timeline['in_transit'], Shipment::STATUS_IN_TRANSIT, 'In transit to destination', 'Riyadh sorting center'],
            [$timeline['out_for_delivery'], Shipment::STATUS_IN_TRANSIT, 'Out for delivery', $city],
            [$timeline['delivered'], Shipment::STATUS_DELIVERED, 'Delivered', $city],
        ];

        $events = [];

        foreach ($steps as [$hours, $status, $label, $location]) {
            $at = $booked->copy()->addHours($hours);

            if ($at->isFuture()) {
                break;
            }

            $events[] = new TrackingEvent($status, $label, $location, $at);
        }

        return $events;
    }
}
