@extends('dashboard.app.layout')

@section('title', $abandonedOnly ? 'Abandoned Carts' : 'Carts')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ $abandonedOnly ? 'Abandoned Carts' : 'Carts Module Pro' }}</h1>
            @if(!$abandonedOnly)
                <a href="{{ route('dashboard.carts.abandoned') }}" class="btn btn-warning">
                    View Abandoned Carts
                </a>
            @endif
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
                            <h3>{{ $stats['total_carts'] }}</h3>
                            <p>Total Carts</p>
                        </div>
                        <div class="icon"><i class="fas fa-shopping-basket"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['active_carts'] }}</h3>
                            <p>Active Carts</p>
                        </div>
                        <div class="icon"><i class="fas fa-bolt"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['abandoned_carts'] }}</h3>
                            <p>Abandoned</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $stats['user_carts'] }}</h3>
                            <p>User Carts</p>
                        </div>
                        <div class="icon"><i class="fas fa-user"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $stats['guest_carts'] }}</h3>
                            <p>Guest Carts</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-slash"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stats['total_cart_items'] }}</h3>
                            <p>Total Cart Items</p>
                        </div>
                        <div class="icon"><i class="fas fa-list"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Advanced Filters</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ $abandonedOnly ? route('dashboard.carts.abandoned') : route('dashboard.carts.index') }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <input type="text" name="q" class="form-control"
                                       placeholder="Session, user name, email, phone"
                                       value="{{ $filters['q'] ?? '' }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="user" @selected(($filters['type'] ?? '') === 'user')>User Carts</option>
                                    <option value="guest" @selected(($filters['type'] ?? '') === 'guest')>Guest Carts</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                                    <option value="abandoned" @selected(($filters['status'] ?? ($abandonedOnly ? 'abandoned' : '')) === 'abandoned')>Abandoned</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="product_id" class="form-control">
                                    <option value="">All Products</option>
                                    @foreach($productsFilter as $product)
                                        @php
                                            $translation = $product->translations->firstWhere('locale', app()->getLocale())
                                                ?? $product->translations->firstWhere('locale', config('app.fallback_locale'))
                                                ?? $product->translations->first();
                                            $productName = $translation->name ?? ('Product #' . $product->id);
                                        @endphp
                                        <option value="{{ $product->id }}" @selected(($filters['product_id'] ?? '') == $product->id)>
                                            #{{ $product->id }} - {{ $productName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="product_status" class="form-control">
                                    <option value="">Product Status</option>
                                    <option value="active" @selected(($filters['product_status'] ?? '') === 'active')>Active</option>
                                    <option value="inactive" @selected(($filters['product_status'] ?? '') === 'inactive')>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <input type="number" name="min_items" class="form-control"
                                       placeholder="Min"
                                       value="{{ $filters['min_items'] ?? '' }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <input type="date" name="date_from" class="form-control"
                                       value="{{ $filters['date_from'] ?? '' }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <input type="date" name="date_to" class="form-control"
                                       value="{{ $filters['date_to'] ?? '' }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Latest</option>
                                    <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Oldest</option>
                                    <option value="items_asc" @selected(($filters['sort'] ?? '') === 'items_asc')>Items ASC</option>
                                    <option value="items_desc" @selected(($filters['sort'] ?? '') === 'items_desc')>Items DESC</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="per_page" class="form-control">
                                    <option value="15" @selected(($filters['per_page'] ?? 15) == 15)>15</option>
                                    <option value="25" @selected(($filters['per_page'] ?? '') == 25)>25</option>
                                    <option value="50" @selected(($filters['per_page'] ?? '') == 50)>50</option>
                                    <option value="100" @selected(($filters['per_page'] ?? '') == 100)>100</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <button class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Top Cart Products</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Qty</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($topProducts as $index => $row)
                                    @php
                                        $product = $row['product'];
                                        $translation = $product->translations->firstWhere('locale', app()->getLocale())
                                            ?? $product->translations->firstWhere('locale', config('app.fallback_locale'))
                                            ?? $product->translations->first();
                                        $productName = $translation->name ?? ('Product #' . $product->id);
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <a href="{{ route('dashboard.products.show', $product) }}">
                                                {{ $productName }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($product->status === 'active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($product->status) }}</span>
                                            @endif
                                        </td>
                                        <td><span class="badge badge-danger">{{ $row['count'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No data found.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Cart Analytics By Date</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Rows</th>
                                    <th>Total Quantity</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($analytics as $row)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Carbon::parse($row->day)->format('Y-m-d') }}</td>
                                        <td>{{ $row->total_rows }}</td>
                                        <td>{{ $row->total_qty }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No analytics data available.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Carts</h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>User / Session</th>
                            <th>Email</th>
                            <th>Items Count</th>
                            <th>Status</th>
                            <th>Last Activity</th>
                            <th>Created</th>
                            <th>Order Visibility</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($carts as $cart)
                            @php
                                $isAbandoned = $cart->updated_at < now()->subHours(24) && $cart->items_count > 0;
                                $userHasOrders = $cart->user ? $cart->user->orders()->exists() : false;
                            @endphp
                            <tr>
                                <td>{{ $cart->id }}</td>
                                <td>
                                    @if($cart->user_id)
                                        <span class="badge badge-primary">User</span>
                                    @else
                                        <span class="badge badge-secondary">Guest</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cart->user)
                                        <div>{{ $cart->user->name }}</div>
                                        <small class="text-muted">{{ $cart->user->phone ?? '-' }}</small>
                                    @else
                                        <small class="text-muted">{{ $cart->session_id ?: '-' }}</small>
                                    @endif
                                </td>
                                <td>{{ $cart->user->email ?? '-' }}</td>
                                <td><span class="badge badge-info">{{ $cart->items_count }}</span></td>
                                <td>
                                    @if($isAbandoned)
                                        <span class="badge badge-warning">Abandoned</span>
                                    @elseif($cart->items_count > 0)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-light">Empty</span>
                                    @endif
                                </td>
                                <td>{{ $cart->updated_at?->diffForHumans() }}</td>
                                <td>{{ $cart->created_at?->format('Y-m-d') }}</td>
                                <td>
                                    @if($userHasOrders)
                                        <span class="badge badge-success">User Has Orders</span>
                                    @else
                                        <span class="badge badge-secondary">No Orders</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('dashboard.carts.show', $cart) }}" class="btn btn-sm btn-info">View</a>

                                    <form action="{{ route('dashboard.carts.destroy', $cart) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this cart?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">No carts found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    {{ $carts->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
