<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('account.my_account')) | Creative Store</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

<style>
:root {
    --bg-page:       #0b0c0f;
    --bg-sidebar:    #0f1014;
    --bg-card:       #16181d;
    --bg-card-hover: #1c1e25;
    --bg-input:      #1e2028;
    --accent:        #6c63ff;
    --accent-soft:   rgba(108,99,255,.12);
    --accent-glow:   rgba(108,99,255,.35);
    --text-primary:  #f0f1f5;
    --text-secondary:#8a8fa8;
    --text-muted:    #4e5268;
    --border:        rgba(255,255,255,.06);
    --border-hover:  rgba(108,99,255,.4);
    --radius-sm:     8px;
    --radius-md:     14px;
    --radius-lg:     20px;
    --sidebar-w:     260px;
    --font-main:     'Sora', sans-serif;
    --font-ar:       'Cairo', sans-serif;
    --transition:    .22s cubic-bezier(.4,0,.2,1);
}

html[lang="ar"] { --font-main: 'Cairo', sans-serif; }

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: var(--font-main);
    background: var(--bg-page);
    color: var(--text-primary);
    overflow-x: hidden;
    min-height: 100vh;
}

/* ─── Sidebar ─── */
.cs-sidebar {
    position: fixed;
    top: 0; left: 0;
    width: var(--sidebar-w);
    height: 100vh;
    background: var(--bg-sidebar);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    padding: 0;
    z-index: 100;
    transition: transform var(--transition);
}

html[dir="rtl"] .cs-sidebar {
    left: auto; right: 0;
    border-right: none;
    border-left: 1px solid var(--border);
}

.cs-sidebar-top {
    padding: 28px 20px 20px;
    border-bottom: 1px solid var(--border);
}

.cs-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    margin-bottom: 20px;
}

.cs-brand-logo {
    width: 38px; height: 38px;
    border-radius: var(--radius-sm);
    background: var(--accent-soft);
    border: 1px solid var(--accent-glow);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.cs-brand-logo img { width: 26px; height: 26px; object-fit: contain; }

.cs-brand-text { line-height: 1.2; }
.cs-brand-name { font-size: 15px; font-weight: 700; color: var(--text-primary); }
.cs-brand-sub  { font-size: 11px; color: var(--text-muted); margin-top: 1px; }

/* User Card */
.cs-user-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.cs-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent), #a78bfa);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    letter-spacing: -.5px;
}

.cs-user-info { min-width: 0; flex: 1; }
.cs-user-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cs-user-email { font-size: 11px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Nav */
.cs-nav {
    flex: 1;
    overflow-y: auto;
    padding: 16px 12px;
    scrollbar-width: none;
}
.cs-nav::-webkit-scrollbar { display: none; }

.cs-nav-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 0 8px;
    margin: 16px 0 6px;
}
.cs-nav-label:first-child { margin-top: 0; }

.cs-nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 12px;
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: var(--transition);
    position: relative;
    margin-bottom: 2px;
}

html[dir="rtl"] .cs-nav-link { flex-direction: row-reverse; }

.cs-nav-link:hover {
    background: var(--bg-card);
    color: var(--text-primary);
}

.cs-nav-link.active {
    background: var(--accent-soft);
    color: var(--accent);
    border: 1px solid rgba(108,99,255,.2);
}

.cs-nav-link .cs-nav-icon {
    width: 32px; height: 32px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    background: rgba(255,255,255,.04);
    flex-shrink: 0;
    transition: var(--transition);
}

.cs-nav-link:hover .cs-nav-icon { background: rgba(255,255,255,.08); }
.cs-nav-link.active .cs-nav-icon { background: var(--accent-soft); color: var(--accent); }

/* Sidebar Bottom */
.cs-sidebar-bottom {
    padding: 16px 12px;
    border-top: 1px solid var(--border);
}

.cs-logout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 11px;
    border-radius: var(--radius-sm);
    background: rgba(239,68,68,.08);
    border: 1px solid rgba(239,68,68,.15);
    color: #f87171;
    font-size: 14px;
    font-weight: 600;
    font-family: var(--font-main);
    cursor: pointer;
    transition: var(--transition);
}

.cs-logout-btn:hover {
    background: rgba(239,68,68,.15);
    border-color: rgba(239,68,68,.35);
    color: #fca5a5;
}

/* ─── Main Content ─── */
.cs-main {
    margin-left: var(--sidebar-w);
    min-height: 100vh;
    padding: 32px 36px;
    background: var(--bg-page);
}

html[dir="rtl"] .cs-main {
    margin-left: 0;
    margin-right: var(--sidebar-w);
}

/* ─── Mobile Toggle ─── */
.cs-mobile-toggle {
    display: none;
    position: fixed;
    top: 14px;
    left: 14px;
    z-index: 200;
    width: 42px; height: 42px;
    border-radius: var(--radius-sm);
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text-primary);
    font-size: 18px;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

html[dir="rtl"] .cs-mobile-toggle { left: auto; right: 14px; }
.cs-mobile-toggle:hover { border-color: var(--border-hover); }

.cs-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.6);
    backdrop-filter: blur(2px);
    z-index: 99;
}

/* ─── Responsive ─── */
@media (max-width: 991px) {
    .cs-mobile-toggle { display: flex; }

    .cs-sidebar {
        transform: translateX(-100%);
        box-shadow: 4px 0 40px rgba(0,0,0,.5);
    }

    html[dir="rtl"] .cs-sidebar { transform: translateX(100%); }

    .cs-sidebar.open {
        transform: translateX(0);
    }

    .cs-overlay.open { display: block; }

    .cs-main {
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding: 70px 16px 24px;
    }
}

@media (max-width: 480px) {
    .cs-main { padding: 66px 12px 20px; }
}
</style>
</head>
<body>

<!-- Mobile Toggle -->
<button class="cs-mobile-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Overlay -->
<div class="cs-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="cs-sidebar" id="sidebar">

    <div class="cs-sidebar-top">
        <a href="{{ route('home') }}" class="cs-brand">
            <div class="cs-brand-logo">
                <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}" alt="Logo">
            </div>
            <div class="cs-brand-text">
                <div class="cs-brand-name">Creative Store</div>
                <div class="cs-brand-sub">{{ __('account.my_account') }}</div>
            </div>
        </a>

        @auth
        <div class="cs-user-card">
            <div class="cs-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="cs-user-info">
                <div class="cs-user-name">{{ auth()->user()->name }}</div>
                <div class="cs-user-email">{{ auth()->user()->email }}</div>
            </div>
        </div>
        @endauth
    </div>

    <nav class="cs-nav">
        <div class="cs-nav-label">{{ __('account.navigation') }}</div>

        <a href="{{ route('home') }}" class="cs-nav-link">
            <span class="cs-nav-icon"><i class="fas fa-home"></i></span>
            {{ __('account.back_to_site') }}
        </a>

        <a href="{{ route('myaccount') }}" class="cs-nav-link {{ request()->routeIs('myaccount') ? 'active' : '' }}">
            <span class="cs-nav-icon"><i class="fas fa-user"></i></span>
            {{ __('account.profile') }}
        </a>

        <a href="{{ route('shipping.orders.index') }}" class="cs-nav-link {{ request()->routeIs('shipping.orders.*') ? 'active' : '' }}">
            <span class="cs-nav-icon"><i class="fas fa-box"></i></span>
            {{ __('account.my_orders') }}
        </a>

        <a href="{{ route('shipping.addresses.index') }}" class="cs-nav-link {{ request()->routeIs('shipping.addresses.*') ? 'active' : '' }}">
            <span class="cs-nav-icon"><i class="fas fa-map-marker-alt"></i></span>
            {{ __('account.my_addresses') }}
        </a>

        <a href="{{ route('wishlist.index') }}" class="cs-nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
            <span class="cs-nav-icon"><i class="fas fa-heart"></i></span>
            {{ __('account.wishlist') }}
        </a>

        @auth
            @if(auth()->user()->isDashboardUser())
                <div class="cs-nav-label">{{ __('account.management') }}</div>
                <a href="{{ route('dashboard.home') }}" class="cs-nav-link">
                    <span class="cs-nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                    {{ __('account.dashboard') }}
                </a>
            @endif
        @endauth
    </nav>

    <div class="cs-sidebar-bottom">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="cs-logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                {{ __('account.logout') }}
            </button>
        </form>
    </div>

</aside>

<!-- Main Content -->
<main class="cs-main">
    @yield('content')
</main>

<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
const sidebar  = document.getElementById('sidebar');
const toggle   = document.getElementById('sidebarToggle');
const overlay  = document.getElementById('sidebarOverlay');

function openSidebar()  { sidebar.classList.add('open');  overlay.classList.add('open');  toggle.innerHTML = '<i class="fas fa-times"></i>'; }
function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('open'); toggle.innerHTML = '<i class="fas fa-bars"></i>'; }

toggle.addEventListener('click', () => sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
overlay.addEventListener('click', closeSidebar);
</script>
</body>
</html>
