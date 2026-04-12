<?php

namespace App\Support\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class SidebarBuilder
{
    public function build(?User $user, array $counters = []): array
    {
        $brand = [
            'title' => config('app.name', 'Creative Store'),
            'logo'  => asset('AdminLTE/dist/img/logo-01.png'),
            'route' => $this->safeRoute('dashboard.home'),
        ];

        $sidebarUser = [
            'name'   => $user?->name ?: 'Dashboard User',
            'avatar' => asset('AdminLTE/dist/img/user1-128x128.jpg'),
        ];

        if (! $user) {
            return [
                'brand' => $brand,
                'user'  => $sidebarUser,
                'items' => [],
            ];
        }

        $pendingOrdersCount   = max(0, (int) ($counters['dashboardPendingOrdersCount'] ?? 0));
        $pendingPaymentsCount = max(0, (int) ($counters['dashboardPendingPaymentsCount'] ?? 0));
        $unreadMessagesCount  = max(0, (int) ($counters['dashboardUnreadMessagesCount'] ?? 0));
        $productsCount        = max(0, (int) ($counters['dashboardProductsCount'] ?? 0));
        $pendingReviewsCount  = max(0, (int) ($counters['dashboardPendingReviewsCount'] ?? 0));

        $shippingMethodsCount       = max(0, (int) ($counters['dashboardShippingMethodsCount'] ?? 0));
        $activeShippingMethodsCount = max(0, (int) ($counters['dashboardActiveShippingMethodsCount'] ?? 0));
        $shipmentsInTransitCount    = max(0, (int) ($counters['dashboardShipmentsInTransitCount'] ?? 0));

        $usersCount          = max(0, (int) ($counters['dashboardUsersCount'] ?? 0));
        $addressesCount      = max(0, (int) ($counters['dashboardAddressesCount'] ?? 0));
        $wishlistsCount      = max(0, (int) ($counters['dashboardWishlistsCount'] ?? 0));
        $cartsCount          = max(0, (int) ($counters['dashboardCartsCount'] ?? 0));
        $abandonedCartsCount = max(0, (int) ($counters['dashboardAbandonedCartsCount'] ?? 0));

        $abandonedCartRecoveriesCount = max(0, (int) ($counters['dashboardAbandonedCartRecoveriesCount'] ?? 0));

        $salesOperationsAlertCount = $pendingOrdersCount;
        $financeAlertCount         = $pendingPaymentsCount;

        $items = [];

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        if ($this->canAny($user, ['dashboard.view']) || $user->hasRole('owner')) {
            $this->pushItem($items, $this->singleItem(
                title: 'Dashboard',
                titleKey: 'site.Dashboard',
                icon: 'fas fa-tachometer-alt',
                routeName: 'dashboard.home',
                routePatterns: ['dashboard.home']
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Sales Operations
        |--------------------------------------------------------------------------
        */
        $salesChildren = [];

        if ($this->canAny($user, ['orders.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Orders',
                titleKey: 'site.Orders',
                routeName: 'dashboard.orders.index',
                routePatterns: ['dashboard.orders.*'],
                icon: 'fas fa-shopping-cart',
                badge: $this->normalizeBadge([
                    'value' => $pendingOrdersCount,
                    'class' => 'badge badge-warning',
                ])
            );
        }

        if ($this->canAny($user, ['returns.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Returns',
                titleKey: 'site.Returns',
                routeName: 'dashboard.returns.index',
                routePatterns: ['dashboard.returns.*'],
                icon: 'fas fa-box-open'
            );
        }

        if ($this->canAny($user, ['coupons.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Coupons',
                titleKey: 'site.Coupons',
                routeName: 'dashboard.coupons.index',
                routePatterns: ['dashboard.coupons.*'],
                icon: 'fas fa-ticket-alt'
            );
        }

        if (! empty(array_filter($salesChildren))) {
            $this->pushItem($items, $this->treeItem(
                title: 'Sales Operations',
                titleKey: 'site.Sales Operations',
                icon: 'fas fa-shopping-bag',
                routePatterns: [
                    'dashboard.orders.*',
                    'dashboard.returns.*',
                    'dashboard.coupons.*',
                ],
                children: $salesChildren,
                badge: $this->normalizeBadge([
                    'value' => $salesOperationsAlertCount,
                    'class' => 'badge badge-warning',
                ])
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Finance
        |--------------------------------------------------------------------------
        */
        $financeChildren = [];

        if ($this->canAny($user, ['payments.view'])) {
            $financeChildren[] = $this->childItem(
                title: 'Payments',
                titleKey: 'site.Payments',
                routeName: 'dashboard.payments.index',
                routePatterns: ['dashboard.payments.*'],
                icon: 'fas fa-credit-card',
                badge: $this->normalizeBadge([
                    'value' => $pendingPaymentsCount,
                    'class' => 'badge badge-danger',
                ])
            );
        }

        if ($this->canAny($user, ['refunds.view'])) {
            $financeChildren[] = $this->childItem(
                title: 'Refunds',
                titleKey: 'site.Refunds',
                routeName: 'dashboard.refunds.index',
                routePatterns: ['dashboard.refunds.*'],
                icon: 'fas fa-undo'
            );
        }

        if (! empty(array_filter($financeChildren))) {
            $this->pushItem($items, $this->treeItem(
                title: 'Finance',
                titleKey: 'site.Finance',
                icon: 'fas fa-wallet',
                routePatterns: [
                    'dashboard.payments.*',
                    'dashboard.refunds.*',
                ],
                children: $financeChildren,
                badge: $this->normalizeBadge([
                    'value' => $financeAlertCount,
                    'class' => 'badge badge-danger',
                ])
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Shipping
        |--------------------------------------------------------------------------
        */
        $shippingChildren = [];

        if ($this->canAny($user, ['shipping-methods.view'])) {
            $shippingChildren[] = $this->childItem(
                title: 'Shipping Methods',
                titleKey: 'site.Shipping Methods',
                routeName: 'dashboard.shipping-methods.index',
                routePatterns: ['dashboard.shipping-methods.*'],
                icon: 'fas fa-truck',
                badge: $this->normalizeBadge([
                    'value' => $activeShippingMethodsCount,
                    'class' => 'badge badge-success',
                ])
            );
        }

        if ($this->canAny($user, ['shipping-zones.view'])) {
            $shippingChildren[] = $this->childItem(
                title: 'Shipping Zones',
                titleKey: 'site.Shipping Zones',
                routeName: 'dashboard.shipping-zones.index',
                routePatterns: ['dashboard.shipping-zones.*'],
                icon: 'fas fa-map-marked-alt'
            );
        }

        if ($this->canAny($user, ['shipping-rates.view'])) {
            $shippingChildren[] = $this->childItem(
                title: 'Shipping Rates',
                titleKey: 'site.Shipping Rates',
                routeName: 'dashboard.shipping-rates.index',
                routePatterns: ['dashboard.shipping-rates.*'],
                icon: 'fas fa-money-check-alt'
            );
        }

        if ($this->canAny($user, ['shipments.view'])) {
            $shippingChildren[] = $this->childItem(
                title: 'Shipments',
                titleKey: 'site.Shipments',
                routeName: 'dashboard.shipments.index',
                routePatterns: ['dashboard.shipments.*'],
                icon: 'fas fa-shipping-fast',
                badge: $this->normalizeBadge([
                    'value' => $shipmentsInTransitCount,
                    'class' => 'badge badge-warning',
                ])
            );
        }

        if (! empty(array_filter($shippingChildren))) {
            $this->pushItem($items, $this->treeItem(
                title: 'Shipping',
                titleKey: 'site.Shipping',
                icon: 'fas fa-truck-loading',
                routePatterns: [
                    'dashboard.shipping-methods.*',
                    'dashboard.shipping-zones.*',
                    'dashboard.shipping-rates.*',
                    'dashboard.shipments.*',
                ],
                children: $shippingChildren,
                badge: $this->normalizeBadge([
                    'value' => $shippingMethodsCount,
                    'class' => 'badge badge-info',
                ])
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Catalog
        |--------------------------------------------------------------------------
        */
        $catalogChildren = [];

        if ($this->canAny($user, ['categories.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Categories',
                titleKey: 'site.Categories',
                routeName: 'dashboard.categories.index',
                routePatterns: ['dashboard.categories.*'],
                icon: 'fas fa-tags'
            );
        }

        if ($this->canAny($user, ['products.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Products',
                titleKey: 'site.Products',
                routeName: 'dashboard.products.index',
                routePatterns: ['dashboard.products.*'],
                icon: 'fas fa-box'
            );
        }

        if ($this->canAny($user, ['attributes.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Attributes',
                titleKey: 'site.Attributes',
                routeName: 'dashboard.attributes.index',
                routePatterns: ['dashboard.attributes.*'],
                icon: 'fas fa-sliders-h'
            );
        }

        if ($this->canAny($user, ['reviews.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Product Reviews',
                titleKey: 'site.Product Reviews',
                routeName: 'dashboard.reviews.index',
                routePatterns: [
                    'dashboard.reviews.*',
                    'dashboard.products.reviews.*',
                ],
                icon: 'fas fa-star'
            );
        }

        if (! empty(array_filter($catalogChildren))) {
            $catalogBadgeValue = $pendingReviewsCount > 0 ? $pendingReviewsCount : $productsCount;
            $catalogBadgeClass = $pendingReviewsCount > 0 ? 'badge badge-warning' : 'badge badge-info';

            $this->pushItem($items, $this->treeItem(
                title: 'Catalog',
                titleKey: 'site.Catalog',
                icon: 'fas fa-boxes',
                routePatterns: [
                    'dashboard.categories.*',
                    'dashboard.products.*',
                    'dashboard.attributes.*',
                    'dashboard.reviews.*',
                    'dashboard.products.reviews.*',
                ],
                children: $catalogChildren,
                badge: $this->normalizeBadge([
                    'value' => $catalogBadgeValue,
                    'class' => $catalogBadgeClass,
                ])
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */
        $customersChildren = [];

        if ($this->canAny($user, ['addresses.view'])) {
            $customersChildren[] = $this->childItem(
                title: 'Addresses',
                titleKey: 'site.Addresses',
                routeName: 'dashboard.addresses.index',
                routePatterns: ['dashboard.addresses.*'],
                icon: 'fas fa-map-marker-alt',
                badge: $this->normalizeBadge([
                    'value' => $addressesCount,
                    'class' => 'badge badge-secondary',
                ])
            );
        }

        if ($this->canAny($user, ['wishlists.view'])) {
            $customersChildren[] = $this->childItem(
                title: 'Wishlist Activity',
                titleKey: 'site.Wishlist Activity',
                routeName: 'dashboard.wishlists.index',
                routePatterns: ['dashboard.wishlists.*'],
                icon: 'fas fa-heart',
                badge: $this->normalizeBadge([
                    'value' => $wishlistsCount,
                    'class' => 'badge badge-danger',
                ])
            );
        }

        if ($this->canAny($user, ['carts.view'])) {
            $customersChildren[] = $this->childItem(
                title: 'Carts',
                titleKey: 'site.Carts',
                routeName: 'dashboard.carts.index',
                routePatterns: ['dashboard.carts.*'],
                icon: 'fas fa-shopping-basket',
                badge: $this->normalizeBadge([
                    'value' => $cartsCount,
                    'class' => 'badge badge-warning',
                ])
            );
        }

        if ($this->canAny($user, ['carts.view']) && $this->routeExists('dashboard.carts.abandoned')) {
            $customersChildren[] = $this->childItem(
                title: 'Abandoned Carts',
                titleKey: 'site.Abandoned Carts',
                routeName: 'dashboard.carts.abandoned',
                routePatterns: ['dashboard.carts.abandoned'],
                icon: 'fas fa-cart-arrow-down',
                badge: $this->normalizeBadge([
                    'value' => $abandonedCartsCount,
                    'class' => 'badge badge-dark',
                ])
            );
        }

        if ($this->canAny($user, ['abandoned-cart-recoveries.view']) && $this->routeExists('dashboard.abandoned-cart-recoveries.index')) {
            $customersChildren[] = $this->childItem(
                title: 'Cart Recovery',
                titleKey: 'site.Cart Recovery',
                routeName: 'dashboard.abandoned-cart-recoveries.index',
                routePatterns: ['dashboard.abandoned-cart-recoveries.*'],
                icon: 'fas fa-envelope-open-text',
                badge: $this->normalizeBadge([
                    'value' => $abandonedCartRecoveriesCount,
                    'class' => 'badge badge-warning',
                ])
            );
        }

        if (! empty(array_filter($customersChildren))) {
            $customersBadgeValue = $addressesCount + $wishlistsCount + $cartsCount + $abandonedCartRecoveriesCount;

            $this->pushItem($items, $this->treeItem(
                title: 'Customers',
                titleKey: 'site.Customers',
                icon: 'fas fa-user-friends',
                routePatterns: [
                    'dashboard.addresses.*',
                    'dashboard.wishlists.*',
                    'dashboard.carts.*',
                    'dashboard.abandoned-cart-recoveries.*',
                ],
                children: $customersChildren,
                badge: $this->normalizeBadge([
                    'value' => $customersBadgeValue,
                    'class' => 'badge badge-primary',
                ])
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Content
        |--------------------------------------------------------------------------
        */
        $contentChildren = [];

        if ($this->canAny($user, ['blogs.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Blogs',
                titleKey: 'site.Blogs',
                routeName: 'dashboard.blogs.index',
                routePatterns: ['dashboard.blogs.*'],
                icon: 'fas fa-blog'
            );
        }

        if ($this->canAny($user, ['blog-categories.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Blog Categories',
                titleKey: 'site.Blog Categories',
                routeName: 'dashboard.blog-categories.index',
                routePatterns: ['dashboard.blog-categories.*'],
                icon: 'fas fa-folder-open'
            );
        }

        if ($this->canAny($user, ['blog-comments.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Comments',
                titleKey: 'site.Comments',
                routeName: 'dashboard.blog-comments.index',
                routePatterns: ['dashboard.blog-comments.*'],
                icon: 'fas fa-comments'
            );
        }

        if ($this->canAny($user, ['pages.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Static Pages',
                titleKey: 'site.Static Pages',
                routeName: 'dashboard.pages.index',
                routePatterns: ['dashboard.pages.*'],
                icon: 'fas fa-file-alt'
            );
        }

        if ($this->canAny($user, ['contact.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Contact Messages',
                titleKey: 'site.Contact Messages',
                routeName: 'dashboard.contact-messages.index',
                routePatterns: ['dashboard.contact-messages.*'],
                icon: 'fas fa-envelope',
                badge: $this->normalizeBadge([
                    'value' => $unreadMessagesCount,
                    'class' => 'badge badge-danger',
                ])
            );
        }

        if (! empty(array_filter($contentChildren))) {
            $this->pushItem($items, $this->treeItem(
                title: 'Content',
                titleKey: 'site.Content',
                icon: 'fas fa-copy',
                routePatterns: [
                    'dashboard.blogs.*',
                    'dashboard.blog-categories.*',
                    'dashboard.blog-comments.*',
                    'dashboard.pages.*',
                    'dashboard.contact-messages.*',
                ],
                children: $contentChildren,
                badge: $this->normalizeBadge([
                    'value' => $unreadMessagesCount,
                    'class' => 'badge badge-danger',
                ])
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Access Control
        |--------------------------------------------------------------------------
        */
        $aclChildren = [];

        if ($this->canAny($user, ['users.view'])) {
            $aclChildren[] = $this->childItem(
                title: 'Users',
                titleKey: 'site.Users',
                routeName: 'dashboard.users.index',
                routePatterns: ['dashboard.users.*'],
                icon: 'fas fa-users-cog',
                badge: $this->normalizeBadge([
                    'value' => $usersCount,
                    'class' => 'badge badge-info',
                ])
            );
        }

        if ($this->canAny($user, ['roles.view'])) {
            $aclChildren[] = $this->childItem(
                title: 'Roles',
                titleKey: 'site.Roles',
                routeName: 'dashboard.roles.index',
                routePatterns: ['dashboard.roles.*'],
                icon: 'fas fa-user-tag'
            );
        }

        if ($this->canAny($user, ['permissions.view'])) {
            $aclChildren[] = $this->childItem(
                title: 'Permissions',
                titleKey: 'site.Permissions',
                routeName: 'dashboard.permissions.index',
                routePatterns: ['dashboard.permissions.*'],
                icon: 'fas fa-key'
            );
        }

        if (! empty(array_filter($aclChildren))) {
            $this->pushItem($items, $this->treeItem(
                title: 'Access Control',
                titleKey: 'site.Access Control',
                icon: 'fas fa-shield-alt',
                routePatterns: [
                    'dashboard.users.*',
                    'dashboard.roles.*',
                    'dashboard.permissions.*',
                ],
                children: $aclChildren
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | System
        |--------------------------------------------------------------------------
        */
        $systemChildren = [];

        if ($this->canAny($user, ['audit.view'])) {
            $systemChildren[] = $this->childItem(
                title: 'Audit Logs',
                titleKey: 'site.Audit Logs',
                routeName: 'dashboard.audit.index',
                routePatterns: ['dashboard.audit.*'],
                icon: 'fas fa-history'
            );
        }

        if ($this->routeExists('dashboard.error-logs.index') && $this->canAny($user, ['audit.view'])) {
            $systemChildren[] = $this->childItem(
                title: 'Error Logs',
                titleKey: 'site.Error Logs',
                routeName: 'dashboard.error-logs.index',
                routePatterns: ['dashboard.error-logs.*'],
                icon: 'fas fa-bug'
            );
        }

        if ($this->routeExists('dashboard.backups.index') && $this->canAny($user, ['audit.view'])) {
            $systemChildren[] = $this->childItem(
                title: 'Backup History',
                titleKey: 'site.Backup History',
                routeName: 'dashboard.backups.index',
                routePatterns: ['dashboard.backups.*'],
                icon: 'fas fa-database'
            );
        }

        if ($this->routeExists('dashboard.settings.index') && $this->canAny($user, ['audit.view'])) {
            $systemChildren[] = $this->childItem(
                title: 'Settings',
                titleKey: 'site.Settings',
                routeName: 'dashboard.settings.index',
                routePatterns: ['dashboard.settings.*'],
                icon: 'fas fa-cog'
            );
        }

        if ($this->routeExists('dashboard.uat.index') && $this->canAny($user, ['audit.view'])) {
            $systemChildren[] = $this->childItem(
                title: 'UAT',
                titleKey: 'site.UAT',
                routeName: 'dashboard.uat.index',
                routePatterns: ['dashboard.uat.*'],
                icon: 'fas fa-vial'
            );
        }

        if ($this->routeExists('dashboard.privacy-requests.index') && $this->canAny($user, ['audit.view'])) {
            $systemChildren[] = $this->childItem(
                title: 'Privacy Requests',
                titleKey: 'site.Privacy Requests',
                routeName: 'dashboard.privacy-requests.index',
                routePatterns: ['dashboard.privacy-requests.*'],
                icon: 'fas fa-user-shield'
            );
        }

        if ($this->routeExists('dashboard.retention-policies.index') && $this->canAny($user, ['audit.view'])) {
            $systemChildren[] = $this->childItem(
                title: 'Retention Policies',
                titleKey: 'site.Retention Policies',
                routeName: 'dashboard.retention-policies.index',
                routePatterns: ['dashboard.retention-policies.*'],
                icon: 'fas fa-shield-alt'
            );
        }

        if (! empty(array_filter($systemChildren))) {
            $this->pushItem($items, $this->treeItem(
                title: 'System',
                titleKey: 'site.System',
                icon: 'fas fa-cogs',
                routePatterns: [
                    'dashboard.audit.*',
                    'dashboard.error-logs.*',
                    'dashboard.backups.*',
                    'dashboard.settings.*',
                    'dashboard.uat.*',
                    'dashboard.privacy-requests.*',
                    'dashboard.retention-policies.*',
                ],
                children: $systemChildren
            ));
        }

        return [
            'brand' => $brand,
            'user'  => $sidebarUser,
            'items' => array_values(array_filter($items)),
        ];
    }

    protected function pushItem(array &$items, ?array $item): void
    {
        if (! empty($item)) {
            $items[] = $item;
        }
    }

    protected function singleItem(
        string $title,
        string $titleKey,
        string $icon,
        string $routeName,
        array $routePatterns = [],
        ?array $badge = null
    ): ?array {
        if (! $this->routeExists($routeName)) {
            return null;
        }

        return [
            'type'      => 'single',
            'title'     => $title,
            'title_key' => $titleKey,
            'icon'      => $icon,
            'url'       => $this->safeRoute($routeName),
            'active'    => $this->isActive($routePatterns),
            'badge'     => $this->normalizeBadge($badge),
        ];
    }

    protected function childItem(
        string $title,
        string $titleKey,
        string $routeName,
        array $routePatterns = [],
        ?string $icon = null,
        ?array $badge = null
    ): ?array {
        if (! $this->routeExists($routeName)) {
            return null;
        }

        return [
            'title'     => $title,
            'title_key' => $titleKey,
            'url'       => $this->safeRoute($routeName),
            'active'    => $this->isActive($routePatterns),
            'icon'      => $icon,
            'badge'     => $this->normalizeBadge($badge),
        ];
    }

    protected function treeItem(
        string $title,
        string $titleKey,
        string $icon,
        array $routePatterns = [],
        array $children = [],
        ?array $badge = null
    ): ?array {
        $children = array_values(array_filter($children));

        if (empty($children)) {
            return null;
        }

        $active = $this->isActive($routePatterns);

        return [
            'type'      => 'tree',
            'title'     => $title,
            'title_key' => $titleKey,
            'icon'      => $icon,
            'url'       => '#',
            'active'    => $active,
            'open'      => $active,
            'children'  => $children,
            'badge'     => $this->normalizeBadge($badge),
        ];
    }

    protected function normalizeBadge(?array $badge): ?array
    {
        if (! $badge) {
            return null;
        }

        $value = (int) ($badge['value'] ?? 0);

        if ($value <= 0) {
            return null;
        }

        return [
            'value' => $value,
            'class' => $badge['class'] ?? 'badge badge-info',
        ];
    }

    protected function canAny(User $user, array $permissions = []): bool
    {
        if ($user->hasRole('owner')) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    protected function isActive(array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function routeExists(string $name): bool
    {
        return Route::has($name);
    }

    protected function safeRoute(string $name): string
    {
        return $this->routeExists($name) ? route($name) : '#';
    }
}
