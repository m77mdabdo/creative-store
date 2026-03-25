<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PaymentIndexRequest;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use App\Models\Refund;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(PaymentIndexRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $query = Payment::query()
            ->with([
                'order:id,order_number,user_id,total,status,payment_status,created_at',
                'order.user:id,name,email',
                'refunds:id,payment_id,amount,status,created_at',
            ]);

        if (! empty($filters['q'])) {
            $query->search($filters['q']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['gateway'])) {
            $query->where('gateway', $filters['gateway']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['amount_min']) && $filters['amount_min'] !== null && $filters['amount_min'] !== '') {
            $query->where('amount', '>=', $filters['amount_min']);
        }

        if (isset($filters['amount_max']) && $filters['amount_max'] !== null && $filters['amount_max'] !== '') {
            $query->where('amount', '<=', $filters['amount_max']);
        }

        match ($filters['sort'] ?? 'newest') {
            'oldest'      => $query->orderBy('id', 'asc'),
            'amount_asc'  => $query->orderBy('amount', 'asc')->orderBy('id', 'desc'),
            'amount_desc' => $query->orderBy('amount', 'desc')->orderBy('id', 'desc'),
            default       => $query->orderByDesc('id'),
        };

        $payments = $query->paginate($perPage)->withQueryString();

        $statsBase = Payment::query();

        $stats = [
            'total_payments'     => (clone $statsBase)->count(),
            'paid_payments'      => (clone $statsBase)->paidLike()->count(),
            'pending_payments'   => (clone $statsBase)->pending()->count(),
            'failed_payments'    => (clone $statsBase)->failedLike()->count(),
            'captured_amount'    => (float) (clone $statsBase)->paidLike()->sum('amount'),
            'refunded_amount'    => class_exists(Refund::class) ? (float) Refund::query()->sum('amount') : 0,
            'avg_payment_amount' => (float) ((clone $statsBase)->avg('amount') ?? 0),
        ];

        $gateways = Payment::query()
            ->whereNotNull('gateway')
            ->where('gateway', '!=', '')
            ->select('gateway')
            ->distinct()
            ->orderBy('gateway')
            ->pluck('gateway');

        return view('dashboard.payments.index', compact(
            'payments',
            'stats',
            'gateways'
        ));
    }

    public function show(Payment $payment): View
    {
        $payment->load([
            'order.user:id,name,email,phone',
            'order.items',
            'refunds',
        ]);

        $recentWebhookLogs = PaymentWebhookLog::query()
            ->where('gateway', $payment->gateway)
            ->latest('id')
            ->take(10)
            ->get();

        return view('dashboard.payments.show', compact(
            'payment',
            'recentWebhookLogs'
        ));
    }
}
