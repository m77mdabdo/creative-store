<?php

/*
| Shipping carrier used to book waybills and pull tracking updates.
|
| "sandbox" simulates a carrier (waybill numbers, a printable label and a
| realistic tracking timeline) so the whole flow can be tested without an
| account. To go live, add a driver class implementing
| App\Services\Shipping\Carriers\Carrier (SMSA, Aramex, ...) to "drivers"
| and set SHIPPING_CARRIER to its key.
*/
return [
    'default' => env('SHIPPING_CARRIER', 'sandbox'),

    'drivers' => [
        'sandbox' => [
            'class' => App\Services\Shipping\Carriers\SandboxCarrier::class,
            'name' => 'Creative Express (sandbox)',
            // Hours after booking at which the simulated parcel moves on.
            'timeline' => ['in_transit' => 6, 'out_for_delivery' => 24, 'delivered' => 30],
        ],
    ],

    // Shipments still moving are re-checked this often by shipments:sync.
    'sync_every_minutes' => (int) env('SHIPPING_SYNC_MINUTES', 30),
];
