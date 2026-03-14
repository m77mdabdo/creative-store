<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ContactMessage;
use App\Models\DailySalesAggregate;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $lowStockThreshold = 5;

        // Main stats
        $totalSales = Order::where('payment_status', 'paid')->sum('total');
        $ordersCount = Order::count();
        $paidOrdersCount = Order::where('payment_status', 'paid')->count();
        $pendingOrdersCount = Order::whereIn('status', ['pending', 'processing'])->count();
        $productsCount = Product::count();
        $customersCount = User::whereHas('roles', function ($q) {
            $q->where('name', 'customer');
        })->count();

        $lowStockProductsCount = Product::where(function ($q) use ($lowStockThreshold) {
            $q->where('has_variants', false)
              ->whereRaw('COALESCE((SELECT SUM(quantity) FROM inventory_movements WHERE inventory_movements.product_id = products.id), 0) <= ?', [$lowStockThreshold]);
        })->orWhereHas('variants', function ($q) use ($lowStockThreshold) {
            $q->where('stock', '<=', $lowStockThreshold);
        })->count();

        $newReviewsCount = Review::whereDate('created_at', '>=', now()->subDays(7))->count();
        $unreadContactMessagesCount = ContactMessage::where('is_read', false)->count();

        // Latest orders
        $latestOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(8)
            ->get();

        // Top products
        $topProducts = Product::with(['translations', 'primaryImage'])
            ->withSum('orderItems as sold_qty', 'quantity')
            ->orderByDesc('sold_qty')
            ->take(6)
            ->get();

        // Sales chart (last 7 days)
        $days = collect(range(6, 0))->map(function ($i) {
            return Carbon::now()->subDays($i)->format('Y-m-d');
        });

        $aggregateData = DailySalesAggregate::whereBetween('day', [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->day)->format('Y-m-d'));

        $salesChartLabels = [];
        $salesChartData = [];

        foreach ($days as $day) {
            $salesChartLabels[] = Carbon::parse($day)->format('d M');
            $salesChartData[] = (float) ($aggregateData[$day]->net_sales ?? 0);
        }

        // Recent activity
        $recentActivities = AuditLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.home', compact(
            'totalSales',
            'ordersCount',
            'paidOrdersCount',
            'pendingOrdersCount',
            'productsCount',
            'customersCount',
            'lowStockProductsCount',
            'newReviewsCount',
            'unreadContactMessagesCount',
            'latestOrders',
            'topProducts',
            'salesChartLabels',
            'salesChartData',
            'recentActivities'
        ));
    }
}
