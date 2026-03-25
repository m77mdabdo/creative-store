@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Payment #{{ $payment->id }}</h1>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('dashboard.payments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Payments
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-8">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Payment Information</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="220">Payment ID</th>
                                    <td>{{ $payment->id }}</td>
                                </tr>
                                <tr>
                                    <th>Order</th>
                                    <td>
                                        @if($payment->order)
                                            <a href="{{ route('dashboard.orders.show', $payment->order) }}">
                                                {{ $payment->order->order_number ?? ('#'.$payment->order->id) }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Gateway</th>
                                    <td>{{ $payment->display_gateway }}</td>
                                </tr>
                                <tr>
                                    <th>Transaction ID</th>
                                    <td>{{ $payment->transaction_id ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Idempotency Key</th>
                                    <td style="word-break: break-all;">{{ $payment->idempotency_key ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td>{{ $payment->display_amount }}</td>
                                </tr>
                                <tr>
                                    <th>Exchange Rate</th>
                                    <td>{{ $payment->exchange_rate ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="{{ $payment->status_badge_class }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Paid At</th>
                                    <td>{{ $payment->paid_at?->format('Y-m-d h:i A') ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $payment->created_at?->format('Y-m-d h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $payment->updated_at?->format('Y-m-d h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Raw Response</h3>
                        </div>
                        <div class="card-body">
                            <pre class="mb-0" style="max-height: 500px; overflow: auto;">{{ json_encode($payment->raw_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Customer</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> {{ $payment->order?->user?->name ?? 'Guest' }}</p>
                            <p><strong>Email:</strong> {{ $payment->order?->user?->email ?? '—' }}</p>
                            <p><strong>Phone:</strong> {{ $payment->order?->user?->phone ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">Order Snapshot</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Order Number:</strong> {{ $payment->order?->order_number ?? '—' }}</p>
                            <p><strong>Order Status:</strong> {{ $payment->order?->status ?? '—' }}</p>
                            <p><strong>Payment Status:</strong> {{ $payment->order?->payment_status ?? '—' }}</p>
                            <p><strong>Order Total:</strong> {{ number_format((float) ($payment->order?->total ?? 0), 2) }}</p>

                            @if($payment->order)
                                <a href="{{ route('dashboard.orders.show', $payment->order) }}" class="btn btn-outline-primary btn-sm">
                                    View Order
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Refunds</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payment->refunds as $refund)
                                        <tr>
                                            <td>{{ $refund->id }}</td>
                                            <td>{{ number_format((float) $refund->amount, 2) }}</td>
                                            <td>{{ $refund->status ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                No refunds found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card card-outline card-dark">
                        <div class="card-header">
                            <h3 class="card-title">Recent Webhook Logs ({{ strtoupper($payment->gateway) }})</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Processed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentWebhookLogs as $log)
                                        <tr>
                                            <td>{{ $log->id }}</td>
                                            <td>{{ $log->event_type ?: '—' }}</td>
                                            <td>
                                                @if($log->processed)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-warning">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                No webhook logs found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>
@endsection
