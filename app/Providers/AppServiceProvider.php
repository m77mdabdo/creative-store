<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Support\Dashboard\SidebarBuilder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // حل مشكلة Target class [files] does not exist
        $this->app->singleton('files', function () {
            return new Filesystem();
        });

        // Register Sidebar Builder
        $this->app->singleton(SidebarBuilder::class, function () {
            return new SidebarBuilder();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['dashboard.app.nav', 'dashboard.app.sidebar'], function ($view) {
            $dashboardPendingOrders = collect();
            $dashboardUnreadMessages = collect();
            $dashboardPendingOrdersCount = 0;
            $dashboardUnreadMessagesCount = 0;
            $sidebar = [
                'brand' => [
                    'title' => 'Creative Store',
                    'logo' => asset('AdminLTE/dist/img/AdminLTELogo.png'),
                    'route' => route('dashboard.home'),
                ],
                'user' => [
                    'name' => 'Dashboard User',
                    'avatar' => asset('AdminLTE/dist/img/user2-160x160.jpg'),
                ],
                'items' => [],
            ];

            if (Auth::check() && Auth::user()->isDashboardUser()) {
                $dashboardPendingOrders = Order::with('user')
                    ->whereIn('status', ['pending', 'processing'])
                    ->latest()
                    ->take(5)
                    ->get();

                $dashboardPendingOrdersCount = Order::whereIn('status', ['pending', 'processing'])->count();

                $dashboardUnreadMessages = ContactMessage::query()
                    ->where('is_read', false)
                    ->latest()
                    ->take(5)
                    ->get();

                $dashboardUnreadMessagesCount = ContactMessage::query()
                    ->where('is_read', false)
                    ->count();

                $sidebar = app(SidebarBuilder::class)->build(Auth::user(), [
                    'dashboardPendingOrdersCount' => $dashboardPendingOrdersCount,
                    'dashboardUnreadMessagesCount' => $dashboardUnreadMessagesCount,
                ]);
            }

            $view->with([
                'dashboardPendingOrders' => $dashboardPendingOrders,
                'dashboardPendingOrdersCount' => $dashboardPendingOrdersCount,
                'dashboardUnreadMessages' => $dashboardUnreadMessages,
                'dashboardUnreadMessagesCount' => $dashboardUnreadMessagesCount,
                'sidebar' => $sidebar,
            ]);
        });

        Paginator::useBootstrap();
    }
}
