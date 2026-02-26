<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Refuses to continue a deploy when the server's settings would expose the
 * store: debug pages on, a non-production environment, a plain-HTTP URL,
 * no application key, debug logging, or test payment keys on the live site.
 *
 *   php artisan app:preflight          (exits 1 when anything is wrong)
 */
class ProductionPreflightCommand extends Command
{
    protected $signature = 'app:preflight';

    protected $description = 'Check the production settings before going live';

    public function handle(): int
    {
        $checks = [
            'APP_ENV is production'            => app()->environment('production'),
            'APP_DEBUG is false'               => config('app.debug') === false,
            'APP_KEY is set'                   => filled(config('app.key')),
            'APP_URL uses https'               => str_starts_with((string) config('app.url'), 'https://'),
            'Session cookie is secure'         => (bool) config('session.secure'),
            'Log level is not debug'           => ! in_array('debug', $this->logLevels(), true),
            'Moyasar uses live keys'           => ! str_starts_with((string) config('services.moyasar.secret_key'), 'sk_test_'),
            'Moyasar webhook secret is set'    => filled(config('services.moyasar.webhook_secret')),
        ];

        $failed = 0;

        foreach ($checks as $label => $passed) {
            $this->line(($passed ? '<info>  ok </info> ' : '<error> FAIL </error> ') . $label);
            $failed += $passed ? 0 : 1;
        }

        if ($failed > 0) {
            $this->newLine();
            $this->error("$failed setting(s) must be fixed in .env before this server goes live.");

            return self::FAILURE;
        }

        $this->info('Production settings look good.');

        return self::SUCCESS;
    }

    /**
     * Levels of the default log channel, following a "stack" into its channels.
     *
     * @return list<string|null>
     */
    private function logLevels(): array
    {
        $default = (string) config('logging.default');
        $channels = config("logging.channels.$default.driver") === 'stack'
            ? (array) config("logging.channels.$default.channels", [])
            : [$default];

        return array_map(fn ($channel) => config("logging.channels.$channel.level"), $channels);
    }
}
