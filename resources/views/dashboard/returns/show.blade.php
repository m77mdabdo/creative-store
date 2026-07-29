@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Return #{{ $returnRequest->id }}</h1></div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('dashboard.returns.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="220">{{ __('Return ID') }}</th><td>{{ $returnRequest->id }}</td></tr>
                        <tr><th>{{ __('Order') }}</th><td>{{ $returnRequest->order?->order_number ?? '—' }}</td></tr>
                        <tr><th>{{ __('Customer') }}</th><td>{{ $returnRequest->order?->user?->name ?? __('Guest') }}</td></tr>
                        <tr><th>{{ __('Status') }}</th><td><span class="{{ $returnRequest->status_badge_class }}">{{ status_label($returnRequest->status) }}</span></td></tr>
                        <tr><th>{{ __('Reason') }}</th><td>{{ $returnRequest->reason ?: '—' }}</td></tr>
                        <tr><th>{{ __('Refund Shipping') }}</th><td>{{ $returnRequest->refund_shipping ? __('Yes') : __('No') }}</td></tr>
                        <tr><th>{{ __('Refund Tax') }}</th><td>{{ $returnRequest->refund_tax ? __('Yes') : __('No') }}</td></tr>
                        <tr><th>{{ __('Shipping Refund Amount') }}</th><td>{{ number_format((float) ($returnRequest->shipping_refund_amount ?? 0), 2) }}</td></tr>
                        <tr><th>{{ __('Tax Refund Amount') }}</th><td>{{ number_format((float) ($returnRequest->tax_refund_amount ?? 0), 2) }}</td></tr>
                        <tr><th>{{ __('Items Refund Total') }}</th><td>{{ number_format($returnRequest->items_refund_total, 2) }}</td></tr>
                        <tr><th>{{ __('Grand Refund Total') }}</th><td>{{ number_format($returnRequest->grand_refund_total, 2) }}</td></tr>
                        <tr><th>{{ __('Requested At') }}</th><td>{{ $returnRequest->requested_at?->format('Y-m-d h:i A') ?: '—' }}</td></tr>
                        <tr><th>{{ __('Processed At') }}</th><td>{{ $returnRequest->processed_at?->format('Y-m-d h:i A') ?: '—' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">{{ __('Returned Items') }}</h3></div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Order Item') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Condition Note') }}</th>
                                <th>{{ __('Refund Amount') }}</th>
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
                                    <td colspan="5" class="text-center text-muted py-4">{{ __('No items found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card card-outline card-success">
                <div class="card-header"><h3 class="card-title">{{ __('Linked Refunds') }}</h3></div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Payment') }}</th>
                                <th>{{ __('Refund Txn') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Processed At') }}</th>
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
                                    <td><span class="{{ $refund->status_badge_class }}">{{ status_label($refund->status) }}</span></td>
                                    <td>{{ $refund->processed_at?->format('Y-m-d h:i A') ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">{{ __('No refunds linked.') }}</td>
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
