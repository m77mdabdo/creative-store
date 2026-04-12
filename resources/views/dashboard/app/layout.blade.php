<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
@include('dashboard.app.head')

<body class="hold-transition sidebar-mini layout-fixed text-sm">
<div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake"
             src="{{ asset('AdminLTE/dist/img/AdminLTELogo.png') }}"
             alt="Creative Store Logo"
             height="60"
             width="60">
    </div>

    @include('dashboard.app.nav')
    @include('dashboard.app.sidebar')

    @yield('content')

    @include('dashboard.app.footer')
</div>

@include('dashboard.app.script')
@stack('scripts')
</body>
</html>
