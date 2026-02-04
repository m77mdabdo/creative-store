<?php

namespace App\Services\Dashboard;

use App\Models\AbandonedCartRecovery;
use App\Models\Address;
use App\Models\Cart;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\Shipment;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Cache;

/**
 * Counters and previews shown in the dashboard sidebar and top bar.
 *
 * They are rendered on every dashboard page, so the numbers are computed at
 * most once per minute (stale values are served while a refresh runs)
 * instead of running ~20 table-wide COUNT queries per page view.
 */
class DashboardBadgeService
{
    private const CACHE_KEY = 'dashboard.badges';

    public function data(): array
    {
        return Cache::flexible(self::CACHE_KEY, [60, 300], fn () => $this->compute());
    }

    /**
     * The badge data is cached once for all staff, so each person's copy is
     * trimmed to what their permissions allow before it reaches the view.
     * Previews carry customer names and messages, so they are removed
     * entirely, not just hidden.
     */
    public static function visibleTo(User $user, array $badges): array
    {
        $sections = [
            'orders.view'   => ['dashboardPendingOrders', 'dashboardPendingOrdersCount'],
            'payments.view' => ['dashboardPendingPaymentsCount'],
            'contact.view'  => ['dashboardUnreadMessages', 'dashboardUnreadMessagesCount'],
            'reviews.view'  => ['dashboardPendingReviews', 'dashboardPendingReviewsCount'],
            'users.view'    => ['dashboardUsersCount'],
        ];

        foreach ($sections as $permission => $keys) {
            if ($user->hasPermission($permission)) {
                continue;
            }

            foreach ($keys as $key) {
                $badges[$key] = str_ends_with($key, 'Count') ? 0 : collect();
            }
        }

        return $badges;
    }

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function compute(): array
    {
        $openOrders = ['pending', 'processing'];

        return [
            'dashboardPendingOrders' => Order::query()
                ->with(['user:id,name'])
                ->whereIn('status', $openOrders)
                ->latest('id')
                ->take(5)
                ->get(),
            'dashboardPendingOrdersCount' => Order::query()->whereIn('status', $openOrders)->count(),
            'dashboardPendingPaymentsCount' => Payment::query()->whereIn('status', ['pending', 'authorized'])->count(),

            'dashboardUnreadMessages' => ContactMessage::query()->where('is_read', false)->latest('id')->take(5)->get(),
            'dashboardUnreadMessagesCount' => ContactMessage::query()->where('is_read', false)->count(),

            'dashboardPendingReviews' => Review::query()
                ->with(['product.translations', 'user:id,name'])
                ->where('is_approved', false)
                ->latest('id')
                ->take(5)
                ->get(),
            'dashboardPendingReviewsCount' => Review::query()->where('is_approved', false)->count(),

            'dashboardProductsCount' => Product::query()->count(),
            'dashboardShippingMethodsCount' => ShippingMethod::query()->count(),
            'dashboardActiveShippingMethodsCount' => ShippingMethod::query()->where('is_active', true)->count(),
            'dashboardShipmentsInTransitCount' => Shipment::query()
                ->whereIn('status', ['pending', 'processing', 'shipped', 'in_transit'])
                ->count(),

            'dashboardUsersCount' => User::query()->count(),
            'dashboardAddressesCount' => Address::query()->count(),
            'dashboardWishlistsCount' => Wishlist::query()->count(),
            'dashboardCartsCount' => Cart::query()->count(),
            'dashboardAbandonedCartsCount' => Cart::query()
                ->where('updated_at', '<', now()->subDay())
                ->whereHas('items')
                ->count(),
            'dashboardAbandonedCartRecoveriesCount' => AbandonedCartRecovery::query()
                ->whereIn('status', ['pending', 'sent', 'failed'])
                ->count(),
        ];
    }
}
