<?php

namespace App\Services\Dashboard;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

class SidebarService
{
    public function build(): array
    {
        $user = auth()->user();

        return [
            'brand' => [
                'route' => route('dashboard.home'),
                'logo' => asset('dashboard/dist/img/AdminLTELogo.png'),
                'title' => 'Creative Store',
            ],

            'user' => [
                'name' => $user?->name ?? 'Dashboard User',
                'avatar' => asset('dashboard/dist/img/user2-160x160.jpg'),
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

                $this->single(
                    title: 'Orders',
                    icon: 'fas fa-shopping-bag',
                    routeName: 'dashboard.orders.index',
                    can: $this->can('orders.view'),
                    badge: [
                        'value' => Order::where('payment_status', 'pending')->count(),
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
                    ]),
                    activePatterns: [
                        'dashboard.categories.*',
                        'dashboard.products.*',
                        'dashboard.attributes.*',
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
        if (empty($children)) {
            return null;
        }

        $active = collect($activePatterns)->contains(fn($pattern) => request()->routeIs($pattern));

        return [
            'type' => 'tree',
            'title' => $title,
            'icon' => $icon,
            'url' => '#',
            'active' => $active,
            'open' => $active,
            'badge' => $this->normalizeBadge($badge),
            'children' => array_values($children),
        ];
    }

    private function child(string $title, string $routeName, bool $can = true): ?array
    {
        if (! $can || ! Route::has($routeName)) {
            return null;
        }

        return [
            'title' => $title,
            'url' => route($routeName),
            'active' => request()->routeIs($routeName),
        ];
    }

    private function normalizeBadge(?array $badge): ?array
    {
        if (! $badge || empty($badge['value'])) {
            return null;
        }

        return [
            'value' => $badge['value'],
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
