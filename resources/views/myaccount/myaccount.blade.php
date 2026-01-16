@extends('myaccount.layout')

@section('title', __('account.my_account'))

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/site/myaccount-myaccount.css') }}">
@endpush

@php
    $role = $user->roles->pluck('name')->first(fn ($name) => in_array($name, ['owner', 'admin', 'employee'], true));
    $avatar = $user->avatarUrl();
    // The profile form asks for the current password only when the email changes.
    $profileForm = old('_form') === 'profile';
    $emailNeedsPassword = $profileForm && old('email', $user->email) !== $user->email;
    $passwordError = ! $profileForm ? $errors->first('current_password') : null;
    $profilePasswordError = $profileForm ? $errors->first('current_password') : null;
    $firstName = \Illuminate\Support\Str::before(trim($user->name), ' ') ?: $user->name;
@endphp

@section('content')

{{-- Profile header --}}
<div class="acc-hero">
    <div class="acc-hero-avatar">
        @if ($avatar)
            <img src="{{ $avatar }}" alt="{{ $user->name }}" width="88" height="88">
        @else
            <span>{{ $user->initials() }}</span>
        @endif
    </div>
    <div class="acc-hero-info">
        <h1 class="acc-hero-name">{{ __('account.hello', ['name' => $firstName]) }}</h1>
        <p class="acc-hero-meta">
            <span dir="ltr">{{ $user->email }}</span>
            @if ($role)
                <span class="acc-role"><i class="fas fa-shield-alt" aria-hidden="true"></i> {{ __('staff.roles.' . $role) }}</span>
            @endif
        </p>
        <p class="acc-hero-since">
            <i class="far fa-calendar-check" aria-hidden="true"></i>
            {{ __('account.member_since') }} {{ $user->created_at?->translatedFormat('F Y') }}
        </p>
    </div>
    @if ($user->isDashboardUser())
        <a href="{{ route('dashboard.home') }}" class="cs-btn cs-btn-light acc-hero-action">
            <i class="fas fa-tachometer-alt" aria-hidden="true"></i> {{ __('account.dashboard') }}
        </a>
    @endif
</div>

{{-- Stats --}}
<div class="acc-stats">
    <a href="{{ route('shipping.orders.index') }}" class="acc-stat">
        <span class="acc-stat-icon"><i class="fas fa-box" aria-hidden="true"></i></span>
        <span class="acc-stat-value">{{ number_format($stats['orders']) }}</span>
        <span class="acc-stat-label">{{ __('account.my_orders') }}</span>
    </a>
    <div class="acc-stat">
        <span class="acc-stat-icon acc-stat-icon--green"><i class="fas fa-wallet" aria-hidden="true"></i></span>
        <span class="acc-stat-value">{{ money($stats['spent']) }}</span>
        <span class="acc-stat-label">{{ __('account.total_spent') }}</span>
    </div>
    <a href="{{ route('shipping.addresses.index') }}" class="acc-stat">
        <span class="acc-stat-icon acc-stat-icon--orange"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></span>
        <span class="acc-stat-value">{{ number_format($stats['addresses']) }}</span>
        <span class="acc-stat-label">{{ __('account.my_addresses') }}</span>
    </a>
    <a href="{{ route('wishlist.index') }}" class="acc-stat">
        <span class="acc-stat-icon acc-stat-icon--pink"><i class="fas fa-heart" aria-hidden="true"></i></span>
        <span class="acc-stat-value">{{ number_format($stats['wishlist']) }}</span>
        <span class="acc-stat-label">{{ __('account.wishlist') }}</span>
    </a>
</div>

<div class="acc-grid">

    {{-- Personal details --}}
    <div class="acc-card">
        <div class="acc-card-head">
            <span class="acc-card-title">
                <span class="acc-card-title-icon"><i class="fas fa-user" aria-hidden="true"></i></span>
                {{ __('account.personal_details') }}
            </span>
        </div>
        <div class="acc-card-body">
            @if (session('success_profile'))
                <div class="cs-alert cs-alert-success" role="status">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    {{ session('success_profile') }}
                </div>
            @endif

            @if (session('info_profile'))
                <div class="cs-alert cs-alert-info" role="status">
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    {{ session('info_profile') }}
                </div>
            @endif

            <form action="{{ route('myaccount.profile') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="_form" value="profile">

                <div class="acc-photo">
                    <div class="acc-photo-preview" id="avatarPreview">
                        @if ($avatar)
                            <img src="{{ $avatar }}" alt="" width="72" height="72">
                        @else
                            <span>{{ $user->initials() }}</span>
                        @endif
                    </div>
                    <div class="acc-photo-actions">
                        <label class="cs-btn cs-btn-light cs-btn-sm" for="avatarInput">
                            <i class="fas fa-camera" aria-hidden="true"></i> {{ __('account.change_photo') }}
                        </label>
                        <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp" class="acc-file">
                        @if ($avatar)
                            <label class="acc-remove">
                                <input type="checkbox" name="remove_avatar" value="1" id="removeAvatar"> {{ __('account.remove_photo') }}
                            </label>
                        @endif
                        <small class="acc-help">{{ __('account.photo_hint') }}</small>
                    </div>
                </div>
                @error('avatar')
                    <div class="cs-invalid-msg acc-photo-error"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ $message }}</div>
                @enderror

                <div class="cs-form-group">
                    <label class="cs-label" for="name">{{ __('account.full_name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" autocomplete="name"
                           class="cs-input @error('name') is-invalid @enderror" required maxlength="120">
                    @error('name')
                        <div class="cs-invalid-msg"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="cs-form-row">
                    <div class="cs-form-group">
                        <label class="cs-label" for="email">{{ __('account.email') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" dir="ltr" autocomplete="email"
                               data-original="{{ $user->email }}" class="cs-input @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="cs-invalid-msg"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="cs-form-group">
                        <label class="cs-label" for="phone">{{ __('account.phone') }}</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" dir="ltr" inputmode="tel" autocomplete="tel"
                               placeholder="05XXXXXXXX" class="cs-input @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="cs-invalid-msg"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="cs-form-group" id="emailPasswordGroup" @unless ($emailNeedsPassword) hidden @endunless>
                    <label class="cs-label" for="profile_current_password">{{ __('account.current_password') }}</label>
                    <input type="password" id="profile_current_password" name="current_password" autocomplete="current-password"
                           class="cs-input {{ $profilePasswordError ? 'is-invalid' : '' }}" placeholder="••••••••">
                    <small class="acc-help">{{ __('account.password_needed_for_email') }}</small>
                    @if ($profilePasswordError)
                        <div class="cs-invalid-msg"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ $profilePasswordError }}</div>
                    @endif
                </div>

                <button type="submit" class="cs-btn cs-btn-primary">
                    <i class="fas fa-save" aria-hidden="true"></i> {{ __('account.save_changes') }}
                </button>
            </form>
        </div>
    </div>

    <div class="acc-column">
        {{-- Password --}}
        <div class="acc-card">
            <div class="acc-card-head">
                <span class="acc-card-title">
                    <span class="acc-card-title-icon"><i class="fas fa-lock" aria-hidden="true"></i></span>
                    {{ __('account.change_password') }}
                </span>
            </div>
            <div class="acc-card-body">
                @if (session('success_password'))
                    <div class="cs-alert cs-alert-success" role="status">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        {{ session('success_password') }}
                    </div>
                @endif

                <form action="{{ route('myaccount.password') }}" method="POST" novalidate>
                    @csrf

                    <div class="cs-form-group">
                        <label class="cs-label" for="current_password">{{ __('account.current_password') }}</label>
                        <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                               class="cs-input {{ $passwordError ? 'is-invalid' : '' }}" placeholder="••••••••">
                        @if ($passwordError)
                            <div class="cs-invalid-msg"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ $passwordError }}</div>
                        @endif
                    </div>

                    <div class="cs-form-group">
                        <label class="cs-label" for="password">{{ __('account.new_password') }}</label>
                        <div class="acc-password">
                            <input type="password" id="password" name="password" autocomplete="new-password"
                                   class="cs-input @error('password') is-invalid @enderror" placeholder="••••••••">
                            <button type="button" class="acc-eye" data-toggle-password="password" aria-label="{{ __('account.show_password') }}">
                                <i class="far fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="acc-strength" aria-hidden="true"><span id="strengthBar"></span></div>
                        <small class="acc-help">{{ __('account.password_rules') }}</small>
                        @error('password')
                            <div class="cs-invalid-msg"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="cs-form-group">
                        <label class="cs-label" for="password_confirmation">{{ __('account.confirm_new_password') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                               class="cs-input" placeholder="••••••••">
                    </div>

                    <button type="submit" class="cs-btn cs-btn-primary cs-btn-block">
                        <i class="fas fa-key" aria-hidden="true"></i> {{ __('account.update_password') }}
                    </button>
                </form>

                <a href="{{ route('forgot-password') }}" class="cs-forgot-link">
                    <i class="fas fa-question-circle" aria-hidden="true"></i>
                    {{ __('account.forgot_password') }}
                </a>
            </div>
        </div>

        {{-- Recent orders --}}
        <div class="acc-card">
            <div class="acc-card-head">
                <span class="acc-card-title">
                    <span class="acc-card-title-icon"><i class="fas fa-box" aria-hidden="true"></i></span>
                    {{ __('account.recent_orders') }}
                </span>
                <a href="{{ route('shipping.orders.index') }}" class="orders-link-btn">
                    {{ __('account.view_all') }}
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
                </a>
            </div>
            <div class="acc-card-body acc-orders">
                @forelse ($orders as $order)
                    @php
                        $statusClass = match (strtolower((string) $order->status)) {
                            'pending'    => 'status-pending',
                            'processing' => 'status-processing',
                            'completed'  => 'status-completed',
                            'cancelled'  => 'status-cancelled',
                            default      => 'status-default',
                        };
                        $promise = $order->status !== 'cancelled' && $order->shipping_status !== 'delivered'
                            ? \App\Support\Shipping\DeliveryPromise::forOrder($order->estimated_delivery_from, $order->estimated_delivery_to)
                            : null;
                    @endphp
                    <div class="acc-order">
                        <div class="acc-order-main">
                            <a href="{{ route('shipping.orders.show', $order->id) }}" class="order-num-link" dir="ltr">#{{ $order->order_number }}</a>
                            <span class="acc-order-meta">
                                {{ $order->created_at?->translatedFormat('j M Y') }} · {{ money($order->total, $order->currency) }}
                            </span>
                            @if ($promise)
                                <span class="acc-order-eta"><i class="fas fa-truck" aria-hidden="true"></i> {{ $promise->label() }}</span>
                            @endif
                        </div>
                        <div class="acc-order-side">
                            <span class="status-badge {{ $statusClass }}">{{ status_label($order->status) }}</span>
                            @if ($order->status !== 'cancelled')
                                <a href="{{ route('tracking.show', $order->order_number) }}" class="acc-track">{{ __('site.track_order') }}</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="orders-empty">
                        <i class="fas fa-box-open" aria-hidden="true"></i>
                        <p>{{ __('account.no_orders_yet') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    var input = document.getElementById('avatarInput');
    var preview = document.getElementById('avatarPreview');
    var remove = document.getElementById('removeAvatar');

    if (input && preview) {
        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file || !/^image\//.test(file.type)) return;
            var reader = new FileReader();
            reader.onload = function (event) {
                preview.innerHTML = '';
                var img = document.createElement('img');
                img.src = event.target.result;
                img.alt = '';
                preview.appendChild(img);
                preview.classList.remove('is-removed');
                if (remove) remove.checked = false;
            };
            reader.readAsDataURL(file);
        });
    }

    if (remove && preview) {
        remove.addEventListener('change', function () {
            preview.classList.toggle('is-removed', remove.checked);
        });
    }

    var email = document.getElementById('email');
    var group = document.getElementById('emailPasswordGroup');
    if (email && group) {
        email.addEventListener('input', function () {
            group.hidden = email.value.trim().toLowerCase() === (email.dataset.original || '').toLowerCase();
        });
    }

    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            var field = document.getElementById(button.dataset.togglePassword);
            if (!field) return;
            field.type = field.type === 'password' ? 'text' : 'password';
            button.querySelector('i').className = field.type === 'password' ? 'far fa-eye' : 'far fa-eye-slash';
        });
    });

    var password = document.getElementById('password');
    var bar = document.getElementById('strengthBar');
    if (password && bar) {
        password.addEventListener('input', function () {
            var value = password.value;
            var score = 0;
            if (value.length >= 8) score++;
            if (value.length >= 12) score++;
            if (/[a-z؀-ۿ]/i.test(value) && /\d/.test(value)) score++;
            if (/[^A-Za-z0-9؀-ۿ]/.test(value)) score++;
            bar.style.width = (value ? Math.max(15, score * 25) : 0) + '%';
            bar.className = score >= 3 ? 'is-strong' : (score === 2 ? 'is-ok' : 'is-weak');
        });
    }
})();
</script>
@endpush
