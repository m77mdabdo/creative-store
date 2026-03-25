@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Return #{{ $returnRequest->id }}</h1></div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('dashboard.returns.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="220">Return ID</th><td>{{ $returnRequest->id }}</td></tr>
                        <tr><th>Order</th><td>{{ $returnRequest->order?->order_number ?? '—' }}</td></tr>
                        <tr><th>Customer</th><td>{{ $returnRequest->order?->user?->name ?? 'Guest' }}</td></tr>
                        <tr><th>Status</th><td><span class="{{ $returnRequest->status_badge_class }}">{{ ucfirst($returnRequest->status) }}</span></td></tr>
                        <tr><th>Reason</th><td>{{ $returnRequest->reason ?: '—' }}</td></tr>
                        <tr><th>Refund Shipping</th><td>{{ $returnRequest->refund_shipping ? 'Yes' : 'No' }}</td></tr>
                        <tr><th>Refund Tax</th><td>{{ $returnRequest->refund_tax ? 'Yes' : 'No' }}</td></tr>
                        <tr><th>Shipping Refund Amount</th><td>{{ number_format((float) ($returnRequest->shipping_refund_amount ?? 0), 2) }}</td></tr>
                        <tr><th>Tax Refund Amount</th><td>{{ number_format((float) ($returnRequest->tax_refund_amount ?? 0), 2) }}</td></tr>
                        <tr><th>Items Refund Total</th><td>{{ number_format($returnRequest->items_refund_total, 2) }}</td></tr>
                        <tr><th>Grand Refund Total</th><td>{{ number_format($returnRequest->grand_refund_total, 2) }}</td></tr>
                        <tr><th>Requested At</th><td>{{ $returnRequest->requested_at?->format('Y-m-d h:i A') ?: '—' }}</td></tr>
                        <tr><th>Processed At</th><td>{{ $returnRequest->processed_at?->format('Y-m-d h:i A') ?: '—' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">Returned Items</h3></div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order Item</th>
                                <th>Quantity</th>
                                <th>Condition Note</th>
                                <th>Refund Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returnRequest->items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->order_item_id }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->condition_note ?: '—' }}</td>
                                    <td>{{ number_format((float) $item->refund_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card card-outline card-success">
                <div class="card-header"><h3 class="card-title">Linked Refunds</h3></div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Payment</th>
                                <th>Refund Txn</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Processed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returnRequest->refunds as $refund)
                                <tr>
                                    <td>
                                        <a href="{{ route('dashboard.refunds.show', $refund) }}">
                                            {{ $refund->id }}
                                        </a>
                                    </td>
                                    <td>{{ $refund->payment?->transaction_id ?? '—' }}</td>
                                    <td>{{ $refund->transaction_id ?? '—' }}</td>
                                    <td>{{ number_format((float) $refund->amount, 2) }}</td>
                                    <td><span class="{{ $refund->status_badge_class }}">{{ ucfirst($refund->status) }}</span></td>
                                    <td>{{ $refund->processed_at?->format('Y-m-d h:i A') ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No refunds linked.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
