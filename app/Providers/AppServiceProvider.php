<?php

namespace App\Providers;

use App\Models\AbandonedCartRecovery;
use App\Models\Address;
use App\Models\Cart;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\Dashboard\DashboardLayoutService;
use App\Support\Dashboard\SidebarBuilder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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

            $dashboardShippingMethodsCount = 0;
            $dashboardActiveShippingMethodsCount = 0;
            $dashboardShipmentsInTransitCount = 0;

            $dashboardUsersCount = 0;
            $dashboardAddressesCount = 0;
            $dashboardWishlistsCount = 0;
            $dashboardCartsCount = 0;
            $dashboardAbandonedCartsCount = 0;

            $dashboardAbandonedCartRecoveriesCount = 0;

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

                $dashboardPendingPaymentsCount = Payment::query()
                    ->whereIn('status', ['pending', 'authorized'])
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

                $dashboardPendingReviewsCount = $this->resolveDashboardPendingReviewsCount();

                $dashboardShippingMethodsCount = ShippingMethod::query()->count();

                $dashboardActiveShippingMethodsCount = ShippingMethod::query()
                    ->where('is_active', true)
                    ->count();

                $dashboardShipmentsInTransitCount = Shipment::query()
                    ->whereIn('status', [
                        'pending',
                        'processing',
                        'shipped',
                        'in_transit',
                    ])
                    ->count();

                $dashboardUsersCount = User::query()->count();

                $dashboardAddressesCount = Address::query()->count();

                $dashboardWishlistsCount = Wishlist::query()->count();

                $dashboardCartsCount = Cart::query()->count();

                $dashboardAbandonedCartsCount = Cart::query()
                    ->where('updated_at', '<', now()->subDay())
                    ->whereHas('items')
                    ->count();

                $dashboardAbandonedCartRecoveriesCount = AbandonedCartRecovery::query()
                    ->whereIn('status', ['pending', 'sent', 'failed'])
                    ->count();
            }

            $sidebar = app(SidebarBuilder::class)->build($user, [
                'dashboardPendingOrdersCount'              => $dashboardPendingOrdersCount,
                'dashboardPendingPaymentsCount'            => $dashboardPendingPaymentsCount,
                'dashboardUnreadMessagesCount'             => $dashboardUnreadMessagesCount,
                'dashboardProductsCount'                   => $dashboardProductsCount,
                'dashboardPendingReviewsCount'             => $dashboardPendingReviewsCount,

                'dashboardShippingMethodsCount'            => $dashboardShippingMethodsCount,
                'dashboardActiveShippingMethodsCount'      => $dashboardActiveShippingMethodsCount,
                'dashboardShipmentsInTransitCount'         => $dashboardShipmentsInTransitCount,

                'dashboardUsersCount'                      => $dashboardUsersCount,
                'dashboardAddressesCount'                  => $dashboardAddressesCount,
                'dashboardWishlistsCount'                  => $dashboardWishlistsCount,
                'dashboardCartsCount'                      => $dashboardCartsCount,
                'dashboardAbandonedCartsCount'             => $dashboardAbandonedCartsCount,

                'dashboardAbandonedCartRecoveriesCount'    => $dashboardAbandonedCartRecoveriesCount,
            ]);

            $view->with([
                'dashboardPendingOrders'                   => $dashboardPendingOrders,
                'dashboardPendingOrdersCount'              => $dashboardPendingOrdersCount,
                'dashboardPendingPaymentsCount'            => $dashboardPendingPaymentsCount,
                'dashboardUnreadMessages'                  => $dashboardUnreadMessages,
                'dashboardUnreadMessagesCount'             => $dashboardUnreadMessagesCount,
                'dashboardProductsCount'                   => $dashboardProductsCount,
                'dashboardPendingReviewsCount'             => $dashboardPendingReviewsCount,
                'sidebar'                                  => $sidebar,

                'dashboardShippingMethodsCount'            => $dashboardShippingMethodsCount,
                'dashboardActiveShippingMethodsCount'      => $dashboardActiveShippingMethodsCount,
                'dashboardShipmentsInTransitCount'         => $dashboardShipmentsInTransitCount,

                'dashboardUsersCount'                      => $dashboardUsersCount,
                'dashboardAddressesCount'                  => $dashboardAddressesCount,
                'dashboardWishlistsCount'                  => $dashboardWishlistsCount,
                'dashboardCartsCount'                      => $dashboardCartsCount,
                'dashboardAbandonedCartsCount'             => $dashboardAbandonedCartsCount,

                'dashboardAbandonedCartRecoveriesCount'    => $dashboardAbandonedCartRecoveriesCount,
            ]);
        });

        Paginator::useBootstrapFive();
    }

    protected function resolveDashboardPendingReviewsCount(): int
    {
        if (class_exists(\App\Models\ProductReview::class)) {
            $model = new \App\Models\ProductReview();
            $table = $model->getTable();

            if (Schema::hasColumn($table, 'status')) {
                return $model->newQuery()
                    ->where('status', 'pending')
                    ->count();
            }

            if (Schema::hasColumn($table, 'is_approved')) {
                return $model->newQuery()
                    ->where('is_approved', false)
                    ->count();
            }
        }

        if (class_exists(\App\Models\Review::class)) {
            $model = new \App\Models\Review();
            $table = $model->getTable();

            if (Schema::hasColumn($table, 'status')) {
                return $model->newQuery()
                    ->where('status', 'pending')
                    ->count();
            }

            if (Schema::hasColumn($table, 'is_approved')) {
                return $model->newQuery()
                    ->where('is_approved', false)
                    ->count();
            }
        }

        return 0;
    }
}
