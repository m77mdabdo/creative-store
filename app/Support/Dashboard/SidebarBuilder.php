<?php

namespace App\Support\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class SidebarBuilder
{
    public function build(?User $user, array $counters = []): array
    {
        if (!$user) {
            return [
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
        }

        $pendingOrdersCount = (int) ($counters['dashboardPendingOrdersCount'] ?? 0);
        $unreadMessagesCount = (int) ($counters['dashboardUnreadMessagesCount'] ?? 0);

        $items = [];

        $items[] = $this->singleItem(
            title: 'Dashboard',
            icon: 'fas fa-tachometer-alt',
            routeName: 'dashboard.home',
            routePatterns: ['dashboard.home']
        );

        if ($this->can($user, ['users.view'])) {
            $items[] = $this->singleItem(
                title: 'Users',
                icon: 'fas fa-users',
                routeName: 'dashboard.users.index',
                routePatterns: ['dashboard.users.*']
            );
        }

        $salesChildren = [];

        if ($this->can($user, ['orders.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Orders',
                routeName: 'dashboard.orders.index',
                routePatterns: ['dashboard.orders.*']
            );
        }

        if ($this->routeExists('dashboard.payments.index') && $this->can($user, ['payments.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Payments',
                routeName: 'dashboard.payments.index',
                routePatterns: ['dashboard.payments.*']
            );
        }

        if ($this->routeExists('dashboard.refunds.index') && $this->can($user, ['refunds.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Refunds',
                routeName: 'dashboard.refunds.index',
                routePatterns: ['dashboard.refunds.*']
            );
        }

        if ($this->routeExists('dashboard.returns.index') && $this->can($user, ['returns.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Returns',
                routeName: 'dashboard.returns.index',
                routePatterns: ['dashboard.returns.*']
            );
        }

        if ($this->routeExists('dashboard.coupons.index') && $this->can($user, ['coupons.view'])) {
            $salesChildren[] = $this->childItem(
                title: 'Coupons',
                routeName: 'dashboard.coupons.index',
                routePatterns: ['dashboard.coupons.*']
            );
        }

        if (!empty($salesChildren)) {
            $items[] = $this->treeItem(
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
                badge: $pendingOrdersCount > 0 ? [
                    'value' => $pendingOrdersCount,
                    'class' => 'badge badge-warning',
                ] : null
            );
        }

        $catalogChildren = [];

        if ($this->can($user, ['categories.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Categories',
                routeName: 'dashboard.categories.index',
                routePatterns: ['dashboard.categories.*']
            );
        }

        if ($this->can($user, ['products.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Products',
                routeName: 'dashboard.products.index',
                routePatterns: ['dashboard.products.*']
            );
        }

        if ($this->routeExists('dashboard.attributes.index') && $this->can($user, ['attributes.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Attributes',
                routeName: 'dashboard.attributes.index',
                routePatterns: ['dashboard.attributes.*']
            );
        }

        if ($this->routeExists('dashboard.attribute-values.index') && $this->can($user, ['attribute-values.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Attribute Values',
                routeName: 'dashboard.attribute-values.index',
                routePatterns: ['dashboard.attribute-values.*']
            );
        }

        if ($this->routeExists('dashboard.product-variants.index') && $this->can($user, ['product-variants.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Product Variants',
                routeName: 'dashboard.product-variants.index',
                routePatterns: ['dashboard.product-variants.*']
            );
        }

        if ($this->routeExists('dashboard.product-reviews.index') && $this->can($user, ['product-reviews.view'])) {
            $catalogChildren[] = $this->childItem(
                title: 'Product Reviews',
                routeName: 'dashboard.product-reviews.index',
                routePatterns: ['dashboard.product-reviews.*']
            );
        }

        if (!empty($catalogChildren)) {
            $items[] = $this->treeItem(
                title: 'Catalog',
                icon: 'fas fa-boxes',
                routePatterns: [
                    'dashboard.categories.*',
                    'dashboard.products.*',
                    'dashboard.attributes.*',
                    'dashboard.attribute-values.*',
                    'dashboard.product-variants.*',
                    'dashboard.product-reviews.*',
                ],
                children: $catalogChildren
            );
        }

        $contentChildren = [];

        if ($this->routeExists('dashboard.blogs.index') && $this->can($user, ['blogs.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Blogs',
                routeName: 'dashboard.blogs.index',
                routePatterns: ['dashboard.blogs.*']
            );
        }

        if ($this->routeExists('dashboard.blog-categories.index') && $this->can($user, ['blog-categories.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Blog Categories',
                routeName: 'dashboard.blog-categories.index',
                routePatterns: ['dashboard.blog-categories.*']
            );
        }

        if ($this->routeExists('dashboard.blog-comments.index') && $this->can($user, ['blog-comments.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Comments',
                routeName: 'dashboard.blog-comments.index',
                routePatterns: ['dashboard.blog-comments.*']
            );
        }

        if ($this->routeExists('dashboard.pages.index') && $this->can($user, ['pages.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Static Pages',
                routeName: 'dashboard.pages.index',
                routePatterns: ['dashboard.pages.*']
            );
        }

        if ($this->can($user, ['contact.view'])) {
            $contentChildren[] = $this->childItem(
                title: 'Contact Messages',
                routeName: 'dashboard.contact-messages.index',
                routePatterns: ['dashboard.contact-messages.*']
            );
        }

        if (!empty($contentChildren)) {
            $items[] = $this->treeItem(
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
                badge: $unreadMessagesCount > 0 ? [
                    'value' => $unreadMessagesCount,
                    'class' => 'badge badge-danger',
                ] : null
            );
        }

        $systemChildren = [];

        if ($this->can($user, ['audit.view'])) {
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

        if (!empty($systemChildren)) {
            $items[] = $this->treeItem(
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
            );
        }

        return [
            'brand' => [
                'title' => 'Creative Store',
                'logo' => asset('AdminLTE/dist/img/AdminLTELogo.png'),
                'route' => route('dashboard.home'),
            ],
            'user' => [
                'name' => $user->name ?: 'Dashboard User',
                'avatar' => asset('AdminLTE/dist/img/user2-160x160.jpg'),
            ],
            'items' => $items,
        ];
    }

    protected function singleItem(
        string $title,
        string $icon,
        string $routeName,
        array $routePatterns = [],
        ?array $badge = null
    ): array {
        return [
            'type' => 'single',
            'title' => $title,
            'icon' => $icon,
            'url' => $this->safeRoute($routeName),
            'active' => $this->isActive($routePatterns),
            'badge' => $badge,
        ];
    }

    protected function childItem(
        string $title,
        string $routeName,
        array $routePatterns = []
    ): array {
        return [
            'title' => $title,
            'url' => $this->safeRoute($routeName),
            'active' => $this->isActive($routePatterns),
        ];
    }

    protected function treeItem(
        string $title,
        string $icon,
        array $routePatterns = [],
        array $children = [],
        ?array $badge = null
    ): array {
        return [
            'type' => 'tree',
            'title' => $title,
            'icon' => $icon,
            'open' => $this->isActive($routePatterns),
            'active' => $this->isActive($routePatterns),
            'children' => $children,
            'badge' => $badge,
        ];
    }

    protected function can(User $user, array $permissions = []): bool
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
