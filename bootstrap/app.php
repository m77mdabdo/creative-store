<?php

use App\Models\ErrorLog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
// use Throwable;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__ . '/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/dashboard.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Outside local development, only requests for the APP_URL host (and its
        // subdomains) are served, so a forged Host header cannot reach the app.
        $middleware->trustHosts();
        $middleware->appendToGroup('web', \App\Http\Middleware\PersistCustomerCart::class);

        // A session ends once the account's password changes elsewhere,
        // and a disabled account is signed out on its next request.
        $middleware->authenticateSessions();
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureAccountIsActive::class);

        // Guests can shop and fill the cart; checkout asks them to sign in
        // and brings them back to the same step afterwards.
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->routeIs('shipping.*', 'checkout.*') && $request->hasSession()) {
                $request->session()->flash('info', __('site.login_to_checkout'));
            }

            return route('login');
        });

        $middleware->validateCsrfTokens(except: [
            'webhooks/moyasar',
        ]);

        $middleware->alias([
            'role'       => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'localeSessionRedirect' => LocaleSessionRedirect::class,
            'localizationRedirect' => LaravelLocalizationRedirectFilter::class,
             'localeViewPath' => LaravelLocalizationViewPath::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Errors go to Sentry when SENTRY_LARAVEL_DSN is set (no-op otherwise).
        // Guarded so a missing package never stops the app from booting.
        if (class_exists(\Sentry\Laravel\Integration::class)) {
            \Sentry\Laravel\Integration::handles($exceptions);
        }

        $exceptions->report(function (Throwable $e) {
            try {
                if (app()->runningInConsole()) {
                    return;
                }

                $message = trim((string) $e->getMessage());

                if ($message === '') {
                    $message = class_basename($e);
                }

                $trace = $e->getTraceAsString();

                // Store each distinct error at most once every two minutes.
                // Cache::add is atomic, so a burst of identical failures writes one row.
                if (! \Illuminate\Support\Facades\Cache::add('error-log:' . sha1($message . '|' . $e->getFile() . ':' . $e->getLine()), true, 120)) {
                    return;
                }

                ErrorLog::query()->create([
                    'level'       => normalize_error_log_level($e),
                    'service'     => 'application',
                    'message'     => mb_substr($message, 0, 65535),
                    'context'     => [
                        'exception' => get_class($e),
                        'file'      => $e->getFile(),
                        'line'      => $e->getLine(),
                        // Path only: query strings can hold reset tokens and link signatures.
                        'url'       => request()?->url(),
                        'method'    => request()?->method(),
                        'user_id'   => auth()->id(),
                        'ip'        => request()?->ip(),
                    ],
                    'trace'       => mb_substr($trace, 0, 65535),
                    'resolved_at' => null,
                ]);
            } catch (Throwable $loggingException) {
                // prevent loop/crash while saving error logs
            }
        });
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('carts:process-abandoned')->everyFiveMinutes()->withoutOverlapping();

        $schedule->command('orders:expire-unpaid')->everyFifteenMinutes()->withoutOverlapping();

        $schedule->command('model:prune')->dailyAt('03:30')->withoutOverlapping();

        $schedule->command('backup:run scheduled --cleanup-days=' . (int) config('backup.retention_days', 30))
            ->cron('0 */12 * * *')
            ->withoutOverlapping(120)
            ->runInBackground();
    })
    ->create();
