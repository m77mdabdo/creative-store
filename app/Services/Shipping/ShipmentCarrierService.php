<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Shipment;
use App\Models\ShipmentTrackingLog;
use App\Notifications\ShipmentStatusNotification;
use App\Services\Shipping\Carriers\Carrier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

/**
 * Books waybills with the configured carrier and applies its tracking
 * events to shipments and orders.
 */
class ShipmentCarrierService
{
    public function carrier(?string $key = null): Carrier
    {
        $key ??= config('carriers.default');
        $config = config("carriers.drivers.{$key}");

        if (! $config || ! class_exists($config['class'] ?? '')) {
            throw new InvalidArgumentException("Unknown shipping carrier [{$key}].");
        }

        return app()->make($config['class'], ['config' => $config]);
    }

    /**
     * Book a waybill, store its label and mark the shipment as shipped.
     */
    public function book(Shipment $shipment): Shipment
    {
        if ($shipment->tracking_number) {
            throw new RuntimeException(__('This shipment already has a waybill.'));
        }

        if (in_array($shipment->order?->status, ['cancelled'], true)) {
            throw new RuntimeException(__('Cannot manage shipment for a cancelled order.'));
        }

        $carrier = $this->carrier();
        $waybill = $carrier->book($shipment);

        if ($waybill->labelPdf) {
            Storage::disk('local')->put($this->labelPath($waybill->trackingNumber), $waybill->labelPdf);
        }

        DB::transaction(function () use ($shipment, $carrier, $waybill) {
            $shipment->update([
                'carrier'         => $carrier->name(),
                'tracking_number' => $waybill->trackingNumber,
                'status'          => Shipment::STATUS_SHIPPED,
                'carrier_status'  => 'Waybill created',
                'shipped_at'      => now(),
            ]);

            $this->log($shipment, Shipment::STATUS_SHIPPED, 'Waybill created', null, now());
            $this->moveOrder($shipment->order, 'shipped');
        });

        $shipment = $shipment->fresh();
        $shipment->order?->user?->notify(new ShipmentStatusNotification($shipment, 'shipped'));

        return $shipment;
    }

    /**
     * Pull new tracking events from the carrier. Returns how many were new.
     */
    public function sync(Shipment $shipment): int
    {
        if (! $shipment->tracking_number || $shipment->status === Shipment::STATUS_DELIVERED) {
            return 0;
        }

        $events = $this->carrier()->track($shipment);
        $known = $shipment->trackingLogs()->pluck('carrier_status')->all();
        $new = 0;
        $delivered = false;

        DB::transaction(function () use ($shipment, $events, $known, &$new, &$delivered) {
            foreach ($events as $event) {
                if (in_array($event->carrierStatus, $known, true)) {
                    continue;
                }

                $this->log($shipment, $event->status, $event->carrierStatus, $event->location, $event->at);
                $new++;

                $shipment->forceFill([
                    'status'           => $event->status,
                    'carrier_status'   => $event->carrierStatus,
                    'current_location' => $event->location,
                    'delivered_at'     => $event->status === Shipment::STATUS_DELIVERED ? $event->at : $shipment->delivered_at,
                ])->save();

                if ($event->status === Shipment::STATUS_DELIVERED) {
                    $this->moveOrder($shipment->order, 'delivered');
                    $delivered = true;
                }
            }
        });

        if ($delivered) {
            $shipment->order?->user?->notify(new ShipmentStatusNotification($shipment, 'delivered'));
        }

        return $new;
    }

    public function labelPath(string $trackingNumber): string
    {
        return 'shipping-labels/' . preg_replace('/[^A-Za-z0-9_-]/', '', $trackingNumber) . '.pdf';
    }

    private function log(Shipment $shipment, string $status, string $carrierStatus, ?string $location, $at): void
    {
        ShipmentTrackingLog::create([
            'shipment_id'    => $shipment->id,
            'status'         => $status,
            'location'       => $location,
            'carrier_status' => $carrierStatus,
            'tracked_at'     => $at,
        ]);
    }

    private function moveOrder(?Order $order, string $shippingStatus): void
    {
        if (! $order || $order->status === 'cancelled') {
            return;
        }

        $from = $order->status;
        $to = $shippingStatus === 'delivered' ? 'delivered' : ($from === 'delivered' ? $from : 'shipped');

        $order->update(['shipping_status' => $shippingStatus, 'status' => $to]);

        if ($from !== $to) {
            OrderStatusLog::create([
                'order_id'    => $order->id,
                'from_status' => $from,
                'to_status'   => $to,
                'changed_by'  => auth()->id(),
                'note'        => 'Updated from carrier tracking.',
            ]);
        }
    }
}
