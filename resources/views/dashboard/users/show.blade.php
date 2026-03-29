@extends('dashboard.app.layout')

@section('title', 'User Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>User Details</h1>
            <a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-primary">Edit</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <h3 class="profile-username text-center">{{ $user->name }}</h3>
                            <p class="text-muted text-center">{{ $user->email }}</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item"><b>Phone</b> <span class="float-right">{{ $user->phone ?: '-' }}</span></li>
                                <li class="list-group-item"><b>Addresses</b> <span class="float-right">{{ $user->addresses_count }}</span></li>
                                <li class="list-group-item"><b>Orders</b> <span class="float-right">{{ $user->orders_count }}</span></li>
                                <li class="list-group-item"><b>Reviews</b> <span class="float-right">{{ $user->reviews_count }}</span></li>
                                <li class="list-group-item"><b>Carts</b> <span class="float-right">{{ $user->carts_count }}</span></li>
                                <li class="list-group-item"><b>Active</b> <span class="float-right">{{ $user->is_active ? 'Yes' : 'No' }}</span></li>
                            </ul>

                            <p>
                                @foreach($user->roles as $role)
                                    <span class="badge badge-info">{{ $role->name }}</span>
                                @endforeach
                            </p>

                            <p>
                                @if($user->email_verified_at)
                                    <span class="badge badge-success">Verified</span>
                                @else
                                    <span class="badge badge-warning">Unverified</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Direct Permissions</h3></div>
                        <div class="card-body">
                            @php
                                $allowedPermissions = $user->permissions->filter(fn($permission) => ! $permission->pivot->is_denied);
                                $deniedPermissions = $user->permissions->filter(fn($permission) => $permission->pivot->is_denied);
                            @endphp

                            <h6 class="text-success">Allowed</h6>
                            @forelse($allowedPermissions as $permission)
                                <span class="badge badge-success mb-1">{{ $permission->code }}</span>
                            @empty
                                <p class="text-muted">No allowed direct permissions.</p>
                            @endforelse

                            <hr>

                            <h6 class="text-danger">Denied</h6>
                            @forelse($deniedPermissions as $permission)
                                <span class="badge badge-danger mb-1">{{ $permission->code }}</span>
                            @empty
                                <p class="text-muted">No denied direct permissions.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Addresses</h3></div>
                        <div class="card-body">
                            @forelse($user->addresses as $address)
                                <div class="border rounded p-2 mb-2">
                                    <strong>{{ $address->label ?: 'Address' }}</strong><br>
                                    {{ $address->city }} - {{ $address->street }}
                                    @if($address->is_default)
                                        <span class="badge badge-success">Default</span>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted mb-0">No addresses.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Recent Orders</h3></div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Order Number</th>
                                        <th>Status</th>
                                        @if(auth()->user()->hasPermission('orders.view'))
                                            <th>Payment</th>
                                        @endif
                                        @if(auth()->user()->hasPermission('orders.view-totals') || auth()->user()->hasRole('owner'))
                                            <th>Total</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($user->orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->status }}</td>
                                        @if(auth()->user()->hasPermission('orders.view'))
                                            <td>{{ $order->payment_status }}</td>
                                        @endif
                                        @if(auth()->user()->hasPermission('orders.view-totals') || auth()->user()->hasRole('owner'))
                                            <td>{{ number_format($order->total, 2) }} {{ $order->currency }}</td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No orders yet.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Recent Reviews</h3></div>
                        <div class="card-body">
                            @forelse($user->reviews as $review)
                                <div class="border rounded p-2 mb-2">
                                    <strong>Rating:</strong> {{ $review->rating }}/5
                                    <br>
                                    <strong>Comment:</strong> {{ $review->comment ?: '-' }}
                                </div>
                            @empty
                                <p class="text-muted mb-0">No reviews.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
