<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AbandonedCartRecovery\AbandonedCartRecoveryIndexRequest;
use App\Models\AbandonedCartRecovery;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AbandonedCartRecoveryController extends Controller
{
    public function index(AbandonedCartRecoveryIndexRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $dateFrom = !empty($filters['date_from'])
            ? Carbon::parse($filters['date_from'])->startOfDay()
            : null;

        $dateTo = !empty($filters['date_to'])
            ? Carbon::parse($filters['date_to'])->endOfDay()
            : null;

        $query = AbandonedCartRecovery::query()
            ->with([
                'user:id,name,email,phone',
                'cart:id,user_id,session_id,updated_at,created_at',
                'cart.items:id,cart_id,product_id,variant_id,quantity,price_snapshot',
            ]);

        $query->when(!empty($filters['q']), function ($q) use ($filters) {
            $search = trim((string) $filters['q']);

            $q->where(function ($sub) use ($search) {
                $sub->where('email', 'like', "%{$search}%")
                    ->orWhere('session_id', 'like', "%{$search}%")
                    ->orWhere('recovery_token', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        });

        $query->when(!empty($filters['status']), function ($q) use ($filters) {
            $q->where('status', $filters['status']);
        });

        $query->when(!empty($filters['channel']), function ($q) use ($filters) {
            $q->where('channel', $filters['channel']);
        });

        $query->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom));
        $query->when($dateTo, fn ($q) => $q->where('created_at', '<=', $dateTo));

        $sort = $filters['sort'] ?? 'latest';

        match ($sort) {
            'oldest'       => $query->oldest(),
            'expires_asc'  => $query->orderBy('expires_at'),
            'expires_desc' => $query->orderByDesc('expires_at'),
            default        => $query->latest(),
        };

        $recoveries = $query->paginate($perPage)->withQueryString();

        $baseStatsQuery = AbandonedCartRecovery::query()
            ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('created_at', '<=', $dateTo));

        $totalRecoveries = (clone $baseStatsQuery)->count();
        $sentCount       = (clone $baseStatsQuery)->where('status', 'sent')->count();
        $failedCount     = (clone $baseStatsQuery)->where('status', 'failed')->count();
        $recoveredCount  = (clone $baseStatsQuery)->where('status', 'recovered')->count();
        $expiredCount    = (clone $baseStatsQuery)->where('status', 'expired')->count();
        $pendingCount    = (clone $baseStatsQuery)->where('status', 'pending')->count();

        $recoveryRate = $totalRecoveries > 0
            ? round(($recoveredCount / $totalRecoveries) * 100, 2)
            : 0;

        $recoveredRevenue = (clone $baseStatsQuery)
            ->where('status', 'recovered')
            ->with(['cart.items'])
            ->get()
            ->sum(function ($recovery) {
                return $recovery->cart?->items?->sum(function ($item) {
                    return ((float) $item->price_snapshot) * ((int) $item->quantity);
                }) ?? 0;
            });

        $sentRevenuePotential = (clone $baseStatsQuery)
            ->whereIn('status', ['sent', 'pending', 'failed', 'expired'])
            ->with(['cart.items'])
            ->get()
            ->sum(function ($recovery) {
                return $recovery->cart?->items?->sum(function ($item) {
                    return ((float) $item->price_snapshot) * ((int) $item->quantity);
                }) ?? 0;
            });

        $statusBreakdown = [
            'pending'   => $pendingCount,
            'sent'      => $sentCount,
            'failed'    => $failedCount,
            'recovered' => $recoveredCount,
            'expired'   => $expiredCount,
        ];

        $stats = [
            'total_recoveries'        => $totalRecoveries,
            'recovery_rate'           => $recoveryRate,
            'recovered_revenue'       => $recoveredRevenue,
            'revenue_estimation'      => $sentRevenuePotential,
            'pending_count'           => $pendingCount,
            'sent_count'              => $sentCount,
            'failed_count'            => $failedCount,
            'recovered_count'         => $recoveredCount,
            'expired_count'           => $expiredCount,
        ];

        return view('dashboard.abandoned-cart-recoveries.index', compact(
            'recoveries',
            'filters',
            'stats',
            'statusBreakdown'
        ));
    }

    public function show(AbandonedCartRecovery $recovery): View
    {
        $recovery->load([
            'user:id,name,email,phone',
            'cart:id,user_id,session_id,updated_at,created_at',
            'cart.items:id,cart_id,product_id,variant_id,quantity,price_snapshot,tax_rate_snapshot,created_at',
            'cart.items.product:id,slug,sku,base_price,currency,status',
            'cart.items.product.translations:id,product_id,locale,name',
            'cart.items.variant:id,product_id,sku,price,stock,is_active',
        ]);

        $summary = [
            'cart_items_count' => $recovery->cart?->items?->count() ?? 0,
            'cart_qty_total'   => $recovery->cart?->items?->sum('quantity') ?? 0,
            'cart_subtotal'    => $recovery->cart?->items?->sum(function ($item) {
                return ((float) $item->price_snapshot) * ((int) $item->quantity);
            }) ?? 0,
        ];

        return view('dashboard.abandoned-cart-recoveries.show', compact('recovery', 'summary'));
    }
}
