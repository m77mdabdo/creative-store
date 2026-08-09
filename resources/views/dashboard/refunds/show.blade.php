@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Refund #{{ $refund->id }}</h1></div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('dashboard.refunds.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="220">{{ __('Refund ID') }}</th><td>{{ $refund->id }}</td></tr>
                        <tr><th>{{ __('Order') }}</th><td>{{ $refund->payment?->order?->order_number ?? '—' }}</td></tr>
                        <tr><th>{{ __('Payment Transaction') }}</th><td>{{ $refund->payment?->transaction_id ?? '—' }}</td></tr>
                        <tr><th>{{ __('Refund Transaction') }}</th><td>{{ $refund->transaction_id ?? '—' }}</td></tr>
                        <tr><th>{{ __('Amount') }}</th><td>{{ number_format((float) $refund->amount, 2) }}</td></tr>
                        <tr><th>{{ __('Status') }}</th><td><span class="{{ $refund->status_badge_class }}">{{ status_label($refund->status) }}</span></td></tr>
                        <tr><th>{{ __('Processed At') }}</th><td>{{ $refund->processed_at?->translatedFormat('Y-m-d h:i A') ?: '—' }}</td></tr>
                    </table>
                </div>
            </div>

            @if($refund->returnRequest)
                <div class="card card-outline card-secondary">
                    <div class="card-header"><h3 class="card-title">{{ __('Linked Return Request') }}</h3></div>
                    <div class="card-body">
                        <p><strong>{{ __('Return ID:') }}</strong> {{ $refund->returnRequest->id }}</p>
                        <p><strong>{{ __('Status:') }}</strong> {{ $refund->returnRequest->status }}</p>
                        <p><strong>{{ __('Reason:') }}</strong> {{ $refund->returnRequest->reason ?: '—' }}</p>
                        <a href="{{ route('dashboard.returns.show', $refund->returnRequest) }}" class="btn btn-outline-primary btn-sm">{{ __('View Return') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
