@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Shipping Zones') }}</h1>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('dashboard.shipping-zones.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> {{ __('Add Shipping Zone') }}
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

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total']) }}</h3>
                            <p>{{ __('Total Zones') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-map-marked-alt"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['active']) }}</h3>
                            <p>{{ __('Active') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($stats['inactive']) }}</h3>
                            <p>{{ __('Inactive') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['rates_count']) }}</h3>
                            <p>{{ __('Linked Rates') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-check-alt"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.shipping-zones.index') }}">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search name / code / country / description') }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="active" @selected(request('status') === 'active')>{{ __('Active') }}</option>
                                    <option value="inactive" @selected(request('status') === 'inactive')>{{ __('Inactive') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="name_asc" @selected(request('sort', 'name_asc') === 'name_asc')>{{ __('Name A-Z') }}</option>
                                    <option value="name_desc" @selected(request('sort') === 'name_desc')>{{ __('Name Z-A') }}</option>
                                    <option value="country_asc" @selected(request('sort') === 'country_asc')>{{ __('Country A-Z') }}</option>
                                    <option value="country_desc" @selected(request('sort') === 'country_desc')>{{ __('Country Z-A') }}</option>
                                    <option value="newest" @selected(request('sort') === 'newest')>{{ __('Newest') }}</option>
                                    <option value="oldest" @selected(request('sort') === 'oldest')>{{ __('Oldest') }}</option>
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

                            <div class="col-md-1 mb-2">
                                <button class="btn btn-primary btn-block">{{ __('Filter') }}</button>
                            </div>

                            <div class="col-md-1 mb-2">
                                <a href="{{ route('dashboard.shipping-zones.index') }}" class="btn btn-outline-secondary btn-block">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Rates') }}</th>
                                <th width="220">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shippingZones as $zone)
                                <tr>
                                    <td>{{ $zone->id }}</td>
                                    <td>{{ $zone->name }}</td>
                                    <td><code>{{ $zone->code }}</code></td>
                                    <td>{{ $zone->country }}</td>
                                    <td>{{ $zone->description ?: '—' }}</td>
                                    <td>
                                        @if($zone->is_active)
                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $zone->rates()->count() }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.shipping-zones.edit', $zone) }}" class="btn btn-sm btn-outline-primary">
                                            {{ __('Edit') }}
                                        </a>

                                        <form action="{{ route('dashboard.shipping-zones.toggle-status', $zone) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                {{ $zone->is_active ? __('Disable') : __('Enable') }}
                                            </button>
                                        </form>

                                        <form action="{{ route('dashboard.shipping-zones.destroy', $zone) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Delete this shipping zone?')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">{{ __('No shipping zones found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($shippingZones->hasPages())
                    <div class="card-footer clearfix">
                        {{ $shippingZones->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection 
