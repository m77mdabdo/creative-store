<?php

namespace App\Services\Dashboard;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Cache;

class DashboardStatsService
{
    public function get(): array
    {
        return Cache::remember('dashboard.stats', now()->addMinutes(5), function () {
            $totalReviews = Review::query()->count();
            $pendingReviews = Review::query()->where('is_approved', false)->count();
            $approvedReviews = Review::query()->where('is_approved', true)->count();
            $averageRating = round((float) Review::query()->avg('rating'), 1);

            return [
                'totalProducts' => Product::query()->count(),
                'activeProducts' => Product::query()->where('status', 'active')->count(),

                'totalOrders' => Order::query()->count(),
                'pendingOrders' => Order::query()->where('payment_status', 'pending')->count(),

                'totalReviews' => $totalReviews,
                'pendingReviews' => $pendingReviews,
                'approvedReviews' => $approvedReviews,
                'averageRating' => $averageRating,

                'unreadMessages' => ContactMessage::query()->where('is_read', false)->count(),
            ];
        });
    }

    public function clear(): void
    {
        Cache::forget('dashboard.stats');
    }
}
