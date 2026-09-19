<?php

namespace App\Services\Shipping\Carriers;

final class Waybill
{
    public function __construct(
        public readonly string $trackingNumber,
        public readonly ?string $labelPdf = null,
    ) {
    }
}
