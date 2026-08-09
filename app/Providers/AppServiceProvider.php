<?php

namespace App\Providers;

use App\Services\Dashboard\DashboardBadgeService;
use App\Support\Dashboard\SidebarBuilder;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('files', function () {
            return new Filesystem();
        });

        $this->app->singleton(SidebarBuilder::class, function () {
            return new SidebarBuilder();
        });
    }

    public function boot(): void
    {
        $this->configureRateLimiting();

        // Month names, weekdays and AM/PM follow the site language.
        Carbon::setLocale($this->app->getLocale());
        Event::listen(LocaleUpdated::class, fn (LocaleUpdated $event) => Carbon::setLocale($event->locale));

        View::composer(['dashboard.app.nav', 'dashboard.app.sidebar'], function ($view) {
            $user = Auth::user();

            if (! $user) {
                return;
            }

            $badges = app(DashboardBadgeService::class)->data();

            $view->with($badges + [
                'sidebar' => app(SidebarBuilder::class)->build($user, array_filter(
                    $badges,
                    fn ($value) => is_int($value)
                )),
            ]);
        });

        Paginator::useBootstrapFive();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('auth', function (Request $request) {
            $email = strtolower((string) $request->input('email'));

            return [
                Limit::perMinute(5)->by('auth:' . $email . '|' . $request->ip()),
                Limit::perMinute(20)->by('auth-ip:' . $request->ip()),
            ];
        });

        RateLimiter::for('otp', fn (Request $request) => [
            Limit::perMinutes(10, 5)->by('otp:' . strtolower((string) $request->input('email'))),
            Limit::perMinute(10)->by('otp-ip:' . $request->ip()),
        ]);

        RateLimiter::for('forms', fn (Request $request) => Limit::perMinute(6)
            ->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('checkout', fn (Request $request) => Limit::perMinute(10)
            ->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('webhooks', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
    }
}
