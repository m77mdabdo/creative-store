<?php

/*
| Error monitoring with Sentry. Nothing is sent until SENTRY_LARAVEL_DSN
| is set; create a free project at sentry.io (platform: Laravel) and copy
| its DSN into .env on the server.
*/
return [
    'dsn' => env('SENTRY_LARAVEL_DSN'),

    'release' => env('SENTRY_RELEASE'),

    'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV')),

    // Customer emails, IPs and cookies are not sent.
    'send_default_pii' => false,

    // Share of requests traced for performance (0.0 - 1.0).
    'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE') === null ? 0.0 : (float) env('SENTRY_TRACES_SAMPLE_RATE'),

    'ignore_exceptions' => [
        Illuminate\Validation\ValidationException::class,
        Illuminate\Auth\AuthenticationException::class,
        Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        Illuminate\Session\TokenMismatchException::class,
    ],

    'breadcrumbs' => [
        'logs' => true,
        'sql_queries' => true,
        'sql_bindings' => false,
        'queue_info' => true,
        'command_info' => true,
    ],
];
