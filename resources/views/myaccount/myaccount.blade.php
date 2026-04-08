@extends('myaccount.layout')

@section('title', __('account.my_account'))

@section('content')

<style>
:root {
    --accent:        #717fe0;
    --accent-dark:   #6c7ae0;
    --accent-soft:   rgba(113,127,224,.10);
    --accent-hover:  #5a6ad0;

    --bg-page:       #f5f5f5;
    --bg-card:       #ffffff;
    --bg-input:      #f7f7f7;

    --text-primary:  #333333;
    --text-secondary:#555555;
    --text-muted:    #888888;

    --border:        #e6e6e6;
    --border-focus:  rgba(113,127,224,.45);

    --success:       #28a745;
    --success-soft:  rgba(40,167,69,.08);
    --danger:        #dc3545;

    --radius-sm:     4px;
    --radius-md:     8px;
    --radius-lg:     12px;
    --shadow-sm:     0 2px 8px rgba(0,0,0,.06);
    --shadow-md:     0 4px 16px rgba(0,0,0,.08);
    --transition:    .2s ease;
    --font-main:     'Poppins', sans-serif;
}
html[lang="ar"] { --font-main: 'Cairo', sans-serif; }

/* ─── Page Header ─── */
.page-header { margin-bottom: 28px; }

.page-title {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -.3px;
}

.page-subtitle {
    font-size: 13px;
    color: var(--text-muted);
    margin-top: 3px;
}

/* ─── Grid ─── */
.acc-grid {
    display: grid;
    grid-template-columns: 1fr 1.45fr;
    gap: 20px;
    align-items: start;
}

@media (max-width: 900px) { .acc-grid { grid-template-columns: 1fr; } }

/* ─── Card ─── */
.acc-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    margin-bottom: 20px;
    transition: box-shadow var(--transition);
}

.acc-card:hover { box-shadow: var(--shadow-md); }

.acc-card-head {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: #fafafa;
}

html[dir="rtl"] .acc-card-head { flex-direction: row-reverse; }

.acc-card-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: .04em;
}

html[dir="rtl"] .acc-card-title { flex-direction: row-reverse; }

.acc-card-title-icon {
    width: 26px; height: 26px;
    border-radius: var(--radius-sm);
    background: var(--accent-soft);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px;
    color: var(--accent);
}

.acc-card-body { padding: 20px; }

/* ─── Info Rows ─── */
.info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
    gap: 12px;
}
.info-row:last-child { border-bottom: none; padding-bottom: 0; }
.info-row:first-child { padding-top: 0; }

html[dir="rtl"] .info-row { flex-direction: row-reverse; }

.info-label {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
    white-space: nowrap;
    flex-shrink: 0;
}

.info-value {
    font-size: 13px;
    color: var(--text-secondary);
    font-weight: 500;
    text-align: right;
    word-break: break-all;
}

html[dir="rtl"] .info-value { text-align: left; }

.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    background: var(--success-soft);
    color: var(--success);
    border: 1px solid rgba(40,167,69,.2);
}

/* ─── Orders ─── */
.orders-link-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 600;
    color: var(--accent);
    text-decoration: none;
    background: var(--accent-soft);
    padding: 5px 12px;
    border-radius: 20px;
    border: 1px solid rgba(113,127,224,.2);
    transition: var(--transition);
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: .04em;
}

html[dir="rtl"] .orders-link-btn { flex-direction: row-reverse; }

.orders-link-btn:hover {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
    text-decoration: none;
}

.orders-empty {
    text-align: center;
    padding: 32px 20px;
    color: var(--text-muted);
}

.orders-empty i { font-size: 32px; margin-bottom: 10px; display: block; color: #ddd; }
.orders-empty p { font-size: 13px; }

/* Table */
.orders-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 0 -20px;
    padding: 0 20px;
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
    letter-spacing: .07em;
    color: var(--text-muted);
    padding: 0 10px 12px;
    text-align: left;
    white-space: nowrap;
    border-bottom: 2px solid var(--border);
}

html[dir="rtl"] .orders-table th { text-align: right; }

.orders-table td {
    padding: 12px 10px;
    font-size: 13px;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
    vertical-align: middle;
}

.orders-table tbody tr:last-child td { border-bottom: none; }

.orders-table tbody tr { transition: background var(--transition); }
.orders-table tbody tr:hover { background: #fafafa; }

.order-num-link {
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
    font-size: 12px;
}
.order-num-link:hover { color: var(--accent-hover); text-decoration: underline; }

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
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

.status-pending    { background: rgba(255,193,7,.1);   color: #d97706; border: 1px solid rgba(255,193,7,.25); }
.status-processing { background: rgba(113,127,224,.1); color: var(--accent); border: 1px solid rgba(113,127,224,.25); }
.status-completed  { background: var(--success-soft);  color: var(--success); border: 1px solid rgba(40,167,69,.2); }
.status-cancelled  { background: rgba(220,53,69,.08);  color: #dc3545; border: 1px solid rgba(220,53,69,.2); }
.status-default    { background: #f5f5f5; color: var(--text-muted); border: 1px solid var(--border); }

/* ─── Forms ─── */
.cs-form-group { margin-bottom: 16px; }

.cs-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 6px;
}

.cs-input {
    width: 100%;
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    font-size: 14px;
    font-family: var(--font-main);
    padding: 10px 14px;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
    -webkit-appearance: none;
    height: 44px;
}

.cs-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-soft);
    background: #fff;
}

.cs-input.is-invalid {
    border-color: rgba(220,53,69,.5);
}

.cs-invalid-msg {
    font-size: 12px;
    color: var(--danger);
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Buttons */
.cs-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 22px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 600;
    font-family: var(--font-main);
    cursor: pointer;
    border: none;
    transition: var(--transition);
    text-decoration: none;
    height: 42px;
    letter-spacing: .02em;
    text-transform: uppercase;
}

html[dir="rtl"] .cs-btn { flex-direction: row-reverse; }

.cs-btn-primary {
    background: var(--accent);
    color: #fff;
}

.cs-btn-primary:hover {
    background: var(--accent-hover);
    color: #fff;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(113,127,224,.4);
    transform: translateY(-1px);
}

.cs-btn-primary:active { transform: translateY(0); box-shadow: none; }

.cs-btn-block { width: 100%; }

/* Alert */
.cs-alert {
    padding: 10px 14px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-left: 3px solid var(--success);
}

html[dir="rtl"] .cs-alert {
    flex-direction: row-reverse;
    border-left: none;
    border-right: 3px solid var(--success);
}

.cs-alert-success {
    background: var(--success-soft);
    color: #1a6e3a;
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

.cs-forgot-link:hover {
    color: var(--accent);
    text-decoration: none;
}

html[dir="rtl"] .cs-forgot-link { flex-direction: row-reverse; }

/* Divider */
.section-divider {
    height: 1px;
    background: var(--border);
    margin: 4px 0 18px;
}
</style>

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
                            {{ $user->created_at?->format('d M Y') }}
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
