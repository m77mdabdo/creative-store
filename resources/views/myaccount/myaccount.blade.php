@extends('myaccount.layout')

@section('title', __('account.my_account'))

@section('content')

<link rel="stylesheet" href="{{ versioned_asset('css/site/myaccount-myaccount.css') }}">

<!-- Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('account.my_account') }}</h1>
    <p class="page-subtitle">{{ __('account.manage_your_account') ?? 'Manage your profile, orders and settings' }}</p>
</div>

<div class="acc-grid">

    <!-- ── Left Column ── -->
    <div>

        {{-- Account Details --}}
        <div class="acc-card">
            <div class="acc-card-head">
                <span class="acc-card-title">
                    <span class="acc-card-title-icon"><i class="fas fa-user"></i></span>
                    {{ __('account.account_details') }}
                </span>
            </div>
            <div class="acc-card-body">
                <div class="info-row">
                    <span class="info-label">{{ __('account.full_name') }}</span>
                    <span class="info-value">{{ $user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('account.email') }}</span>
                    <span class="info-value" style="font-size:12px;">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('account.member_since') }}</span>
                    <span class="info-value">
                        <span class="info-badge">
                            <i class="fas fa-check-circle" style="font-size:10px;"></i>
                            {{ $user->created_at?->translatedFormat('d M Y') }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="acc-card">
            <div class="acc-card-head">
                <span class="acc-card-title">
                    <span class="acc-card-title-icon"><i class="fas fa-box"></i></span>
                    {{ __('account.recent_orders') }}
                </span>
                <a href="{{ route('shipping.orders.index') }}" class="orders-link-btn">
                    {{ __('account.view_all') }}
                    <i class="fas fa-arrow-right" style="font-size:9px;"></i>
                </a>
            </div>
            <div class="acc-card-body" style="padding-top:0; padding-bottom:20px;">
                @if($orders->count())
                    <div class="orders-table-wrap">
                        <table class="orders-table">
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
                                    @php
                                        $statusClass = match(strtolower($order->status)) {
                                            'pending'    => 'status-pending',
                                            'processing' => 'status-processing',
                                            'completed'  => 'status-completed',
                                            'cancelled'  => 'status-cancelled',
                                            default      => 'status-default',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('shipping.orders.show', $order->id) }}" class="order-num-link">
                                                #{{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $order->total ? number_format($order->total, 2) . ' ' . $order->currency : '—' }}</td>
                                        <td>
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at?->translatedFormat('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="orders-empty">
                        <i class="fas fa-box-open"></i>
                        <p>{{ __('account.no_orders_yet') }}</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- ── Right Column ── -->
    <div>

        {{-- Update Name --}}
        <div class="acc-card">
            <div class="acc-card-head">
                <span class="acc-card-title">
                    <span class="acc-card-title-icon"><i class="fas fa-pen"></i></span>
                    {{ __('account.update_name') }}
                </span>
            </div>
            <div class="acc-card-body">

                @if(session('success_profile'))
                    <div class="cs-alert cs-alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success_profile') }}
                    </div>
                @endif

                <form action="{{ route('myaccount.profile') }}" method="POST">
                    @csrf
                    <div class="cs-form-group">
                        <label class="cs-label">{{ __('account.full_name') }}</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               class="cs-input @error('name') is-invalid @enderror"
                               placeholder="{{ __('account.full_name') }}">
                        @error('name')
                            <div class="cs-invalid-msg">
                                <i class="fas fa-exclamation-circle" style="font-size:11px;"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <button type="submit" class="cs-btn cs-btn-primary">
                        <i class="fas fa-save"></i>
                        {{ __('account.save') }}
                    </button>
                </form>

            </div>
        </div>

        {{-- Change Password --}}
        <div class="acc-card">
            <div class="acc-card-head">
                <span class="acc-card-title">
                    <span class="acc-card-title-icon"><i class="fas fa-lock"></i></span>
                    {{ __('account.change_password') }}
                </span>
            </div>
            <div class="acc-card-body">

                @if(session('success_password'))
                    <div class="cs-alert cs-alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success_password') }}
                    </div>
                @endif

                <form action="{{ route('myaccount.password') }}" method="POST">
                    @csrf

                    <div class="cs-form-group">
                        <label class="cs-label">{{ __('account.current_password') }}</label>
                        <input type="password"
                               name="current_password"
                               class="cs-input @error('current_password') is-invalid @enderror"
                               placeholder="••••••••">
                        @error('current_password')
                            <div class="cs-invalid-msg">
                                <i class="fas fa-exclamation-circle" style="font-size:11px;"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="cs-form-group">
                        <label class="cs-label">{{ __('account.new_password') }}</label>
                        <input type="password"
                               name="password"
                               class="cs-input @error('password') is-invalid @enderror"
                               placeholder="••••••••">
                        @error('password')
                            <div class="cs-invalid-msg">
                                <i class="fas fa-exclamation-circle" style="font-size:11px;"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="cs-form-group">
                        <label class="cs-label">{{ __('account.confirm_new_password') }}</label>
                        <input type="password"
                               name="password_confirmation"
                               class="cs-input"
                               placeholder="••••••••">
                    </div>

                    <button type="submit" class="cs-btn cs-btn-primary cs-btn-block">
                        <i class="fas fa-key"></i>
                        {{ __('account.update_password') }}
                    </button>
                </form>

                <a href="{{ route('forgot-password') }}" class="cs-forgot-link">
                    <i class="fas fa-question-circle" style="font-size:12px;"></i>
                    {{ __('account.forgot_password') }}
                </a>

            </div>
        </div>

    </div>
</div>

@endsection
