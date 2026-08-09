@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Payments') }}</h1>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('dashboard.orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-shopping-bag mr-1"></i> {{ __('Orders') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total_payments']) }}</h3>
                            <p>{{ __('Total Payments') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-credit-card"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['paid_payments']) }}</h3>
                            <p>{{ __('Paid / Captured') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['pending_payments']) }}</h3>
                            <p>{{ __('Pending') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($stats['failed_payments']) }}</h3>
                            <p>{{ __('Failed / Cancelled') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($stats['captured_amount'], 2) }}</h3>
                            <p>{{ __('Captured Amount') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ number_format($stats['refunded_amount'], 2) }}</h3>
                            <p>{{ __('Refunded Amount') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-undo"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.payments.index') }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ request('q') }}"
                                    class="form-control"
                                    placeholder="{{ __('Search transaction / idempotency / order / customer') }}"
                                >
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @foreach(\App\Models\Payment::statuses() as $status)
                                        <option value="{{ $status }}" @selected(request('status') === $status)>
                                            {{ status_label($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="gateway" class="form-control">
                                    <option value="">{{ __('All Gateways') }}</option>
                                    @foreach($gateways as $gateway)
                                        <option value="{{ $gateway }}" @selected(request('gateway') === $gateway)>
                                            {{ strtoupper($gateway) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                            </div>

                            <div class="col-md-2 mb-2">
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                            </div>

                            <div class="col-md-1 mb-2">
                                <button class="btn btn-primary btn-block">{{ __('Filter') }}</button>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-2 mb-2">
                                <input type="number" step="0.01" name="amount_min" value="{{ request('amount_min') }}" class="form-control" placeholder="{{ __('Min amount') }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <input type="number" step="0.01" name="amount_max" value="{{ request('amount_max') }}" class="form-control" placeholder="{{ __('Max amount') }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>{{ __('Newest') }}</option>
                                    <option value="oldest" @selected(request('sort') === 'oldest')>{{ __('Oldest') }}</option>
                                    <option value="amount_asc" @selected(request('sort') === 'amount_asc')>{{ __('Amount Asc') }}</option>
                                    <option value="amount_desc" @selected(request('sort') === 'amount_desc')>{{ __('Amount Desc') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="per_page" class="form-control">
                                    @foreach([15,25,50,100] as $size)
                                        <option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>
                                            {{ $size }} / page
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <a href="{{ route('dashboard.payments.index') }}" class="btn btn-outline-secondary btn-block">
                                    {{ __('Reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Gateway') }}</th>
                                <th>{{ __('Transaction ID') }}</th>
                                <th>{{ __('Idempotency Key') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Paid At') }}</th>
                                <th>{{ __('Refunded') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th width="100">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>

                                    <td>
                                        @if($payment->order)
                                            <a href="{{ route('dashboard.orders.show', $payment->order) }}">
                                                {{ $payment->order->order_number ?? ('#'.$payment->order->id) }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td>{{ $payment->order?->user?->name ?? __('Guest') }}</td>
                                    <td>{{ $payment->display_gateway }}</td>
                                    <td>{{ $payment->transaction_id ?: '—' }}</td>
                                    <td>{{ $payment->idempotency_key ?: '—' }}</td>
                                    <td>{{ $payment->display_amount }}</td>

                                    <td>
                                        <span class="{{ $payment->status_badge_class }}">
                                            {{ status_label($payment->status) }}
                                        </span>
                                    </td>

                                    <td>{{ $payment->paid_at?->translatedFormat('Y-m-d h:i A') ?: '—' }}</td>
                                    <td>{{ number_format($payment->refunded_amount, 2) }}</td>
                                    <td>{{ $payment->created_at?->format('Y-m-d') }}</td>

                                    <td>
                                        <a href="{{ route('dashboard.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">
                                            {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                        {{ __('No payments found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($payments->hasPages())
                    <div class="card-footer clearfix">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
