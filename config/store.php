<?php

/*
| Public store details shown in the footer and policy pages. Set them in .env;
| empty social links are hidden instead of pointing nowhere.
*/
return [
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
