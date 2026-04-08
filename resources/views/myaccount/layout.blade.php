<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('account.my_account')) | Creative Store</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

<style>
    body {
        background: #f5f6f8;
        overflow-x: hidden;
    }

    .account-shell {
        min-height: 100vh;
        display: flex;
        background: #f5f6f8;
        overflow-x: hidden;
    }

    .account-sidebar {
        width: 300px;
        min-width: 300px;
        background: #0f1115;
        color: #fff;
        padding: 24px 18px;
        border-left: 1px solid rgba(255,255,255,.06);
        flex-shrink: 0;
        min-height: 100vh;
    }

    .account-sidebar a {
        color: #fff;
        opacity: .95;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 14px;
        border-radius: 12px;
        margin-bottom: 8px;
        transition: .2s ease;
        font-size: 15px;
        font-weight: 500;
    }

    .account-sidebar a:hover,
    .account-sidebar a.active,
    .account-sidebar .nav-link.active {
        background: rgba(255,255,255,.08);
        opacity: 1;
    }

    .account-content {
        flex: 1;
        min-width: 0;
        padding: 30px;
    }

    .brand-mini {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 26px;
        padding: 6px 8px;
    }

    .brand-mini img {
        height: 42px;
        width: auto;
        object-fit: contain;
    }

    .card {
        border-radius: 16px;
        border: 0;
        box-shadow: 0 6px 20px rgba(0,0,0,.04);
        overflow: hidden;
    }

    .card-header {
        font-size: 18px;
        font-weight: 700;
        padding: 16px 20px;
    }

    .card-body {
        padding: 22px 20px;
    }

    .sidebar-section-title {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .08em;
        opacity: .55;
        margin: 20px 0 12px;
        padding-left: 8px;
    }

    .logout-btn {
        border-radius: 12px;
        height: 46px;
        font-weight: 600;
    }

    .table th,
    .table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .badge {
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .form-control {
        height: 48px;
        border-radius: 10px;
    }

    .btn {
        border-radius: 10px;
        min-height: 44px;
        font-weight: 600;
    }

    /* RTL */
    html[dir="rtl"] body {
        direction: rtl;
        text-align: right;
    }

    html[dir="rtl"] .account-shell {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .account-sidebar {
        border-left: 0;
        border-right: 1px solid rgba(255,255,255,.06);
        text-align: right;
    }

    html[dir="rtl"] .brand-mini,
    html[dir="rtl"] .account-sidebar a,
    html[dir="rtl"] .btn,
    html[dir="rtl"] .card-header {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .sidebar-section-title {
        padding-left: 0;
        padding-right: 8px;
        text-align: right;
    }

    html[dir="rtl"] .account-content,
    html[dir="rtl"] .card,
    html[dir="rtl"] .card-body,
    html[dir="rtl"] .card-header,
    html[dir="rtl"] .form-group,
    html[dir="rtl"] label,
    html[dir="rtl"] p,
    html[dir="rtl"] h1,
    html[dir="rtl"] h2,
    html[dir="rtl"] h3,
    html[dir="rtl"] h4,
    html[dir="rtl"] h5,
    html[dir="rtl"] h6 {
        text-align: right;
    }

    html[dir="rtl"] .form-control {
        text-align: right;
    }

    html[dir="rtl"] .table {
        direction: rtl;
    }

    html[dir="rtl"] .table th,
    html[dir="rtl"] .table td {
        text-align: right;
    }

    html[dir="rtl"] .table-responsive {
        direction: rtl;
    }

    html[dir="rtl"] .mr-1 {
        margin-right: 0 !important;
        margin-left: .25rem !important;
    }

    html[dir="rtl"] .mr-2 {
        margin-right: 0 !important;
        margin-left: .5rem !important;
    }

    html[dir="rtl"] .ml-1,
    html[dir="rtl"] .ml-2,
    html[dir="rtl"] .ml-3 {
        margin-left: 0 !important;
    }

    html[dir="rtl"] .text-left {
        text-align: right !important;
    }

    html[dir="rtl"] .text-right {
        text-align: left !important;
    }

    html[dir="rtl"] .d-flex.align-items-center.justify-content-between {
        flex-direction: row-reverse;
    }

    .account-orders-table th {
        font-weight: 700;
        background: #fafafa;
    }

    .account-orders-table td a {
        font-weight: 600;
    }

    .account-orders-table {
        min-width: 620px;
    }

    /* Desktop صغير / Tablet */
    @media (max-width: 1199.98px) {
        .account-sidebar {
            width: 240px;
            min-width: 240px;
            padding: 18px 12px;
        }

        .account-content {
            padding: 20px;
        }

        .account-sidebar a {
            font-size: 14px;
            padding: 11px 12px;
            gap: 10px;
        }

        .brand-mini img {
            height: 36px;
        }

        .card-header {
            font-size: 16px;
            padding: 14px 16px;
        }

        .card-body {
            padding: 16px;
        }
    }

    /* فون - نفس روح اللاب */
    @media (max-width: 767.98px) {
        .account-shell {
            flex-direction: row !important;
            align-items: stretch;
        }

        html[dir="rtl"] .account-shell {
            flex-direction: row-reverse !important;
        }

        .account-sidebar {
            width: 86px;
            min-width: 86px;
            padding: 14px 8px;
        }

        .brand-mini {
            justify-content: center;
            margin-bottom: 14px;
            padding: 0;
        }

        .brand-mini img {
            height: 28px;
        }

        .brand-mini > div {
            display: none;
        }

        .sidebar-section-title {
            display: none;
        }

        .account-sidebar a {
            justify-content: center;
            padding: 12px 8px;
            font-size: 0;
            gap: 0;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .account-sidebar a i {
            font-size: 18px;
            margin: 0 !important;
        }

        .logout-btn {
            height: 42px;
            font-size: 0;
            padding: 0;
        }

        .logout-btn i {
            font-size: 18px;
            margin: 0 !important;
        }

        .account-content {
            padding: 12px;
        }

        .card {
            border-radius: 14px;
        }

        .card-header {
            font-size: 14px;
            padding: 12px;
        }

        .card-body {
            padding: 12px;
        }

        .form-control {
            height: 42px;
            font-size: 13px;
        }

        .btn {
            min-height: 40px;
            font-size: 13px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    /* فون صغير */
    @media (max-width: 480px) {
        .account-sidebar {
            width: 72px;
            min-width: 72px;
            padding: 12px 6px;
        }

        .account-sidebar a i,
        .logout-btn i {
            font-size: 16px;
        }

        .account-content {
            padding: 10px;
        }

        .card-header {
            font-size: 13px;
            padding: 10px;
        }

        .card-body {
            padding: 10px;
        }

        .form-group label,
        .card-body p,
        .alert {
            font-size: 12px;
        }

        .form-control,
        .btn {
            font-size: 12px;
        }
    }
</style>
</head>
<body>
<div class="account-shell">

    <aside class="account-sidebar">
        <div class="brand-mini">
            <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}" alt="Creative Store">
            <div>
                <div style="font-weight:700; line-height:1;">Creative Store</div>
                <div style="opacity:.75; font-size:12px;">{{ __('account.my_account') }}</div>
            </div>
        </div>

        <div class="sidebar-section-title">{{ __('account.navigation') }}</div>

        <a href="{{ route('home') }}" class="nav-link">
            <i class="fas fa-home"></i> {{ __('account.back_to_site') }}
        </a>

        <a href="{{ route('myaccount') }}" class="nav-link">
            <i class="fas fa-user"></i> {{ __('account.profile') }}
        </a>

        <a href="{{ route('shipping.orders.index') }}" class="nav-link">
            <i class="fas fa-box"></i> {{ __('account.my_orders') }}
        </a>

        <a href="{{ route('shipping.addresses.index') }}" class="nav-link">
            <i class="fas fa-map-marker-alt"></i> {{ __('account.my_addresses') }}
        </a>

        <a href="{{ route('wishlist.index') }}" class="nav-link">
            <i class="fas fa-heart"></i> {{ __('account.wishlist') }}
        </a>

        @auth
            @if(auth()->user()->isDashboardUser())
                <div class="sidebar-section-title">{{ __('account.management') }}</div>

                <a href="{{ route('dashboard.home') }}" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> {{ __('account.dashboard') }}
                </a>
            @endif
        @endauth

        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-block logout-btn">
                <i class="fas fa-sign-out-alt mr-2"></i> {{ __('account.logout') }}
            </button>
        </form>
    </aside>

    <main class="account-content">
        @yield('content')
    </main>

</div>

<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('AdminLTE/dist/js/adminlte.min.js') }}"></script>
</body>
</html>
