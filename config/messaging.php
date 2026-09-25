<?php

/*
| Text messages to customers (order confirmed, shipped, delivered).
|
| "log" writes each message to storage/logs/laravel.log instead of sending
| it, which is the safe default for development and demos. Set
| MESSAGING_DRIVER to "unifonic" (SMS) or "whatsapp" (WhatsApp Cloud API)
| with the matching credentials to send for real.
*/
return [
    'driver' => env('MESSAGING_DRIVER', 'log'),

    // Country code added to local numbers such as 05XXXXXXXX.
    'default_country_code' => env('MESSAGING_COUNTRY_CODE', '966'),

    'drivers' => [
        'log' => [
            'class' => App\Services\Messaging\Drivers\LogDriver::class,
        ],

        'unifonic' => [
            'class'     => App\Services\Messaging\Drivers\UnifonicDriver::class,
            'endpoint'  => env('UNIFONIC_ENDPOINT', 'https://el.cloud.unifonic.com/rest/SMS/messages'),
            'app_sid'   => env('UNIFONIC_APP_SID'),
            'sender_id' => env('UNIFONIC_SENDER_ID', 'CreativeStr'),
        ],

        // Business-initiated WhatsApp messages outside the 24-hour customer
        // window must use an approved template; set WHATSAPP_TEMPLATE to its
        // name (one {{1}} body parameter receives the message text).
        'whatsapp' => [
            'class'           => App\Services\Messaging\Drivers\WhatsAppDriver::class,
            'endpoint'        => env('WHATSAPP_ENDPOINT', 'https://graph.facebook.com/v21.0'),
            'token'           => env('WHATSAPP_TOKEN'),
            'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
            'template'        => env('WHATSAPP_TEMPLATE'),
            'template_lang'   => env('WHATSAPP_TEMPLATE_LANG', 'ar'),
        ],
    ],
];
