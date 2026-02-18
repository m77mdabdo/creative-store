<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

/**
 * Storefront rules for an order. Customers only ever act on their own
 * orders; staff use the dashboard, which has its own permission checks.
 */
class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $this->owns($user, $order);
    }

    public function cancel(User $user, Order $order): bool
    {
        return $this->owns($user, $order);
    }

    public function pay(User $user, Order $order): bool
    {
        return $this->owns($user, $order);
    }

    public function downloadInvoice(User $user, Order $order): bool
    {
        return $this->owns($user, $order);
    }

    private function owns(User $user, Order $order): bool
    {
        return $order->user_id !== null && (int) $order->user_id === (int) $user->id;
    }
}
