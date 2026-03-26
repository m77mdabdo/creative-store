@extends('dashboard.app.layout')

@section('title', 'Addresses')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Addresses</h1>
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
                            <h3>{{ $stats['total_addresses'] }}</h3>
                            <p>Total Addresses</p>
                        </div>
                        <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['default_addresses'] }}</h3>
                            <p>Default Addresses</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $stats['with_users'] }}</h3>
                            <p>User Addresses</p>
                        </div>
                        <div class="icon"><i class="fas fa-user"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['guest_addresses'] }}</h3>
                            <p>Guest Addresses</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-slash"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filters</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.addresses.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="q" class="form-control"
                                       placeholder="Search name, phone, city, street, user..."
                                       value="{{ $filters['q'] ?? '' }}">
                            </div>

                            <div class="col-md-2">
                                <select name="city" class="form-control">
                                    <option value="">All Cities</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}" @selected(($filters['city'] ?? '') === $city)>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="is_default" class="form-control">
                                    <option value="">Default Status</option>
                                    <option value="1" @selected(($filters['is_default'] ?? '') === '1')>Default</option>
                                    <option value="0" @selected(($filters['is_default'] ?? '') === '0')>Not Default</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="has_user" class="form-control">
                                    <option value="">Address Type</option>
                                    <option value="1" @selected(($filters['has_user'] ?? '') === '1')>User Address</option>
                                    <option value="0" @selected(($filters['has_user'] ?? '') === '0')>Guest Address</option>
                                </select>
                            </div>

                            <div class="col-md-1">
                                <select name="sort" class="form-control">
                                    <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Latest</option>
                                    <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Oldest</option>
                                    <option value="city_asc" @selected(($filters['sort'] ?? '') === 'city_asc')>City ↑</option>
                                    <option value="city_desc" @selected(($filters['sort'] ?? '') === 'city_desc')>City ↓</option>
                                </select>
                            </div>

                            <div class="col-md-1">
                                <button class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Addresses List</h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Label</th>
                            <th>Full Name</th>
                            <th>User</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>District</th>
                            <th>Street</th>
                            <th>Default</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($addresses as $address)
                            <tr>
                                <td>{{ $address->id }}</td>
                                <td>{{ $address->label ?: '-' }}</td>
                                <td>{{ $address->full_name }}</td>
                                <td>
                                    @if($address->user)
                                        <div>{{ $address->user->name }}</div>
                                        <small class="text-muted">{{ $address->user->email }}</small>
                                    @else
                                        <span class="badge badge-secondary">Guest</span>
                                    @endif
                                </td>
                                <td>{{ $address->phone }}</td>
                                <td>{{ $address->city }}</td>
                                <td>{{ $address->district ?: '-' }}</td>
                                <td>{{ $address->street }}</td>
                                <td>
                                    @if($address->is_default)
                                        <span class="badge badge-success">Default</span>
                                    @else
                                        <span class="badge badge-light">No</span>
                                    @endif
                                </td>
                                <td>{{ $address->created_at?->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('dashboard.addresses.show', $address) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('dashboard.addresses.edit', $address) }}" class="btn btn-sm btn-primary">Edit</a>

                                    <form action="{{ route('dashboard.addresses.toggle-default', $address) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-warning">Default</button>
                                    </form>

                                    <form action="{{ route('dashboard.addresses.destroy', $address) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this address?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">No addresses found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    {{ $addresses->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection


