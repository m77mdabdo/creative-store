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

        $salesAlertCount = $pendingOrdersCount + $pendingPaymentsCount;

        $items = [];

        $this->pushItem($items, $this->singleItem(
            title: 'Dashboard',
            icon: 'fas fa-tachometer-alt',
            routeName: 'dashboard.home',
            routePatterns: ['dashboard.home']
        ));

        if ($this->canAny($user, ['users.view'])) {
            $this->pushItem($items, $this->singleItem(
                title: 'Users',
                icon: 'fas fa-users',
                routeName: 'dashboard.users.index',
                routePatterns: ['dashboard.users.*']
            ));
        }

        $salesChildren = [];

        if ($this->canAny($user, ['orders.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Orders',
                routeName: 'dashboard.orders.index',
                routePatterns: ['dashboard.orders.*'],
                icon: 'fas fa-shopping-cart'
            );
        }

        if ($this->canAny($user, ['payments.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Payments',
                routeName: 'dashboard.payments.index',
                routePatterns: ['dashboard.payments.*'],
                icon: 'fas fa-credit-card'
            );
        }

        if ($this->canAny($user, ['refunds.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Refunds',
                routeName: 'dashboard.refunds.index',
                routePatterns: ['dashboard.refunds.*'],
                icon: 'fas fa-undo'
            );
        }

        if ($this->canAny($user, ['returns.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Returns',
                routeName: 'dashboard.returns.index',
                routePatterns: ['dashboard.returns.*'],
                icon: 'fas fa-box-open'
            );
        }

        if ($this->canAny($user, ['coupons.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Coupons',
                routeName: 'dashboard.coupons.index',
                routePatterns: ['dashboard.coupons.*'],
                icon: 'fas fa-ticket-alt'
            );
        }

        if (! empty(array_filter($salesChildren))) {
            $this->pushItem($items, $this->treeItem(
                title: 'Sales',
                icon: 'fas fa-shopping-bag',
                routePatterns: [
                    'dashboard.orders.*',
                    'dashboard.payments.*',
                    'dashboard.refunds.*',
                    'dashboard.returns.*',
                    'dashboard.coupons.*',
                ],
                children: $salesChildren,
                badge: $this->normalizeBadge([
                    'value' => $salesAlertCount,
                    'class' => 'badge badge-warning',
                ])
            ));
        }

        $catalogChildren = [];

        if ($this->canAny($user, ['categories.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Categories',
                routeName: 'dashboard.categories.index',
                routePatterns: ['dashboard.categories.*'],
                icon: 'fas fa-tags'
            );
        }

        if ($this->canAny($user, ['products.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Products',
                routeName: 'dashboard.products.index',
                routePatterns: ['dashboard.products.*'],
                icon: 'fas fa-box'
            );
        }

        if ($this->canAny($user, ['attributes.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Attributes',
                routeName: 'dashboard.attributes.index',
                routePatterns: ['dashboard.attributes.*'],
                icon: 'fas fa-sliders-h'
            );
        }

        if ($this->canAny($user, ['reviews.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Product Reviews',
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

        $contentChildren = [];

        if ($this->canAny($user, ['blogs.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Blogs',
                routeName: 'dashboard.blogs.index',
                routePatterns: ['dashboard.blogs.*'],
                icon: 'fas fa-blog'
            );
        }

        if ($this->canAny($user, ['blog-categories.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Blog Categories',
                routeName: 'dashboard.blog-categories.index',
                routePatterns: ['dashboard.blog-categories.*'],
                icon: 'fas fa-folder-open'
            );
        }

        if ($this->canAny($user, ['blog-comments.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Comments',
                routeName: 'dashboard.blog-comments.index',
                routePatterns: ['dashboard.blog-comments.*'],
                icon: 'fas fa-comments'
            );
        }

        if ($this->canAny($user, ['pages.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Static Pages',
                routeName: 'dashboard.pages.index',
                routePatterns: ['dashboard.pages.*'],
                icon: 'fas fa-file-alt'
            );
        }

        if ($this->canAny($user, ['contact.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Contact Messages',
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

        $systemChildren = [];

        if ($this->canAny($user, ['audit.view'])) {
            $systemChildren[] = $this->childItem(
                title: 'Audit Logs',
                routeName: 'dashboard.audit.index',
                routePatterns: ['dashboard.audit.*'],
                icon: 'fas fa-history'
            );
        }

        if ($this->routeExists('dashboard.error-logs.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Error Logs',
                routeName: 'dashboard.error-logs.index',
                routePatterns: ['dashboard.error-logs.*'],
                icon: 'fas fa-bug'
            );
        }

        if ($this->routeExists('dashboard.backups.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Backup History',
                routeName: 'dashboard.backups.index',
                routePatterns: ['dashboard.backups.*'],
                icon: 'fas fa-database'
            );
        }

        if ($this->routeExists('dashboard.settings.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Settings',
                routeName: 'dashboard.settings.index',
                routePatterns: ['dashboard.settings.*'],
                icon: 'fas fa-cog'
            );
        }

        if ($this->routeExists('dashboard.uat.index')) {
            $systemChildren[] = $this->childItem(
                title: 'UAT',
                routeName: 'dashboard.uat.index',
                routePatterns: ['dashboard.uat.*'],
                icon: 'fas fa-vial'
            );
        }

        if ($this->routeExists('dashboard.privacy-requests.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Privacy Requests',
                routeName: 'dashboard.privacy-requests.index',
                routePatterns: ['dashboard.privacy-requests.*'],
                icon: 'fas fa-user-shield'
            );
        }

        if ($this->routeExists('dashboard.retention-policies.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Retention Policies',
                routeName: 'dashboard.retention-policies.index',
                routePatterns: ['dashboard.retention-policies.*'],
                icon: 'fas fa-shield-alt'
            );
        }

        if (! empty(array_filter($systemChildren))) {
            $this->pushItem($items, $this->treeItem(
                title: 'System',
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
        string $icon,
        string $routeName,
        array $routePatterns = [],
        ?array $badge = null
    ): ?array {
        if (! $this->routeExists($routeName)) {
            return null;
        }

        return [
            'type'   => 'single',
            'title'  => $title,
            'icon'   => $icon,
            'url'    => $this->safeRoute($routeName),
            'active' => $this->isActive($routePatterns),
            'badge'  => $this->normalizeBadge($badge),
        ];
    }

    protected function childItem(
        string $title,
        string $routeName,
        array $routePatterns = [],
        ?string $icon = null,
        ?array $badge = null
    ): ?array {
        if (! $this->routeExists($routeName)) {
            return null;
        }

        return [
            'title'  => $title,
            'url'    => $this->safeRoute($routeName),
            'active' => $this->isActive($routePatterns),
            'icon'   => $icon,
            'badge'  => $this->normalizeBadge($badge),
        ];
    }

    protected function treeItem(
        string $title,
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
            'type'     => 'tree',
            'title'    => $title,
            'icon'     => $icon,
            'url'      => '#',
            'active'   => $active,
            'open'     => $active,
            'children' => $children,
            'badge'    => $this->normalizeBadge($badge),
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
        if ($user->hasRole('owner') || $user->hasRole('admin')) {
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
