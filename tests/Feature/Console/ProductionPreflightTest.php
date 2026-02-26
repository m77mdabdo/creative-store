<?php

namespace Tests\Feature\Console;

use Tests\TestCase;

class ProductionPreflightTest extends TestCase
{
    public function test_it_fails_with_debug_settings(): void
    {
        config(['app.debug' => true, 'app.url' => 'http://localhost']);

        $this->artisan('app:preflight')
            ->expectsOutputToContain('FAIL')
            ->assertExitCode(1);
    }

    public function test_it_passes_with_safe_production_settings(): void
    {
        $this->app['env'] = 'production';
        config([
            'app.debug'                       => false,
            'app.url'                         => 'https://store.example',
            'app.key'                         => 'base64:' . base64_encode(random_bytes(32)),
            'session.secure'                  => true,
            'logging.default'                 => 'daily',
            'logging.channels.daily.level'    => 'warning',
            'services.moyasar.secret_key'     => 'sk_live_example',
            'services.moyasar.webhook_secret' => 'whsec_example',
        ]);

        $this->artisan('app:preflight')
            ->expectsOutputToContain('Production settings look good.')
            ->assertExitCode(0);
    }
}
