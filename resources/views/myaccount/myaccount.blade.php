@extends('myaccount.layout')

@section('title', __('account.my_account'))

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">{{ __('account.my_account') }}</h3>
    </div>

    <div class="row">
        <div class="col-lg-5">

            <div class="card mb-3">
                <div class="card-header bg-white">
                    <strong>{{ __('account.account_details') }}</strong>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>{{ __('account.full_name') }}:</strong> {{ $user->name }}</p>
                    <p class="mb-2"><strong>{{ __('account.email') }}:</strong> {{ $user->email }}</p>
                    <p class="mb-0"><strong>{{ __('account.member_since') }}:</strong> {{ $user->created_at?->format('d M Y') }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>{{ __('account.recent_orders') }}</strong>
                    <a href="{{ route('shipping.orders.index') }}" class="btn btn-sm btn-outline-dark">
                        {{ __('account.view_all') }}
                    </a>
                </div>

                <div class="card-body p-0">
                    @if($orders->count())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('account.order') }}</th>
                                        <th>{{ __('account.total') }}</th>
                                        <th>{{ __('account.status') }}</th>
                                        <th>{{ __('account.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('shipping.orders.show', $order->id) }}">
                                                    #{{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>{{ number_format($order->total, 2) }} {{ $order->currency }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at?->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            {{ __('account.no_orders_yet') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <div class="col-lg-7">

            <div class="card mb-3">
                <div class="card-header bg-white">
                    <strong>{{ __('account.update_name') }}</strong>
                </div>
                <div class="card-body">

                    @if(session('success_profile'))
                        <div class="alert alert-success">{{ session('success_profile') }}</div>
                    @endif

                    <form action="{{ route('myaccount.profile') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>{{ __('account.full_name') }}</label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror">

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button class="btn btn-dark">
                            <i class="fas fa-save mr-1"></i> {{ __('account.save') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <strong>{{ __('account.change_password') }}</strong>
                </div>
                <div class="card-body">

                    @if(session('success_password'))
                        <div class="alert alert-success">{{ session('success_password') }}</div>
                    @endif

                    <form action="{{ route('myaccount.password') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>{{ __('account.current_password') }}</label>
                            <input type="password"
                                   name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror">

                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ __('account.new_password') }}</label>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror">

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ __('account.confirm_new_password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <button class="btn btn-dark">
                            <i class="fas fa-key mr-1"></i> {{ __('account.update_password') }}
                        </button>
                    </form>

                    <div class="mt-3">
                        <a href="{{ route('forgot-password') }}" class="d-block">
                            {{ __('account.forgot_password') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
