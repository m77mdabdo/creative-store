@php
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    $cartCount = collect(session('cart', []))->sum('quantity');

    $wishlistCount = 0;
    if (auth()->check()) {
        $wishlistCount = \App\Models\WishlistItem::whereHas('wishlist', function ($q) {
            $q->where('user_id', auth()->id());
        })->count();
    }

    $homeUrl = LaravelLocalization::localizeUrl(route('home', [], false));
    $productsUrl = LaravelLocalization::localizeUrl(route('products.index', [], false));
    $blogUrl = LaravelLocalization::getLocalizedURL(app()->getLocale(), '/blog');
    $aboutUrl = LaravelLocalization::getLocalizedURL(app()->getLocale(), '/about');
    $contactUrl = LaravelLocalization::getLocalizedURL(app()->getLocale(), '/contact');


@endphp

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-app-nav-1.css') }}">

<div class="wrap-menu-desktop custom-navbar">
    <nav class="container navbar-inner">

        <a href="{{ $homeUrl }}" class="site-brand">
            <img src="{{ asset('cozastore/images/icons/logo-01.png') }}" alt="Creative Store Logo"
                style="width: 110px; height: 110px;">
        </a>

        <div class="menu-desktop">
            <ul class="main-menu">
                <li
                    class="{{ request()->is(app()->getLocale()) || request()->is(app()->getLocale() . '/') ? 'active-menu' : '' }}">
                    <a href="{{ $homeUrl }}">{{ __('site.home') }}</a>
                </li>

                <li class="{{ request()->is(app()->getLocale() . '/products*') ? 'active-menu' : '' }}">
                    <a href="{{ $productsUrl }}">{{ __('site.shop') }}</a>
                </li>

                <li class="{{ request()->is(app()->getLocale() . '/blog*') ? 'active-menu' : '' }}">
                    <a href="{{ $blogUrl }}">{{ __('site.blog') }}</a>
                </li>

                <li class="{{ request()->is(app()->getLocale() . '/about') ? 'active-menu' : '' }}">
                    <a href="{{ $aboutUrl }}">{{ __('site.about') }}</a>
                </li>

                <li class="{{ request()->is(app()->getLocale() . '/contact') ? 'active-menu' : '' }}">
                    <a href="{{ $contactUrl }}">{{ __('site.contact') }}</a>
                </li>
            </ul>
        </div>

        <div class="site-actions">
            <button type="button" class="icon-btn js-show-modal-search" aria-label="{{ __('site.search') }}">
                <i class="zmdi zmdi-search"></i>
            </button>

            <a href="{{ route('cart.index') }}" class="icon-btn icon-header-noti" data-notify="{{ $cartCount }}"
                aria-label="{{ __('site.your_cart') }}">
                <i class="zmdi zmdi-shopping-cart"></i>
            </a>

            <a href="{{ route('wishlist.index') }}" class="icon-btn icon-header-noti" data-notify="{{ $wishlistCount }}"
                aria-label="Wishlist">
                <i class="zmdi zmdi-favorite-outline"></i>
            </a>

            @auth
                {{-- <a href="{{ route('myaccount') }}" class="user-link">
                    {{ auth()->user()->name }}
                </a> --}}
            @endauth
        </div>

    </nav>
</div>

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-app-nav-2.css') }}">
