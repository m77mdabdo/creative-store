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
