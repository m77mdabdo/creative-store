<?php

/*
| Marketing and analytics tags. Each one loads only when its ID is set, so
| leaving them empty keeps the site tag-free (for example on a local copy).
| With ANALYTICS_DEBUG=true every e-commerce event is also printed to the
| browser console, which is how to test the events without real accounts.
*/
return [
    'ga4_id'        => env('GA4_MEASUREMENT_ID'),       // G-XXXXXXXXXX
    'meta_pixel_id' => env('META_PIXEL_ID'),            // 15-16 digits
    'snap_pixel_id' => env('SNAP_PIXEL_ID'),            // UUID
    'tiktok_pixel_id' => env('TIKTOK_PIXEL_ID'),
    'debug'         => (bool) env('ANALYTICS_DEBUG', env('APP_DEBUG', false)),
];
