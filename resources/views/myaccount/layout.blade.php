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

<style>
:root {
    --accent:        #717fe0;
    --accent-dark:   #6c7ae0;
    --accent-soft:   rgba(113,127,224,.10);
    --accent-glow:   rgba(113,127,224,.25);
    --accent-hover:  #5a6ad0;

    --bg-page:       #f5f5f5;
    --bg-sidebar:    #222222;
    --bg-card:       #ffffff;
    --bg-input:      #f7f7f7;

    --text-primary:  #333333;
    --text-secondary:#555555;
    --text-muted:    #888888;
    --text-white:    #ffffff;
    --text-white-muted: rgba(255,255,255,.6);

    --border:        #e6e6e6;
    --border-focus:  rgba(113,127,224,.5);

    --success:       #28a745;
    --success-soft:  rgba(40,167,69,.08);
    --danger:        #dc3545;

    --radius-sm:     4px;
    --radius-md:     8px;
    --radius-lg:     12px;
    --sidebar-w:     260px;

    --font-main:     'Poppins', sans-serif;
    --font-ar:       'Cairo', sans-serif;
    --transition:    .2s ease;
    --shadow-sm:     0 2px 8px rgba(0,0,0,.06);
    --shadow-md:     0 4px 20px rgba(0,0,0,.08);
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
    display: flex;
    flex-direction: column;
    z-index: 100;
    transition: transform var(--transition);
    box-shadow: 2px 0 20px rgba(0,0,0,.15);
}

html[dir="rtl"] .cs-sidebar {
    left: auto; right: 0;
    box-shadow: -2px 0 20px rgba(0,0,0,.15);
}

/* Brand */
.cs-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    padding: 22px 20px 18px;
    border-bottom: 1px solid rgba(255,255,255,.07);
}

html[dir="rtl"] .cs-brand { flex-direction: row-reverse; }

.cs-brand-logo {
    width: 40px; height: 40px;
    border-radius: var(--radius-sm);
    background: rgba(113,127,224,.2);
    border: 1px solid rgba(113,127,224,.4);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.cs-brand-logo img { width: 28px; height: 28px; object-fit: contain; }

.cs-brand-text { line-height: 1.25; }
.cs-brand-name {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-white);
    letter-spacing: -.2px;
}
.cs-brand-sub {
    font-size: 11px;
    color: var(--text-white-muted);
    margin-top: 1px;
}

/* User Card */
.cs-user-card {
    margin: 14px 14px 0;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: var(--radius-md);
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 11px;
}

html[dir="rtl"] .cs-user-card { flex-direction: row-reverse; }

.cs-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent), #a78bfa);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}

.cs-user-info { min-width: 0; flex: 1; }
.cs-user-name  { font-size: 13px; font-weight: 600; color: var(--text-white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cs-user-email { font-size: 11px; color: var(--text-white-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 1px; }

/* Nav */
.cs-nav {
    flex: 1;
    overflow-y: auto;
    padding: 14px 10px;
    scrollbar-width: none;
}
.cs-nav::-webkit-scrollbar { display: none; }

.cs-nav-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: rgba(255,255,255,.3);
    padding: 0 10px;
    margin: 18px 0 6px;
}
.cs-nav-label:first-child { margin-top: 4px; }

.cs-nav-link {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 10px 12px;
    border-radius: var(--radius-sm);
    color: rgba(255,255,255,.65);
    text-decoration: none;
    font-size: 14px;
    font-weight: 400;
    transition: var(--transition);
    margin-bottom: 2px;
}

html[dir="rtl"] .cs-nav-link { flex-direction: row-reverse; }

.cs-nav-link i {
    width: 16px;
    text-align: center;
    font-size: 14px;
    flex-shrink: 0;
    color: rgba(255,255,255,.4);
    transition: var(--transition);
}

.cs-nav-link:hover {
    background: rgba(255,255,255,.07);
    color: #fff;
    text-decoration: none;
}

.cs-nav-link:hover i { color: var(--accent); }

.cs-nav-link.active {
    background: var(--accent);
    color: #fff;
    font-weight: 500;
}

.cs-nav-link.active i { color: #fff; }

/* Bottom */
.cs-sidebar-bottom {
    padding: 12px 10px 16px;
    border-top: 1px solid rgba(255,255,255,.07);
}

.cs-logout-form { margin: 0; }

.cs-logout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px;
    border-radius: var(--radius-sm);
    background: rgba(220,53,69,.1);
    border: 1px solid rgba(220,53,69,.2);
    color: #f87171;
    font-size: 13px;
    font-weight: 500;
    font-family: var(--font-main);
    cursor: pointer;
    transition: var(--transition);
}

.cs-logout-btn:hover {
    background: rgba(220,53,69,.18);
    border-color: rgba(220,53,69,.4);
    color: #fca5a5;
}

/* ─── Main ─── */
.cs-main {
    margin-left: var(--sidebar-w);
    min-height: 100vh;
    padding: 32px 36px;
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
    width: 40px; height: 40px;
    border-radius: var(--radius-sm);
    background: var(--bg-sidebar);
    border: none;
    color: var(--text-white);
    font-size: 16px;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    box-shadow: var(--shadow-md);
}

html[dir="rtl"] .cs-mobile-toggle { left: auto; right: 14px; }

.cs-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 99;
}

/* ─── Responsive ─── */
@media (max-width: 991px) {
    .cs-mobile-toggle { display: flex; }

    .cs-sidebar {
        transform: translateX(-100%);
    }

    html[dir="rtl"] .cs-sidebar { transform: translateX(100%); }
    html[dir="rtl"] .cs-sidebar.open { transform: translateX(0); }

    .cs-sidebar.open { transform: translateX(0); }
    .cs-overlay.open { display: block; }

    .cs-main {
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding: 68px 16px 24px;
    }
}

@media (max-width: 480px) {
    .cs-main { padding: 64px 12px 20px; }
}
</style>
</head>
<body>

<button class="cs-mobile-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<div class="cs-overlay" id="sidebarOverlay"></div>

<aside class="cs-sidebar" id="sidebar">

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
</body>
</html>
