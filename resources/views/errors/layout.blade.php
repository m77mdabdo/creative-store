<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<title>Error @yield('code')</title>

<link rel="stylesheet" href="{{ versioned_asset('css/site/errors-layout.css') }}">

    <link rel="stylesheet" href="{{ versioned_asset('css/site/tokens.css') }}">
</head>

<body>

<div class="error-box">

<div class="error-code">
@yield('code')
</div>

<div class="error-message">
@yield('message')
</div>

<div class="error-actions">
<a href="{{ url('/') }}">Back to Home</a>
</div>

</div>

</body>
</html>

