@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Shipments') }}</h1>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('dashboard.shipments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> {{ __('Add Shipment') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total']) }}</h3>
                            <p>{{ __('Total') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-shipping-fast"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['pending']) }}</h3>
                            <p>{{ __('Pending') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($stats['in_transit']) }}</h3>
                            <p>{{ __('In Transit') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-truck"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['delivered']) }}</h3>
                            <p>{{ __('Delivered') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($stats['failed']) }}</h3>
                            <p>{{ __('Failed') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.shipments.index') }}">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search order / tracking / customer / carrier') }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @foreach(\App\Models\Shipment::statuses() as $status)
                                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ status_label($status) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="method_id" class="form-control">
                                    <option value="">{{ __('All Methods') }}</option>
                                    @foreach($methods as $id => $name)
                                        <option value="{{ $id }}" @selected((int) request('method_id') === (int) $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>{{ __('Newest') }}</option>
                                    <option value="oldest" @selected(request('sort') === 'oldest')>{{ __('Oldest') }}</option>
                                    <option value="shipped_desc" @selected(request('sort') === 'shipped_desc')>{{ __('Shipped Desc') }}</option>
                                    <option value="shipped_asc" @selected(request('sort') === 'shipped_asc')>{{ __('Shipped Asc') }}</option>
                                    <option value="delivered_desc" @selected(request('sort') === 'delivered_desc')>{{ __('Delivered Desc') }}</option>
                                    <option value="delivered_asc" @selected(request('sort') === 'delivered_asc')>{{ __('Delivered Asc') }}</option>
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <button class="btn btn-primary btn-block">{{ __('Go') }}</button>
                            </div>

                            <div class="col-md-1 mb-2">
                                <a href="{{ route('dashboard.shipments.index') }}" class="btn btn-outline-secondary btn-block">{{ __('Reset') }}</a>
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
                                <th>{{ __('Method') }}</th>
                                <th>{{ __('Carrier') }}</th>
                                <th>{{ __('Tracking') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Shipped At') }}</th>
                                <th>{{ __('Delivered At') }}</th>
                                <th width="260">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shipments as $shipment)
                                <tr>
                                    <td>{{ $shipment->id }}</td>
                                    <td>{{ $shipment->order?->order_number ?? '—' }}</td>
                                    <td>{{ $shipment->order?->user?->name ?? __('Guest') }}</td>
                                    <td>{{ $shipment->method?->label ?? '—' }}</td>
                                    <td>{{ $shipment->carrier ?: '—' }}</td>
                                    <td>{{ $shipment->tracking_number ?: '—' }}</td>
                                    <td><span class="{{ $shipment->status_badge_class }}">{{ status_label($shipment->status) }}</span></td>
                                    <td>{{ $shipment->current_location ?: '—' }}</td>
                                    <td>{{ $shipment->shipped_at?->translatedFormat('Y-m-d h:i A') ?: '—' }}</td>
                                    <td>{{ $shipment->delivered_at?->translatedFormat('Y-m-d h:i A') ?: '—' }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.shipments.show', $shipment) }}" class="btn btn-sm btn-outline-info">{{ __('View') }}</a>
                                        <a href="{{ route('dashboard.shipments.edit', $shipment) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>

                                        <form action="{{ route('dashboard.shipments.toggle-status', $shipment) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-warning">{{ __('Next Status') }}</button>
                                        </form>

                                        <form action="{{ route('dashboard.shipments.destroy', $shipment) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Delete this shipment?')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">{{ __('No shipments found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($shipments->hasPages())
                    <div class="card-footer clearfix">
                        {{ $shipments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
