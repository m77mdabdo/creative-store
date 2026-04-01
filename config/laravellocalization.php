<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Here you may define the list of languages supported by your application.
    | These will be used by the localization package for route prefixes,
    | language switching, and localized URLs.
    |
    */

    'supportedLocales' => [
        'ar' => [
            'name'     => 'Arabic',
            'script'   => 'Arab',
            'native'   => 'العربية',
            'regional' => 'ar_SA',
        ],

        'en' => [
            'name'     => 'English',
            'script'   => 'Latn',
            'native'   => 'English',
            'regional' => 'en_GB',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Use Accept Language Header
    |--------------------------------------------------------------------------
    |
    | If true, the package will detect the browser language on the first visit
    | and redirect the user to the matching localized route.
    |
    | For your project, false is better so the system depends on the selected
    | route/session locale instead of browser preferences.
    |
    */

    'useAcceptLanguageHeader' => false,

    /*
    |--------------------------------------------------------------------------
    | Hide Default Locale In URL
    |--------------------------------------------------------------------------
    |
    | If false:
    |   /ar/dashboard
    |   /en/dashboard
    |
    | If true and default locale is "ar":
    |   /dashboard
    |   /en/dashboard
    |
    | Keeping it false is clearer while building and testing the system.
    |
    */

    'hideDefaultLocaleInURL' => false,

    /*
    |--------------------------------------------------------------------------
    | Locales Order
    |--------------------------------------------------------------------------
    |
    | If you want to control the order of languages in the switcher,
    | define them here.
    |
    */

    'localesOrder' => ['ar', 'en'],

    /*
    |--------------------------------------------------------------------------
    | Locales Mapping
    |--------------------------------------------------------------------------
    |
    | Allows mapping custom URL segments to locale codes.
    | Example:
    | 'localesMapping' => ['en-GB' => 'en']
    |
    */

    'localesMapping' => [],

    /*
    |--------------------------------------------------------------------------
    | UTF-8 Suffix
    |--------------------------------------------------------------------------
    |
    | Locale suffix for LC_TIME and LC_MONETARY.
    | Default is usually ".UTF-8".
    |
    */

    'utf8suffix' => env('LARAVELLOCALIZATION_UTF8SUFFIX', '.UTF-8'),

    /*
    |--------------------------------------------------------------------------
    | Ignored URLs
    |--------------------------------------------------------------------------
    |
    | URLs that should not be processed by the localization package.
    |
    */

    'urlsIgnored' => [
        '/skipped',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored HTTP Methods
    |--------------------------------------------------------------------------
    |
    | These methods will not be localized/redirection-processed.
    |
    */

    'httpMethodsIgnored' => ['POST', 'PUT', 'PATCH', 'DELETE'],
];
