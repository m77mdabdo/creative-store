<?php

namespace App\Services\Dashboard;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

class SidebarService
{
    public function build(): array
    {
        $user = auth()->user();

        return [
            'brand' => [
                'route' => Route::has('dashboard.home') ? route('dashboard.home') : '#',
                'logo' => asset('AdminLTE/dist/img/logo-01.png'),
                'title' => 'Creative Store',
            ],

            'user' => [
                'name' => $user?->name ?? 'Dashboard User',
                'avatar' => asset('AdminLTE/dist/img/user1-128x128.jpg'),
            ],

            'items' => array_values(array_filter([
                $this->single(
                    title: 'Dashboard',
                    icon: 'fas fa-tachometer-alt',
                    routeName: 'dashboard.home'
                ),

                $this->single(
                    title: 'Users',
                    icon: 'fas fa-users',
                    routeName: 'dashboard.users.index',
                    can: $this->can('users.view')
                ),

                $this->tree(
                    title: 'Sales',
                    icon: 'fas fa-shopping-bag',
                    children: array_filter([
                        $this->child('Orders', 'dashboard.orders.index', $this->can('orders.view')),
                        $this->child('Payments', 'dashboard.payments.index', $this->can('payments.view')),
                        $this->child('Refunds', 'dashboard.refunds.index', $this->can('refunds.view')),
                        $this->child('Returns', 'dashboard.returns.index', $this->can('returns.view')),
                        $this->child('Coupons', 'dashboard.coupons.index', $this->can('coupons.view')),
                    ]),
                    activePatterns: [
                        'dashboard.orders.*',
                        'dashboard.payments.*',
                        'dashboard.refunds.*',
                        'dashboard.returns.*',
                        'dashboard.coupons.*',
                    ],
                    badge: [
                        'value' => Order::where('payment_status', 'pending')->count()
                            + (class_exists(Payment::class) ? Payment::where('status', 'pending')->count() : 0),
                        'class' => 'badge badge-warning'
                    ]
                ),

                $this->tree(
                    title: 'Catalog',
                    icon: 'fas fa-boxes',
                    children: array_filter([
                        $this->child('Categories', 'dashboard.categories.index', $this->can('categories.view')),
                        $this->child('Products', 'dashboard.products.index', $this->can('products.view')),
                        $this->child('Attributes', 'dashboard.attributes.index', $this->can('attributes.view')),
                        $this->child('Product Reviews', 'dashboard.reviews.index', $this->can('reviews.view')),
                    ]),
                    activePatterns: [
                        'dashboard.categories.*',
                        'dashboard.products.*',
                        'dashboard.attributes.*',
                        'dashboard.reviews.*',
                        'dashboard.products.reviews.*',
                    ],
                    badge: [
                        'value' => Product::count(),
                        'class' => 'badge badge-info'
                    ]
                ),

                $this->single(
                    title: 'Contact Messages',
                    icon: 'fas fa-envelope',
                    routeName: 'dashboard.contact-messages.index',
                    can: $this->can('contact.view'),
                    badge: [
                        'value' => ContactMessage::where('is_read', false)->count(),
                        'class' => 'badge badge-danger'
                    ]
                ),

                $this->single(
                    title: 'Audit Logs',
                    icon: 'fas fa-history',
                    routeName: 'dashboard.audit.index',
                    can: $this->can('audit.view')
                ),
            ])),
        ];
    }

    private function single(
        string $title,
        string $icon,
        string $routeName,
        bool $can = true,
        ?array $badge = null
    ): ?array {
        if (! $can || ! Route::has($routeName)) {
            return null;
        }

        return [
            'type' => 'single',
            'title' => $title,
            'icon' => $icon,
            'url' => route($routeName),
            'active' => request()->routeIs($routeName),
            'badge' => $this->normalizeBadge($badge),
        ];
    }

    private function tree(
        string $title,
        string $icon,
        array $children,
        array $activePatterns = [],
        ?array $badge = null
    ): ?array {
        $children = array_values(array_filter($children));

        if (empty($children)) {
            return null;
        }

        $active = collect($activePatterns)->contains(
            fn ($pattern) => request()->routeIs($pattern)
        );

        return [
            'type' => 'tree',
            'title' => $title,
            'icon' => $icon,
            'url' => '#',
            'active' => $active,
            'open' => $active,
            'badge' => $this->normalizeBadge($badge),
            'children' => $children,
        ];
    }

    private function child(
        string $title,
        string $routeName,
        bool $can = true,
        ?array $badge = null
    ): ?array {
        if (! $can || ! Route::has($routeName)) {
            return null;
        }

        return [
            'title' => $title,
            'url' => route($routeName),
            'active' => request()->routeIs($routeName),
            'badge' => $this->normalizeBadge($badge),
        ];
    }

    private function normalizeBadge(?array $badge): ?array
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

    private function can(string $permission): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('owner')
            || $user->hasRole('admin')
            || $user->hasPermission($permission);
    }
}
