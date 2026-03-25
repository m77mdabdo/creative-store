<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RefundIndexRequest;
use App\Models\Refund;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function index(RefundIndexRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $query = Refund::query()
            ->with([
                'payment:id,order_id,transaction_id,gateway,amount,currency,status,paid_at',
                'payment.order:id,order_number,user_id,total,status,payment_status',
                'payment.order.user:id,name,email',
                'returnRequest:id,order_id,status,reason,requested_at,processed_at',
            ]);

        $query->search($filters['q'] ?? null);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['amount_min']) && $filters['amount_min'] !== '' && $filters['amount_min'] !== null) {
            $query->where('amount', '>=', $filters['amount_min']);
        }

        if (isset($filters['amount_max']) && $filters['amount_max'] !== '' && $filters['amount_max'] !== null) {
            $query->where('amount', '<=', $filters['amount_max']);
        }

        match ($filters['sort'] ?? 'newest') {
            'oldest'      => $query->orderBy('id', 'asc'),
            'amount_asc'  => $query->orderBy('amount', 'asc')->orderByDesc('id'),
            'amount_desc' => $query->orderBy('amount', 'desc')->orderByDesc('id'),
            default       => $query->orderByDesc('id'),
        };

        $refunds = $query->paginate($perPage)->withQueryString();

        $statsBase = Refund::query();

        $stats = [
            'total_refunds'     => (clone $statsBase)->count(),
            'pending_refunds'   => (clone $statsBase)->where('status', Refund::STATUS_PENDING)->count(),
            'processed_refunds' => (clone $statsBase)->where('status', Refund::STATUS_PROCESSED)->count(),
            'failed_refunds'    => (clone $statsBase)->whereIn('status', [Refund::STATUS_FAILED, Refund::STATUS_CANCELLED])->count(),
            'total_amount'      => (float) (clone $statsBase)->sum('amount'),
        ];

        return view('dashboard.refunds.index', compact('refunds', 'stats'));
    }

    public function show(Refund $refund): View
    {
        $refund->load([
            'payment.order.user:id,name,email,phone',
            'payment.order.items',
            'returnRequest.items.orderItem',
        ]);

        return view('dashboard.refunds.show', compact('refund'));
    }
}
