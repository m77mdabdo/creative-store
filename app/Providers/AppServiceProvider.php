<?php

namespace App\Providers;

use App\Models\AbandonedCartRecovery;
use App\Models\Address;
use App\Models\Cart;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Shipment;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\Dashboard\DashboardLayoutService;
use App\Support\Dashboard\SidebarBuilder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
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

            // ✅ Shipping counters
            $dashboardShippingMethodsCount = 0;
            $dashboardActiveShippingMethodsCount = 0;
            $dashboardShipmentsInTransitCount = 0;

            // ✅ Customers counters
            $dashboardUsersCount = 0;
            $dashboardAddressesCount = 0;
            $dashboardWishlistsCount = 0;
            $dashboardCartsCount = 0;
            $dashboardAbandonedCartsCount = 0;

            // ✅ Abandoned Cart Recovery
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

                $dashboardActiveShippingMethodsCount = ShippingMethod::query()
                    ->where('is_active', true)
                    ->count();

                $dashboardShipmentsInTransitCount = Shipment::query()
                    ->whereIn('status', [
                        'pending',
                        'processing',
                        'shipped',
                    ])
                    ->count();

                // ================= Customers =================
                $dashboardUsersCount = class_exists(User::class)
                    ? User::query()->count()
                    : 0;

                $dashboardAddressesCount = class_exists(Address::class)
                    ? Address::query()->count()
                    : 0;

                $dashboardWishlistsCount = class_exists(Wishlist::class)
                    ? Wishlist::query()->count()
                    : 0;

                $dashboardCartsCount = class_exists(Cart::class)
                    ? Cart::query()->count()
                    : 0;

                // ✅ Abandoned carts: carts فيها عناصر وآخر تحديث مر عليه أكثر من 24 ساعة
                $dashboardAbandonedCartsCount = class_exists(Cart::class)
                    ? Cart::query()
                        ->where('updated_at', '<', now()->subDay())
                        ->whereHas('items')
                        ->count()
                    : 0;

                // ================= Abandoned Cart Recovery =================
                $dashboardAbandonedCartRecoveriesCount = class_exists(AbandonedCartRecovery::class)
                    ? AbandonedCartRecovery::query()
                        ->whereIn('status', ['pending', 'sent', 'failed'])
                        ->count()
                    : 0;
            }

            $sidebar = app(SidebarBuilder::class)->build($user, [
                'dashboardPendingOrdersCount'              => $dashboardPendingOrdersCount,
                'dashboardPendingPaymentsCount'            => $dashboardPendingPaymentsCount,
                'dashboardUnreadMessagesCount'             => $dashboardUnreadMessagesCount,
                'dashboardProductsCount'                   => $dashboardProductsCount,
                'dashboardPendingReviewsCount'             => $dashboardPendingReviewsCount,

                // ✅ Shipping
                'dashboardShippingMethodsCount'            => $dashboardShippingMethodsCount,
                'dashboardActiveShippingMethodsCount'      => $dashboardActiveShippingMethodsCount,
                'dashboardShipmentsInTransitCount'         => $dashboardShipmentsInTransitCount,

                // ✅ Customers
                'dashboardUsersCount'                      => $dashboardUsersCount,
                'dashboardAddressesCount'                  => $dashboardAddressesCount,
                'dashboardWishlistsCount'                  => $dashboardWishlistsCount,
                'dashboardCartsCount'                      => $dashboardCartsCount,
                'dashboardAbandonedCartsCount'             => $dashboardAbandonedCartsCount,

                // ✅ Recovery
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

                // ✅ Shipping
                'dashboardShippingMethodsCount'            => $dashboardShippingMethodsCount,
                'dashboardActiveShippingMethodsCount'      => $dashboardActiveShippingMethodsCount,
                'dashboardShipmentsInTransitCount'         => $dashboardShipmentsInTransitCount,

                // ✅ Customers
                'dashboardUsersCount'                      => $dashboardUsersCount,
                'dashboardAddressesCount'                  => $dashboardAddressesCount,
                'dashboardWishlistsCount'                  => $dashboardWishlistsCount,
                'dashboardCartsCount'                      => $dashboardCartsCount,
                'dashboardAbandonedCartsCount'             => $dashboardAbandonedCartsCount,

                // ✅ Recovery
                'dashboardAbandonedCartRecoveriesCount'    => $dashboardAbandonedCartRecoveriesCount,
            ]);
        });

        Paginator::useBootstrapFive();
    }
}
