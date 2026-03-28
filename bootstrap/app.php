<?php

use App\Models\ErrorLog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/dashboard.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e) {
            try {
                ErrorLog::create([
                    'level' => class_basename($e),
                    'service' => 'web',
                    'message' => $e->getMessage(),
                    'context' => [
                        'url' => request()->fullUrl(),
                        'method' => request()->method(),
                        'ip' => request()->ip(),
                        'user_id' => auth()->id(),
                    ],
                    'trace' => $e->getTraceAsString(),
                ]);
            } catch (Throwable $logError) {
                // منع loop لو حصل خطأ أثناء تسجيل الخطأ
            }
        });
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('carts:process-abandoned')->everyMinute();
    })
    ->create();
