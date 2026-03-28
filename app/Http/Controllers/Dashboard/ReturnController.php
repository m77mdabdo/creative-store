<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ReturnIndexRequest;
use App\Models\ReturnRequest;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function index(ReturnIndexRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $query = ReturnRequest::query()
            ->with([
                'order:id,order_number,user_id,total,status,payment_status',
                'order.user:id,name,email',
                'items:id,return_id,order_item_id,quantity,condition_note,refund_amount',
                'refunds:id,return_id,payment_id,amount,status,transaction_id,processed_at',
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

        match ($filters['sort'] ?? 'newest') {
            'oldest' => $query->orderBy('id', 'asc'),
            default  => $query->orderByDesc('id'),
        };

        $returns = $query->paginate($perPage)->withQueryString();

        $statsBase = ReturnRequest::query();

        $stats = [
            'total_returns'            => (clone $statsBase)->count(),
            'requested_returns'        => (clone $statsBase)->where('status', ReturnRequest::STATUS_REQUESTED)->count(),
            'approved_returns'         => (clone $statsBase)->where('status', ReturnRequest::STATUS_APPROVED)->count(),
            'received_returns'         => (clone $statsBase)->where('status', ReturnRequest::STATUS_RECEIVED)->count(),
            'refunded_returns'         => (clone $statsBase)->where('status', ReturnRequest::STATUS_REFUNDED)->count(),
            'partial_refunded_returns' => (clone $statsBase)->where('status', ReturnRequest::STATUS_PARTIAL_REFUNDED)->count(),
            'rejected_returns'         => (clone $statsBase)->where('status', ReturnRequest::STATUS_REJECTED)->count(),
        ];

        return view('dashboard.returns.index', compact('returns', 'stats'));
    }

    public function show(ReturnRequest $returnRequest): View
    {
        $returnRequest->load([
            'order.user:id,name,email,phone',
            'order.items',
            'items.orderItem',
            'refunds.payment',
        ]);

        return view('dashboard.returns.show', compact('returnRequest'));
    }
}
