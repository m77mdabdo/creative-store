@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Orders') }}</h1>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.home') }}">{{ __('Dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Orders') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['totalOrders']) }}</h3>
                            <p>{{ __('Total Orders') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['paidOrders']) }}</h3>
                            <p>{{ __('Paid Orders') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['pendingOrders']) }}</h3>
                            <p>{{ __('Pending / Processing') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($stats['totalSales'], 2) }}</h3>
                            <p>{{ __('Total Sales (Paid)') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Filters') }}</h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.orders.index') }}">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>{{ __('Search') }}</label>
                                <input type="text" name="search" class="form-control"
                                       value="{{ $filters['search'] }}"
                                       placeholder="{{ __('Order number / customer / email') }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label>{{ __('Order Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="processing" {{ $filters['status'] === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                    <option value="completed" {{ $filters['status'] === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                    <option value="cancelled" {{ $filters['status'] === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label>{{ __('Payment Status') }}</label>
                                <select name="payment_status" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="pending" {{ $filters['payment_status'] === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="paid" {{ $filters['payment_status'] === 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                    <option value="failed" {{ $filters['payment_status'] === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                                    <option value="refunded" {{ $filters['payment_status'] === 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label>{{ __('Shipping Status') }}</label>
                                <select name="shipping_status" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="pending" {{ $filters['shipping_status'] === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="processing" {{ $filters['shipping_status'] === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                    <option value="shipped" {{ $filters['shipping_status'] === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                                    <option value="delivered" {{ $filters['shipping_status'] === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                    <option value="cancelled" {{ $filters['shipping_status'] === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <label>{{ __('Payment Method') }}</label>
                                <input type="text" name="payment_method" class="form-control"
                                       value="{{ $filters['payment_method'] }}"
                                       placeholder="{{ __('e.g. moyasar') }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label>{{ __('Date From') }}</label>
                                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label>{{ __('Date To') }}</label>
                                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
                            </div>
                        </div>

                        <div class="mt-2">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search mr-1"></i> {{ __('Filter') }}
                            </button>

                            <a href="{{ route('dashboard.orders.index') }}" class="btn btn-secondary">
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Orders List') }}</h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Order Number') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Items') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Order Status') }}</th>
                            <th>{{ __('Payment') }}</th>
                            <th>{{ __('Shipping') }}</th>
                            <th>{{ __('Method') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th style="width: 280px;">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>

                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                </td>

                                <td>
                                    @if($order->user)
                                        <div>{{ $order->user->name }}</div>
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                    @else
                                        <div>{{ $order->guest_email ?? __('Guest') }}</div>
                                        <small class="text-muted">{{ $order->guest_phone ?? '-' }}</small>
                                    @endif
                                </td>

                                <td>{{ $order->items_count }}</td>

                                <td>{{ number_format((float) $order->total, 2) }} {{ $order->currency ?? __('SAR') }}</td>

                                <td>
                                    <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ status_label($order->status) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'secondary') }}">
                                        {{ status_label($order->payment_status) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge badge-{{ $order->shipping_status === 'delivered' ? 'success' : ($order->shipping_status === 'cancelled' ? 'danger' : 'info') }}">
                                        {{ status_label($order->shipping_status) }}
                                    </span>
                                </td>

                                <td>{{ $order->shippingMethod?->label ?? '-' }}</td>

                                <td>{{ $order->created_at?->translatedFormat('d M Y, h:i A') }}</td>

                                <td>
                                    <div class="d-flex flex-wrap" style="gap:6px;">
                                        <a href="{{ route('dashboard.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            {{ __('View') }}
                                        </a>

                                        <a href="{{ route('dashboard.orders.invoice', $order) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                            {{ __('Invoice') }}
                                        </a>

                                        @if(($canEdit ?? false) && $order->status !== 'cancelled')
                                            <form action="{{ route('dashboard.orders.quick-action', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="mark_processing">
                                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                                    {{ __('Processing') }}
                                                </button>
                                            </form>

                                            <form action="{{ route('dashboard.orders.quick-action', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="mark_paid">
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    {{ __('Paid') }}
                                                </button>
                                            </form>

                                            <form action="{{ route('dashboard.orders.quick-action', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="mark_completed">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    {{ __('Complete') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    {{ __('No orders found.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="card-footer clearfix">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
