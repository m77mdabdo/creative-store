<?php

namespace App\Services\Shipping\Carriers;

use Carbon\CarbonInterface;

final class TrackingEvent
{
    /**
     * @param  string  $status  one of the Shipment::STATUS_* values
     */
    public function __construct(
        public readonly string $status,
        public readonly string $carrierStatus,
        public readonly ?string $location,
        public readonly CarbonInterface $at,
    ) {
    }
}
