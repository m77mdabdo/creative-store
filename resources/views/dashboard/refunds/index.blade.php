@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Refunds</h1></div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total_refunds']) }}</h3>
                            <p>Total Refunds</p>
                        </div>
                        <div class="icon"><i class="fas fa-undo"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['pending_refunds']) }}</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['processed_refunds']) }}</h3>
                            <p>Processed</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($stats['total_amount'], 2) }}</h3>
                            <p>Total Amount</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.refunds.index') }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search refund / order / customer">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    @foreach(\App\Models\Refund::statuses() as $status)
                                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
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
                                <input type="number" step="0.01" name="amount_min" value="{{ request('amount_min') }}" class="form-control" placeholder="Min">
                            </div>

                            <div class="col-md-1 mb-2">
                                <input type="number" step="0.01" name="amount_max" value="{{ request('amount_max') }}" class="form-control" placeholder="Max">
                            </div>

                            <div class="col-md-1 mb-2">
                                <button class="btn btn-primary btn-block">Go</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Payment Txn</th>
                                <th>Refund Txn</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Processed At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($refunds as $refund)
                                <tr>
                                    <td>{{ $refund->id }}</td>
                                    <td>{{ $refund->payment?->order?->order_number ?? '—' }}</td>
                                    <td>{{ $refund->payment?->order?->user?->name ?? 'Guest' }}</td>
                                    <td>{{ $refund->payment?->transaction_id ?? '—' }}</td>
                                    <td>{{ $refund->transaction_id ?? '—' }}</td>
                                    <td>{{ number_format((float) $refund->amount, 2) }}</td>
                                    <td><span class="{{ $refund->status_badge_class }}">{{ ucfirst($refund->status) }}</span></td>
                                    <td>{{ $refund->processed_at?->format('Y-m-d h:i A') ?: '—' }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.refunds.show', $refund) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">No refunds found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($refunds->hasPages())
                    <div class="card-footer">{{ $refunds->links() }}</div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
