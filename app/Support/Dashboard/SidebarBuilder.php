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
            'logo'  => asset('AdminLTE/dist/img/AdminLTELogo.png'),
            'route' => $this->safeRoute('dashboard.home'),
        ];

        $sidebarUser = [
            'name'   => $user?->name ?: 'Dashboard User',
            'avatar' => asset('AdminLTE/dist/img/user2-160x160.jpg'),
        ];

        if (! $user) {
            return [
                'brand' => $brand,
                'user'  => $sidebarUser,
                'items' => [],
            ];
        }

        $pendingOrdersCount   = max(0, (int) ($counters['dashboardPendingOrdersCount'] ?? 0));
        $unreadMessagesCount  = max(0, (int) ($counters['dashboardUnreadMessagesCount'] ?? 0));
        $productsCount        = max(0, (int) ($counters['dashboardProductsCount'] ?? 0));
        $pendingReviewsCount  = max(0, (int) ($counters['dashboardPendingReviewsCount'] ?? 0));

        $items = [];

        // Dashboard
        $this->pushItem($items, $this->singleItem(
            title: 'Dashboard',
            icon: 'fas fa-tachometer-alt',
            routeName: 'dashboard.home',
            routePatterns: ['dashboard.home']
        ));

        // Users
        if ($this->canAny($user, ['users.view'])) {
            $this->pushItem($items, $this->singleItem(
                title: 'Users',
                icon: 'fas fa-users',
                routeName: 'dashboard.users.index',
                routePatterns: ['dashboard.users.*']
            ));
        }

        // Sales
        $salesChildren = [];

        if ($this->canAny($user, ['orders.view']) && $this->routeExists('dashboard.orders.index')) {
            $salesChildren[] = $this->childItem(
                title: 'Orders',
                routeName: 'dashboard.orders.index',
                routePatterns: ['dashboard.orders.*']
            );
        }

        if ($this->canAny($user, ['payments.view']) && $this->routeExists('dashboard.payments.index')) {
            $salesChildren[] = $this->childItem(
                title: 'Payments',
                routeName: 'dashboard.payments.index',
                routePatterns: ['dashboard.payments.*']
            );
        }

        if ($this->canAny($user, ['refunds.view']) && $this->routeExists('dashboard.refunds.index')) {
            $salesChildren[] = $this->childItem(
                title: 'Refunds',
                routeName: 'dashboard.refunds.index',
                routePatterns: ['dashboard.refunds.*']
            );
        }

        if ($this->canAny($user, ['returns.view']) && $this->routeExists('dashboard.returns.index')) {
            $salesChildren[] = $this->childItem(
                title: 'Returns',
                routeName: 'dashboard.returns.index',
                routePatterns: ['dashboard.returns.*']
            );
        }

        if ($this->canAny($user, ['coupons.view']) && $this->routeExists('dashboard.coupons.index')) {
            $salesChildren[] = $this->childItem(
                title: 'Coupons',
                routeName: 'dashboard.coupons.index',
                routePatterns: ['dashboard.coupons.*']
            );
        }

        if (! empty($salesChildren)) {
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
                    'value' => $pendingOrdersCount,
                    'class' => 'badge badge-warning',
                ])
            ));
        }

        // Catalog
        // Catalog
        $catalogChildren = [];

        if ($this->canAny($user, ['categories.view']) && $this->routeExists('dashboard.categories.index')) {
            $catalogChildren[] = $this->childItem(
                title: 'Categories',
                routeName: 'dashboard.categories.index',
                routePatterns: ['dashboard.categories.*']
            );
        }

        if ($this->canAny($user, ['products.view']) && $this->routeExists('dashboard.products.index')) {
            $catalogChildren[] = $this->childItem(
                title: 'Products',
                routeName: 'dashboard.products.index',
                routePatterns: ['dashboard.products.*']
            );
        }

        if ($this->canAny($user, ['attributes.view']) && $this->routeExists('dashboard.attributes.index')) {
            $catalogChildren[] = $this->childItem(
                title: 'Attributes',
                routeName: 'dashboard.attributes.index',
                routePatterns: ['dashboard.attributes.*']
            );
        }

        if ($this->canAny($user, ['attribute-values.view']) && $this->routeExists('dashboard.attribute-values.index')) {
            $catalogChildren[] = $this->childItem(
                title: 'Attribute Values',
                routeName: 'dashboard.attribute-values.index',
                routePatterns: ['dashboard.attribute-values.*']
            );
        }

        if ($this->canAny($user, ['product-variants.view']) && $this->routeExists('dashboard.product-variants.index')) {
            $catalogChildren[] = $this->childItem(
                title: 'Product Variants',
                routeName: 'dashboard.product-variants.index',
                routePatterns: ['dashboard.product-variants.*']
            );
        }

        if ($this->canAny($user, ['reviews.view']) && $this->routeExists('dashboard.reviews.index')) {
            $catalogChildren[] = $this->childItem(
                title: 'Product Reviews',
                routeName: 'dashboard.reviews.index',
                routePatterns: ['dashboard.reviews.*']
            );
        }

        if (! empty($catalogChildren)) {
            $catalogBadgeValue = $pendingReviewsCount > 0 ? $pendingReviewsCount : $productsCount;
            $catalogBadgeClass = $pendingReviewsCount > 0 ? 'badge badge-warning' : 'badge badge-info';

            $this->pushItem($items, $this->treeItem(
                title: 'Catalog',
                icon: 'fas fa-boxes',
                routePatterns: [
                    'dashboard.categories.*',
                    'dashboard.products.*',
                    'dashboard.attributes.*',
                    'dashboard.attribute-values.*',
                    'dashboard.product-variants.*',
                    'dashboard.reviews.*',
                ],
                children: $catalogChildren,
                badge: $this->normalizeBadge([
                    'value' => $catalogBadgeValue,
                    'class' => $catalogBadgeClass,
                ])
            ));
        }

        // Content
        $contentChildren = [];

        if ($this->canAny($user, ['blogs.view']) && $this->routeExists('dashboard.blogs.index')) {
            $contentChildren[] = $this->childItem(
                title: 'Blogs',
                routeName: 'dashboard.blogs.index',
                routePatterns: ['dashboard.blogs.*']
            );
        }

        if ($this->canAny($user, ['blog-categories.view']) && $this->routeExists('dashboard.blog-categories.index')) {
            $contentChildren[] = $this->childItem(
                title: 'Blog Categories',
                routeName: 'dashboard.blog-categories.index',
                routePatterns: ['dashboard.blog-categories.*']
            );
        }

        if ($this->canAny($user, ['blog-comments.view']) && $this->routeExists('dashboard.blog-comments.index')) {
            $contentChildren[] = $this->childItem(
                title: 'Comments',
                routeName: 'dashboard.blog-comments.index',
                routePatterns: ['dashboard.blog-comments.*']
            );
        }

        if ($this->canAny($user, ['pages.view']) && $this->routeExists('dashboard.pages.index')) {
            $contentChildren[] = $this->childItem(
                title: 'Static Pages',
                routeName: 'dashboard.pages.index',
                routePatterns: ['dashboard.pages.*']
            );
        }

        if ($this->canAny($user, ['contact.view']) && $this->routeExists('dashboard.contact-messages.index')) {
            $contentChildren[] = $this->childItem(
                title: 'Contact Messages',
                routeName: 'dashboard.contact-messages.index',
                routePatterns: ['dashboard.contact-messages.*']
            );
        }

        if (! empty($contentChildren)) {
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

        // System
        $systemChildren = [];

        if ($this->canAny($user, ['audit.view']) && $this->routeExists('dashboard.audit.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Audit Logs',
                routeName: 'dashboard.audit.index',
                routePatterns: ['dashboard.audit.*']
            );
        }

        if ($this->routeExists('dashboard.error-logs.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Error Logs',
                routeName: 'dashboard.error-logs.index',
                routePatterns: ['dashboard.error-logs.*']
            );
        }

        if ($this->routeExists('dashboard.backups.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Backup History',
                routeName: 'dashboard.backups.index',
                routePatterns: ['dashboard.backups.*']
            );
        }

        if ($this->routeExists('dashboard.settings.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Settings',
                routeName: 'dashboard.settings.index',
                routePatterns: ['dashboard.settings.*']
            );
        }

        if ($this->routeExists('dashboard.uat.index')) {
            $systemChildren[] = $this->childItem(
                title: 'UAT',
                routeName: 'dashboard.uat.index',
                routePatterns: ['dashboard.uat.*']
            );
        }

        if ($this->routeExists('dashboard.privacy-requests.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Privacy Requests',
                routeName: 'dashboard.privacy-requests.index',
                routePatterns: ['dashboard.privacy-requests.*']
            );
        }

        if ($this->routeExists('dashboard.retention-policies.index')) {
            $systemChildren[] = $this->childItem(
                title: 'Retention Policies',
                routeName: 'dashboard.retention-policies.index',
                routePatterns: ['dashboard.retention-policies.*']
            );
        }

        if (! empty($systemChildren)) {
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
        array $routePatterns = []
    ): ?array {
        if (! $this->routeExists($routeName)) {
            return null;
        }

        return [
            'title'  => $title,
            'url'    => $this->safeRoute($routeName),
            'active' => $this->isActive($routePatterns),
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
