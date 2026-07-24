@php
    $seoSiteName = config('app.name', 'Creative Store');
    $seoTitle = trim($__env->yieldContent('title'));
    $seoTitle = $seoTitle !== '' ? $seoTitle . ' | ' . $seoSiteName : $seoSiteName . ' | ' . __('site.seo_tagline');
    $seoDescription = \Illuminate\Support\Str::limit(trim(strip_tags($__env->yieldContent('meta_description'))) ?: __('site.seo_description'), 160);
    $seoImage = trim($__env->yieldContent('og_image')) ?: asset('cozastore/images/slide-01.jpg');
    $seoType = trim($__env->yieldContent('og_type')) ?: 'website';
    $seoLocales = array_keys(config('laravellocalization.supportedLocales', ['ar' => [], 'en' => []]));
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<link rel="canonical" href="{{ url()->current() }}">
@foreach ($seoLocales as $seoLocale)
<link rel="alternate" hreflang="{{ $seoLocale }}" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($seoLocale, null, [], true) }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('ar', null, [], true) }}">
<meta property="og:site_name" content="{{ $seoSiteName }}">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_SA' : 'en_US' }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#111111">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="icon" type="image/png" href="{{ asset('cozastore/images/icons/favicon.png') }}"/>

<link rel="stylesheet" href="{{ asset('cozastore/vendor/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/fonts/iconic/css/material-design-iconic-font.min.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/fonts/linearicons-v1.0.0/icon-font.min.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/vendor/animate/animate.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/vendor/css-hamburgers/hamburgers.min.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/vendor/animsition/css/animsition.min.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/vendor/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/vendor/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/vendor/slick/slick.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/vendor/MagnificPopup/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/vendor/perfect-scrollbar/perfect-scrollbar.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/css/util.css') }}">
<link rel="stylesheet" href="{{ asset('cozastore/css/main.css') }}">
<link rel="stylesheet" href="{{ versioned_asset('css/site/tokens.css') }}">
<link rel="stylesheet" href="{{ versioned_asset('css/site/base.css') }}">
<link rel="stylesheet" href="{{ versioned_asset('css/site/site-app-head-1.css') }}">

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.querySelector('.btn-show-menu-mobile');
    const drawer    = document.querySelector('.js-mobile-drawer');
    const overlay   = document.querySelector('.js-mobile-overlay');
    const closeBtn  = document.querySelector('.js-close-mobile-menu');

    function openMenu() {
        drawer?.classList.add('is-open');
        overlay?.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        hamburger?.classList.add('is-active');
    }

    function closeMenu() {
        drawer?.classList.remove('is-open');
        overlay?.classList.remove('is-open');
        document.body.style.overflow = '';
        hamburger?.classList.remove('is-active');
    }

    hamburger?.addEventListener('click', function () {
        drawer?.classList.contains('is-open') ? closeMenu() : openMenu();
    });

    overlay?.addEventListener('click', closeMenu);
    closeBtn?.addEventListener('click', closeMenu);
});
</script>
<link rel="stylesheet" href="{{ versioned_asset('css/site/site-app-head-2.css') }}">
