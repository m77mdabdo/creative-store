<?php

/*
| Public store details shown in the footer and policy pages. Set them in .env;
| empty social links are hidden instead of pointing nowhere.
*/
return [
    // Shown in the top bar; keep it equal to the free-shipping threshold of your shipping rates.
    'free_shipping_threshold' => (int) env('STORE_FREE_SHIPPING_THRESHOLD', 300),

    'support_email' => env('STORE_SUPPORT_EMAIL'),
    'support_phone' => env('STORE_SUPPORT_PHONE'),

    'social' => array_filter([
        'instagram' => env('STORE_INSTAGRAM_URL'),
        'tiktok'    => env('STORE_TIKTOK_URL'),
        'x'         => env('STORE_X_URL'),
        'facebook'  => env('STORE_FACEBOOK_URL'),
        'snapchat'  => env('STORE_SNAPCHAT_URL'),
    ]),
];
