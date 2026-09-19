<?php

namespace App\Services\Shipping\Carriers;

use App\Models\Shipment;

/**
 * A shipping company that can book a waybill and report tracking events.
 */
interface Carrier
{
    /**
     * Display name stored on the shipment.
     */
    public function name(): string;

    /**
     * Book a waybill for the shipment's order.
     */
    public function book(Shipment $shipment): Waybill;

    /**
     * Tracking events for a waybill, oldest first.
     *
     * @return list<TrackingEvent>
     */
    public function track(Shipment $shipment): array;
}
