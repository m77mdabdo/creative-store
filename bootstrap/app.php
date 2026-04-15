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

                $duplicate = ErrorLog::query()
                    ->where('message', $message)
                    ->where('service', 'application')
                    ->where('created_at', '>=', now()->subMinutes(2))
                    ->first();

                if ($duplicate) {
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
                        'url'       => request()?->fullUrl(),
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
        $schedule->command('carts:process-abandoned')->everyMinute();

        $schedule->command('backup:run scheduled --cleanup-days=' . (int) config('backup.retention_days', 30))
            ->cron('0 */12 * * *')
            ->withoutOverlapping(120)
            ->runInBackground();
    })
    ->create();
