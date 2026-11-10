<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('account.my_account')) | Creative Store</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

    <link rel="stylesheet" href="{{ versioned_asset('css/site/tokens.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/base.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/myaccount-layout.css') }}">
    @stack('styles')
</head>
<body>

<button class="cs-mobile-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<div class="cs-overlay" id="sidebarOverlay"></div>

<aside class="cs-sidebar" id="sidebar">

    <a href="{{ route('home') }}" class="cs-brand">
        <div class="cs-brand-logo">
            <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}" alt="Creative Store" decoding="async">
        </div>
        <div class="cs-brand-text">
            <div class="cs-brand-name">Creative Store</div>
            <div class="cs-brand-sub">{{ __('account.my_account') }}</div>
        </div>
    </a>

    @auth
    <div class="cs-user-card">
        <div class="cs-avatar">
            @if (auth()->user()->avatarUrl())
                <img src="{{ auth()->user()->avatarUrl() }}" alt="" width="38" height="38">
            @else
                {{ auth()->user()->initials() }}
            @endif
        </div>
        <div class="cs-user-info">
            <div class="cs-user-name">{{ auth()->user()->name }}</div>
            <div class="cs-user-email">{{ auth()->user()->email }}</div>
        </div>
    </div>
    @endauth

    <nav class="cs-nav">
        <div class="cs-nav-label">{{ __('account.navigation') }}</div>

        <a href="{{ route('home') }}" class="cs-nav-link">
            <i class="fas fa-home"></i>
            {{ __('account.back_to_site') }}
        </a>

        <a href="{{ route('myaccount') }}" class="cs-nav-link {{ request()->routeIs('myaccount') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            {{ __('account.profile') }}
        </a>

        <a href="{{ route('shipping.orders.index') }}" class="cs-nav-link {{ request()->routeIs('shipping.orders.*') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            {{ __('account.my_orders') }}
        </a>

        <a href="{{ route('shipping.addresses.index') }}" class="cs-nav-link {{ request()->routeIs('shipping.addresses.*') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i>
            {{ __('account.my_addresses') }}
        </a>

        <a href="{{ route('wishlist.index') }}" class="cs-nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
            <i class="fas fa-heart"></i>
            {{ __('account.wishlist') }}
        </a>

        @auth
            @if(auth()->user()->isDashboardUser())
                <div class="cs-nav-label">{{ __('account.management') }}</div>
                <a href="{{ route('dashboard.home') }}" class="cs-nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    {{ __('account.dashboard') }}
                </a>
            @endif
        @endauth
    </nav>

    <div class="cs-sidebar-bottom">
        <form action="{{ route('logout') }}" method="POST" class="cs-logout-form">
            @csrf
            <button type="submit" class="cs-logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                {{ __('account.logout') }}
            </button>
        </form>
    </div>

</aside>

<main class="cs-main">
    @yield('content')
</main>

<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
const sidebar = document.getElementById('sidebar');
const toggle  = document.getElementById('sidebarToggle');
const overlay = document.getElementById('sidebarOverlay');

function openSidebar()  { sidebar.classList.add('open');  overlay.classList.add('open');  toggle.innerHTML = '<i class="fas fa-times"></i>'; }
function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('open'); toggle.innerHTML = '<i class="fas fa-bars"></i>'; }

toggle.addEventListener('click', () => sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
overlay.addEventListener('click', closeSidebar);
</script>
@stack('scripts')
</body>
</html>
