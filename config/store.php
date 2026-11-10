<?php

/*
| Public store details shown in the footer and policy pages. Set them in .env;
| empty social links are hidden instead of pointing nowhere.
*/
return [
    // Shown in the top bar; keep it equal to the free-shipping threshold of your shipping rates.
    'free_shipping_threshold' => (int) env('STORE_FREE_SHIPPING_THRESHOLD', 300),

    /*
    | Legal seller details printed on tax invoices and policy pages.
    | The defaults are ZATCA-format demo values: replace them with the
    | registered trade name, CR and VAT numbers before going live.
    */
    'legal' => [
        'name_ar'    => env('STORE_LEGAL_NAME_AR') ?: 'مؤسسة كرييتف ستور للتجارة',
        'name_en'    => env('STORE_LEGAL_NAME_EN') ?: 'Creative Store Trading Est.',
        'vat_number' => env('STORE_VAT_NUMBER') ?: '300000000000003',
        'cr_number'  => env('STORE_CR_NUMBER') ?: '1010000000',
        'address_ar' => env('STORE_ADDRESS_AR') ?: 'طريق الملك فهد، حي العليا، الرياض 12214، المملكة العربية السعودية',
        'address_en' => env('STORE_ADDRESS_EN') ?: 'King Fahd Road, Al Olaya, Riyadh 12214, Saudi Arabia',
        'vat_rate'   => (float) (env('STORE_VAT_RATE') ?: 0.15),
    ],

    /*
    | Customers who leave items in their cart are reminded by email and
    | SMS/WhatsApp. Each number is the wait in minutes before the next
    | reminder: the first goes 10 minutes after the last cart change, the
    | next an hour after that, then 2 hours, 5 hours, and so on. Any cart
    | change starts the sequence again; checking out or emptying the cart
    | stops it.
    */
    'cart_reminders' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) (env('CART_REMINDER_MINUTES') ?: '10,60,120,300,720,1440'))
    ))),

    /*
    | Delivery promises shown at checkout, on orders and on the tracking page.
    | Couriers do not deliver on the listed weekdays (0 = Sunday ... 6 =
    | Saturday), so those days are skipped when counting. Same-day and
    | next-day orders placed after the cut-off hour move one day later.
    */
    'delivery' => [
        'timezone'     => env('STORE_TIMEZONE') ?: 'Asia/Riyadh',
        'days_off'     => array_values(array_map('intval', array_filter(
            explode(',', (string) (env('STORE_DELIVERY_DAYS_OFF') ?? '5')),
            fn ($day) => $day !== ''
        ))),
        'cutoff_hour'  => (int) (env('STORE_DELIVERY_CUTOFF_HOUR') ?: 14),
    ],

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
