<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'My Account') | Creative Store</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

    <style>
        body{ background:#f5f6f8; }
        .account-shell{ min-height:100vh; display:flex; }
        .account-sidebar{
            width:280px; background:#111; color:#fff; padding:22px;
        }
        .account-sidebar a{ color:#fff; opacity:.9; text-decoration:none; display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; }
        .account-sidebar a:hover{ background:rgba(255,255,255,.08); }
        .account-content{ flex:1; padding:28px; }
        .brand-mini{ display:flex; align-items:center; gap:12px; margin-bottom:18px; }
        .brand-mini img{ height:42px; width:auto; }
        .card{ border-radius:14px; }
    </style>
</head>
<body>
<div class="account-shell">

    <aside class="account-sidebar">
        <div class="brand-mini">
            <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}" alt="Creative Store">
            <div>
                <div style="font-weight:700; line-height:1;">Creative Store</div>
                <div style="opacity:.75; font-size:12px;">My Account</div>
            </div>
        </div>

       <a href="{{ route('home') }}" class="nav-link">
    <i class="fas fa-home me-1"></i> Back to site
</a>

<a href="{{ route('myaccount') }}" class="nav-link">
    <i class="fas fa-user me-1"></i> Profile
</a>

@auth
    @if(auth()->user()->isDashboardUser())
        <a href="{{ route('dashboard') }}"
           class="nav-link" style="border-radius:10px;">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
        </a>
    @endif
@endauth






        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-block" style="border-radius:10px;">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
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
