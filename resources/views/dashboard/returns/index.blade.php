@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>{{ __('Returns') }}</h1></div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total_returns']) }}</h3>
                            <p>{{ __('Total') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-box-open"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['requested_returns']) }}</h3>
                            <p>{{ __('Requested') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($stats['approved_returns']) }}</h3>
                            <p>{{ __('Approved') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-check"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-indigo">
                        <div class="inner">
                            <h3>{{ number_format($stats['received_returns']) }}</h3>
                            <p>{{ __('Received') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-box"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['refunded_returns']) }}</h3>
                            <p>{{ __('Refunded') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-double"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($stats['rejected_returns']) }}</h3>
                            <p>{{ __('Rejected') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ number_format($stats['partial_refunded_returns']) }}</h3>
                            <p>{{ __('Partial Refunded') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-undo-alt"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.returns.index') }}">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ request('q') }}"
                                    class="form-control"
                                    placeholder="{{ __('Search order / customer / reason') }}"
                                >
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @foreach(\App\Models\ReturnRequest::statuses() as $status)
                                        <option value="{{ $status }}" @selected(request('status') === $status)>
                                            {{ status_label($status) }}
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
                                <select name="sort" class="form-control">
                                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>{{ __('Newest') }}</option>
                                    <option value="oldest" @selected(request('sort') === 'oldest')>{{ __('Oldest') }}</option>
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <button class="btn btn-primary btn-block">{{ __('Go') }}</button>
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
                                <th>{{ __('Items') }}</th>
                                <th>{{ __('Items Refund') }}</th>
                                <th>{{ __('Shipping Refund') }}</th>
                                <th>{{ __('Tax Refund') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Requested At') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $return)
                                <tr>
                                    <td>{{ $return->id }}</td>
                                    <td>{{ $return->order?->order_number ?? '—' }}</td>
                                    <td>{{ $return->order?->user?->name ?? __('Guest') }}</td>
                                    <td>{{ $return->items->count() }}</td>
                                    <td>{{ number_format($return->items_refund_total, 2) }}</td>
                                    <td>{{ number_format((float) ($return->shipping_refund_amount ?? 0), 2) }}</td>
                                    <td>{{ number_format((float) ($return->tax_refund_amount ?? 0), 2) }}</td>
                                    <td>
                                        <span class="{{ $return->status_badge_class }}">
                                            {{ status_label($return->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $return->requested_at?->format('Y-m-d h:i A') ?: $return->created_at?->format('Y-m-d h:i A') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.returns.show', $return) }}" class="btn btn-sm btn-outline-primary">
                                            {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">{{ __('No return requests found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($returns->hasPages())
                    <div class="card-footer">{{ $returns->links() }}</div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
