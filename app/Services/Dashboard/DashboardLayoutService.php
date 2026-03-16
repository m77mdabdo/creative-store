<?php

namespace App\Services\Dashboard;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Review;

class DashboardLayoutService
{
    public function data(): array
    {
        return [
            'dashboardPendingOrdersCount' => Order::query()
                ->where('payment_status', 'pending')
                ->count(),

            'dashboardPendingOrders' => Order::query()
                ->with('user')
                ->where('payment_status', 'pending')
                ->latest()
                ->take(5)
                ->get(),

            'dashboardUnreadMessagesCount' => ContactMessage::query()
                ->where('is_read', false)
                ->count(),

            'dashboardUnreadMessages' => ContactMessage::query()
                ->where('is_read', false)
                ->latest()
                ->take(5)
                ->get(),

            'dashboardPendingReviewsCount' => Review::query()
                ->where('is_approved', false)
                ->count(),

            'dashboardPendingReviews' => Review::query()
                ->with(['product.translations', 'user'])
                ->where('is_approved', false)
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
}
