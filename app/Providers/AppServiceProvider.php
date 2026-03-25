<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductReview;
use App\Services\Dashboard\DashboardLayoutService;
use App\Support\Dashboard\SidebarBuilder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\ShippingMethod;
use App\Models\Shipment;

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
            $dashboardPendingPaymentsCount = 0;
            $dashboardUnreadMessagesCount = 0;
            $dashboardProductsCount = 0;
            $dashboardPendingReviewsCount = 0;
                // ✅ Shipping counters
                $dashboardShippingMethodsCount = 0;
                $dashboardActiveShippingMethodsCount = 0;
                $dashboardShipmentsInTransitCount = 0;

            $user = Auth::user();

            if ($user) {
                $dashboardPendingOrders = Order::query()
                    ->with(['user:id,name'])
                    ->whereIn('status', ['pending', 'processing'])
                    ->latest('id')
                    ->take(5)
                    ->get();

                $dashboardPendingOrdersCount = Order::query()
                    ->whereIn('status', ['pending', 'processing'])
                    ->count();

                $dashboardPendingPaymentsCount = class_exists(Payment::class)
                    ? Payment::query()
                        ->whereIn('status', ['pending', 'initiated', 'unpaid'])
                        ->count()
                    : 0;

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

                // ================= Shipping =================
                $dashboardShippingMethodsCount = ShippingMethod::query()->count();
                $dashboardActiveShippingMethodsCount = ShippingMethod::query()->where('is_active', true)->count();
                $dashboardShipmentsInTransitCount = Shipment::query()->whereIn('status', [
                    'pending',
                    'processing',
                    'shipped',
                ])->count();



            }

            $sidebar = app(SidebarBuilder::class)->build($user, [
                'dashboardPendingOrdersCount'   => $dashboardPendingOrdersCount,
                'dashboardPendingPaymentsCount' => $dashboardPendingPaymentsCount,
                'dashboardUnreadMessagesCount'  => $dashboardUnreadMessagesCount,
                'dashboardProductsCount'        => $dashboardProductsCount,
                'dashboardPendingReviewsCount'  => $dashboardPendingReviewsCount,
                // ✅ Shipping
                'dashboardShippingMethodsCount'       => $dashboardShippingMethodsCount,
                'dashboardActiveShippingMethodsCount' => $dashboardActiveShippingMethodsCount,
                'dashboardShipmentsInTransitCount'    => $dashboardShipmentsInTransitCount,

            ]);

            $view->with([
                'dashboardPendingOrders'         => $dashboardPendingOrders,
                'dashboardPendingOrdersCount'    => $dashboardPendingOrdersCount,
                'dashboardPendingPaymentsCount'  => $dashboardPendingPaymentsCount,
                'dashboardUnreadMessages'        => $dashboardUnreadMessages,
                'dashboardUnreadMessagesCount'   => $dashboardUnreadMessagesCount,
                'dashboardProductsCount'         => $dashboardProductsCount,
                'dashboardPendingReviewsCount'   => $dashboardPendingReviewsCount,
                'sidebar'                        => $sidebar,
                'dashboardShippingMethodsCount'       => $dashboardShippingMethodsCount,
                'dashboardActiveShippingMethodsCount' => $dashboardActiveShippingMethodsCount,
                'dashboardShipmentsInTransitCount'    => $dashboardShipmentsInTransitCount,

            ]);
        });

        Paginator::useBootstrapFive();
    }
}

