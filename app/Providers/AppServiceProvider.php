<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Support\Dashboard\SidebarBuilder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\Dashboard\DashboardLayoutService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('files', function () {
            return new Filesystem();
        });

        $this->app->singleton(SidebarBuilder::class, function () {
            return new SidebarBuilder();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('dashboard.app.nav', function ($view) {
            if (auth()->check()) {
                $view->with(app(DashboardLayoutService::class)->data());
            }
        });

        View::composer([
            'dashboard.app.nav',
            'dashboard.app.sidebar',
        ], function ($view) {
            $dashboardPendingOrders = collect();
            $dashboardUnreadMessages = collect();

            $dashboardPendingOrdersCount = 0;
            $dashboardUnreadMessagesCount = 0;
            $dashboardProductsCount = 0;
            $dashboardPendingReviewsCount = 0;

            $sidebar = [
                'brand' => [
                    'title' => config('app.name', 'Creative Store'),
                    'logo'  => asset('AdminLTE/dist/img/AdminLTELogo.png'),
                    'route' => route('dashboard.home'),
                ],
                'user' => [
                    'name'   => 'Dashboard User',
                    'avatar' => asset('AdminLTE/dist/img/user2-160x160.jpg'),
                ],
                'items' => [],
            ];

            $user = Auth::user();

            if ($user && method_exists($user, 'isDashboardUser') && $user->isDashboardUser()) {
                $dashboardPendingOrders = Order::query()
                    ->with(['user:id,name'])
                    ->whereIn('status', ['pending', 'processing'])
                    ->latest('id')
                    ->take(5)
                    ->get();

                $dashboardPendingOrdersCount = Order::query()
                    ->whereIn('status', ['pending', 'processing'])
                    ->count();

                $dashboardUnreadMessages = ContactMessage::query()
                    ->where('is_read', false)
                    ->latest('id')
                    ->take(5)
                    ->get();

                $dashboardUnreadMessagesCount = ContactMessage::query()
                    ->where('is_read', false)
                    ->count();

                $dashboardProductsCount = Product::query()->count();

                $dashboardPendingReviewsCount = class_exists(ProductReview::class)
                    ? ProductReview::query()->where('status', 'pending')->count()
                    : 0;

                $sidebar = app(SidebarBuilder::class)->build($user, [
                    'dashboardPendingOrdersCount'  => $dashboardPendingOrdersCount,
                    'dashboardUnreadMessagesCount' => $dashboardUnreadMessagesCount,
                    'dashboardProductsCount'       => $dashboardProductsCount,
                    'dashboardPendingReviewsCount' => $dashboardPendingReviewsCount,
                ]);
            }

            $view->with([
                'dashboardPendingOrders'        => $dashboardPendingOrders,
                'dashboardPendingOrdersCount'   => $dashboardPendingOrdersCount,
                'dashboardUnreadMessages'       => $dashboardUnreadMessages,
                'dashboardUnreadMessagesCount'  => $dashboardUnreadMessagesCount,
                'dashboardProductsCount'        => $dashboardProductsCount,
                'dashboardPendingReviewsCount'  => $dashboardPendingReviewsCount,
                'sidebar'                       => $sidebar,
            ]);
        });

        Paginator::useBootstrap();
    }
}
