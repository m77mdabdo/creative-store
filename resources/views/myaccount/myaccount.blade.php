@extends('myaccount.layout')

@section('title', __('account.my_account'))

@section('content')

<style>
/* ─── Page Variables ─── */
:root {
    --bg-page:       #0b0c0f;
    --bg-card:       #16181d;
    --bg-card-hover: #1c1e25;
    --bg-input:      #1e2028;
    --accent:        #6c63ff;
    --accent-soft:   rgba(108,99,255,.12);
    --accent-glow:   rgba(108,99,255,.3);
    --text-primary:  #f0f1f5;
    --text-secondary:#8a8fa8;
    --text-muted:    #4e5268;
    --border:        rgba(255,255,255,.06);
    --border-focus:  rgba(108,99,255,.5);
    --success:       #10b981;
    --success-soft:  rgba(16,185,129,.1);
    --danger:        #ef4444;
    --radius-sm:     8px;
    --radius-md:     14px;
    --radius-lg:     20px;
    --transition:    .22s cubic-bezier(.4,0,.2,1);
}

/* ─── Page Header ─── */
.page-header {
    margin-bottom: 32px;
}

.page-title {
    font-size: 26px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -.5px;
    line-height: 1.2;
}

.page-subtitle {
    font-size: 14px;
    color: var(--text-muted);
    margin-top: 4px;
}

/* ─── Grid ─── */
.acc-grid {
    display: grid;
    grid-template-columns: 1fr 1.4fr;
    gap: 20px;
    align-items: start;
}

@media (max-width: 900px) {
    .acc-grid { grid-template-columns: 1fr; }
}

/* ─── Cards ─── */
.acc-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: border-color var(--transition);
    margin-bottom: 20px;
}

.acc-card:hover { border-color: rgba(255,255,255,.1); }

.acc-card-head {
    padding: 18px 22px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

html[dir="rtl"] .acc-card-head { flex-direction: row-reverse; }

.acc-card-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}

html[dir="rtl"] .acc-card-title { flex-direction: row-reverse; }

.acc-card-title-icon {
    width: 28px; height: 28px;
    border-radius: var(--radius-sm);
    background: var(--accent-soft);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px;
    color: var(--accent);
}

.acc-card-body { padding: 22px; }

/* ─── Info Rows ─── */
.info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}
.info-row:last-child { border-bottom: none; padding-bottom: 0; }
.info-row:first-child { padding-top: 0; }

html[dir="rtl"] .info-row { flex-direction: row-reverse; }

.info-label {
    font-size: 12px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
    font-weight: 600;
}

.info-value {
    font-size: 14px;
    color: var(--text-primary);
    font-weight: 500;
    text-align: right;
    word-break: break-all;
}

html[dir="rtl"] .info-value { text-align: left; }

.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    background: var(--success-soft);
    color: var(--success);
    border: 1px solid rgba(16,185,129,.2);
}

/* ─── Orders Table ─── */
.orders-link-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: var(--accent);
    text-decoration: none;
    background: var(--accent-soft);
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    border: 1px solid rgba(108,99,255,.2);
    transition: var(--transition);
    white-space: nowrap;
}

html[dir="rtl"] .orders-link-btn { flex-direction: row-reverse; }

.orders-link-btn:hover {
    background: rgba(108,99,255,.2);
    color: #a5b4fc;
    text-decoration: none;
}

.orders-empty {
    text-align: center;
    padding: 36px 20px;
    color: var(--text-muted);
}

.orders-empty i {
    font-size: 36px;
    margin-bottom: 10px;
    display: block;
    opacity: .4;
}

.orders-empty p { font-size: 14px; }

/* Table */
.orders-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 0 -22px;
    padding: 0 22px;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 460px;
}

.orders-table th {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-muted);
    padding: 0 12px 12px;
    text-align: left;
    white-space: nowrap;
    border-bottom: 1px solid var(--border);
}

html[dir="rtl"] .orders-table th { text-align: right; }

.orders-table td {
    padding: 13px 12px;
    font-size: 13px;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
    vertical-align: middle;
}

.orders-table tbody tr:last-child td { border-bottom: none; }

.orders-table tbody tr {
    transition: background var(--transition);
}

.orders-table tbody tr:hover { background: rgba(255,255,255,.02); }

.orders-table .order-num-link {
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
    font-size: 12px;
}

.orders-table .order-num-link:hover { color: #a5b4fc; }

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.status-badge::before {
    content: '';
    width: 5px; height: 5px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}

.status-pending   { background: rgba(251,191,36,.1);  color: #fbbf24; border: 1px solid rgba(251,191,36,.2); }
.status-processing{ background: rgba(59,130,246,.1);  color: #60a5fa; border: 1px solid rgba(59,130,246,.2); }
.status-completed { background: var(--success-soft);  color: var(--success); border: 1px solid rgba(16,185,129,.2); }
.status-cancelled { background: rgba(239,68,68,.1);   color: #f87171; border: 1px solid rgba(239,68,68,.2); }
.status-default   { background: rgba(255,255,255,.06); color: var(--text-secondary); border: 1px solid var(--border); }

/* ─── Forms ─── */
.cs-form-group {
    margin-bottom: 18px;
}

.cs-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: 8px;
}

.cs-input {
    width: 100%;
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    font-size: 14px;
    font-family: inherit;
    padding: 11px 14px;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
    -webkit-appearance: none;
}

.cs-input:focus {
    border-color: var(--border-focus);
    box-shadow: 0 0 0 3px var(--accent-soft);
}

.cs-input.is-invalid { border-color: rgba(239,68,68,.5); }

.cs-invalid-msg {
    font-size: 12px;
    color: var(--danger);
    margin-top: 5px;
}

.cs-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 22px;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: none;
    transition: var(--transition);
    text-decoration: none;
}

html[dir="rtl"] .cs-btn { flex-direction: row-reverse; }

.cs-btn-primary {
    background: var(--accent);
    color: #fff;
}

.cs-btn-primary:hover {
    background: #5b52e5;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px var(--accent-glow);
}

.cs-btn-primary:active { transform: translateY(0); }

.cs-btn-block { width: 100%; }

/* Alert */
.cs-alert {
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

html[dir="rtl"] .cs-alert { flex-direction: row-reverse; }

.cs-alert-success {
    background: var(--success-soft);
    border: 1px solid rgba(16,185,129,.2);
    color: var(--success);
}

/* Forgot link */
.cs-forgot-link {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: var(--text-muted);
    text-decoration: none;
    margin-top: 12px;
    transition: color var(--transition);
}

.cs-forgot-link:hover { color: var(--accent); text-decoration: none; }

html[dir="rtl"] .cs-forgot-link { flex-direction: row-reverse; }

/* Section divider */
.section-divider {
    height: 1px;
    background: var(--border);
    margin: 4px 0 20px;
}
</style>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('account.my_account') }}</h1>
    <p class="page-subtitle">{{ __('account.manage_your_account') ?? 'Manage your profile and orders' }}</p>
</div>

<div class="acc-grid">

    <!-- ── Left Column ── -->
    <div>

        {{-- Account Details Card --}}
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
                    <span class="info-value" style="font-size:13px;">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('account.member_since') }}</span>
                    <span class="info-value">
                        <span class="info-badge">
                            <i class="fas fa-check-circle" style="font-size:10px;"></i>
                            {{ $user->created_at?->format('d M Y') }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Recent Orders Card --}}
        <div class="acc-card">
            <div class="acc-card-head">
                <span class="acc-card-title">
                    <span class="acc-card-title-icon"><i class="fas fa-box"></i></span>
                    {{ __('account.recent_orders') }}
                </span>
                <a href="{{ route('shipping.orders.index') }}" class="orders-link-btn">
                    {{ __('account.view_all') }}
                    <i class="fas fa-arrow-right" style="font-size:10px;"></i>
                </a>
            </div>
            <div class="acc-card-body" style="padding: 0 22px 22px;">
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
                                        <td>
                                            {{ $order->total ? number_format($order->total, 2) . ' ' . $order->currency : '—' }}
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $statusClass }}">
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

        {{-- Update Name Card --}}
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
                            <div class="cs-invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="cs-btn cs-btn-primary">
                        <i class="fas fa-save"></i> {{ __('account.save') }}
                    </button>
                </form>

            </div>
        </div>

        {{-- Change Password Card --}}
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
                            <div class="cs-invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="cs-form-group">
                        <label class="cs-label">{{ __('account.new_password') }}</label>
                        <input type="password"
                               name="password"
                               class="cs-input @error('password') is-invalid @enderror"
                               placeholder="••••••••">
                        @error('password')
                            <div class="cs-invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
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
                        <i class="fas fa-key"></i> {{ __('account.update_password') }}
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
