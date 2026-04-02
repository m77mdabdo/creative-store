<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    @include('site.app.head')
</head>
<body class="animsition site-body {{ app()->getLocale() === 'ar' ? 'rtl-site' : 'ltr-site' }}">

    @auth
        <script>
            window.Laravel = {
                userId: {{ auth()->id() }}
            };
        </script>
    @endauth

    @include('site.app.header')

    <main class="main-content">
        @yield('content')
    </main>

    @include('site.app.footer')
    @include('site.app.script')

    @stack('scripts')
</body>
</html>
