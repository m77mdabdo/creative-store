@extends('dashboard.app.layout')

@section('title', 'Wishlist Activity')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Wishlist Activity</h1>
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
                            <h3>{{ $stats['total_wishlists'] }}</h3>
                            <p>Total Wishlists</p>
                        </div>
                        <div class="icon"><i class="fas fa-heart"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stats['total_wishlist_items'] }}</h3>
                            <p>Total Wishlist Items</p>
                        </div>
                        <div class="icon"><i class="fas fa-list"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['non_empty_wishlists'] }}</h3>
                            <p>Non Empty Wishlists</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['empty_wishlists'] }}</h3>
                            <p>Empty Wishlists</p>
                        </div>
                        <div class="icon"><i class="fas fa-minus-circle"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Advanced Filters</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.wishlists.index') }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <input type="text" name="q" class="form-control"
                                       placeholder="User name, email, phone"
                                       value="{{ $filters['q'] ?? '' }}">
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
                                    <option value="">All Product Status</option>
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
                                    <option value="user_name_asc" @selected(($filters['sort'] ?? '') === 'user_name_asc')>User Name ASC</option>
                                    <option value="user_name_desc" @selected(($filters['sort'] ?? '') === 'user_name_desc')>User Name DESC</option>
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
                            <h3 class="card-title">Top Wishlisted Products</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Count</th>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Users With Highest Wishlist Count</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Items</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($topUsers as $index => $userRow)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $userRow->name }}</td>
                                        <td>{{ $userRow->email }}</td>
                                        <td><span class="badge badge-primary">{{ $userRow->wishlist_items_count }}</span></td>
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
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Wishlist Analytics By Date</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total Added Items</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($analytics as $row)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($row->day)->format('Y-m-d') }}</td>
                                <td>{{ $row->total_items }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No analytics data available.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Wishlists</h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Items Count</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($wishlists as $wishlist)
                            <tr>
                                <td>{{ $wishlist->id }}</td>
                                <td>{{ $wishlist->user->name ?? '-' }}</td>
                                <td>{{ $wishlist->user->email ?? '-' }}</td>
                                <td>{{ $wishlist->user->phone ?? '-' }}</td>
                                <td><span class="badge badge-danger">{{ $wishlist->items_count }}</span></td>
                                <td>{{ $wishlist->created_at?->format('Y-m-d') }}</td>
                                <td>{{ $wishlist->updated_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('dashboard.wishlists.show', $wishlist) }}" class="btn btn-sm btn-info">View</a>

                                    <form action="{{ route('dashboard.wishlists.move-all-to-cart', $wishlist) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Move All To Cart</button>
                                    </form>

                                    <form action="{{ route('dashboard.wishlists.destroy', $wishlist) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this wishlist?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No wishlist activity found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    {{ $wishlists->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
