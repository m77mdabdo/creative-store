@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Shipping Rates</h1>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('dashboard.shipping-rates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Add Shipping Rate
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
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total']) }}</h3>
                            <p>Total Rates</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-check-alt"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['active']) }}</h3>
                            <p>Active</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($stats['inactive']) }}</h3>
                            <p>Inactive</p>
                        </div>
                        <div class="icon"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['zones']) }}</h3>
                            <p>Zones</p>
                        </div>
                        <div class="icon"><i class="fas fa-map-marked-alt"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.shipping-rates.index') }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search city / district / zone / method">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="zone_id" class="form-control">
                                    <option value="">All Zones</option>
                                    @foreach($zones as $id => $name)
                                        <option value="{{ $id }}" @selected((int) request('zone_id') === (int) $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="method_id" class="form-control">
                                    <option value="">All Methods</option>
                                    @foreach($methods as $id => $name)
                                        <option value="{{ $id }}" @selected((int) request('method_id') === (int) $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">Status</option>
                                    <option value="active" @selected(request('status') === 'active')>Active</option>
                                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="priority_asc" @selected(request('sort', 'priority_asc') === 'priority_asc')>Priority Asc</option>
                                    <option value="priority_desc" @selected(request('sort') === 'priority_desc')>Priority Desc</option>
                                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Price Asc</option>
                                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Price Desc</option>
                                    <option value="newest" @selected(request('sort') === 'newest')>Newest</option>
                                    <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <button class="btn btn-primary btn-block">Go</button>
                            </div>

                            <div class="col-md-1 mb-2">
                                <a href="{{ route('dashboard.shipping-rates.index') }}" class="btn btn-outline-secondary btn-block">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Zone</th>
                                <th>Method</th>
                                <th>City</th>
                                <th>District</th>
                                <th>Weight</th>
                                <th>Price</th>
                                <th>ETA</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th width="220">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shippingRates as $rate)
                                <tr>
                                    <td>{{ $rate->id }}</td>
                                    <td>{{ $rate->zone?->name ?? '—' }}</td>
                                    <td>{{ $rate->method?->name ?? '—' }}</td>
                                    <td>{{ $rate->city ?: '—' }}</td>
                                    <td>{{ $rate->district ?: '—' }}</td>
                                    <td>
                                        {{ number_format((float) $rate->min_weight, 2) }}
                                        -
                                        {{ number_format((float) $rate->max_weight, 2) }}
                                    </td>
                                    <td>{{ number_format((float) $rate->price, 2) }}</td>
                                    <td>{{ $rate->eta_min_days }} - {{ $rate->eta_max_days }} days</td>
                                    <td>{{ $rate->priority }}</td>
                                    <td>
                                        @if($rate->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.shipping-rates.edit', $rate) }}" class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                        <form action="{{ route('dashboard.shipping-rates.toggle-status', $rate) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                {{ $rate->is_active ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('dashboard.shipping-rates.destroy', $rate) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this shipping rate?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">No shipping rates found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($shippingRates->hasPages())
                    <div class="card-footer clearfix">
                        {{ $shippingRates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection  
