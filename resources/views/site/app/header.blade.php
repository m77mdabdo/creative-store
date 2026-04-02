@php
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
@endphp

<header class="site-header">
    <div class="container-menu-desktop">
        <div class="top-bar custom-topbar">
            <div class="content-topbar container">
                <div class="left-top-bar">
                    {{ __('site.free_shipping_over_100') }}
                </div>

                <div class="right-top-bar">
                    <a href="#" class="topbar-link">
                        {{ __('site.help_faqs') }}
                    </a>

                    @auth
                        <a href="{{ route('myaccount') }}" class="topbar-link">
                            {{ __('site.my_account') }}
                        </a>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="topbar-link">
                            {{ __('site.sign_in') }}
                        </a>

                        <a href="{{ route('register') }}" class="topbar-link">
                            {{ __('site.sign_up') }}
                        </a>
                    @endguest

                    <div class="topbar-divider"></div>

                    <a href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}"
                       class="topbar-link language-link {{ app()->getLocale() === 'ar' ? 'is-active' : '' }}">
                        العربية
                    </a>

                    <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                       class="topbar-link language-link {{ app()->getLocale() === 'en' ? 'is-active' : '' }}">
                        English
                    </a>

                    <div class="topbar-divider"></div>

                    <a href="#" class="topbar-link currency-link">
                        USD
                    </a>
                </div>
            </div>
        </div>

        @include('site.app.nav')
    </div>

    <div class="wrap-header-mobile custom-mobile-header">
        <div class="logo-mobile">
            <a href="{{ LaravelLocalization::localizeUrl(route('home', [], false)) }}" class="mobile-brand">
                <img src="{{ asset('cozastore/images/icons/logo-01.png') }}" alt="Creative Store Logo">
                <span class="mobile-brand-text">{{ config('app.name', 'Creative Store') }}</span>
            </a>
        </div>

        <div class="wrap-icon-header mobile-icons">
            <a href="{{ route('cart.index') }}"
               class="icon-header-item icon-header-noti"
               data-notify="{{ collect(session('cart', []))->sum('quantity') }}">
                <i class="zmdi zmdi-shopping-cart"></i>
            </a>
        </div>

        <div class="btn-show-menu-mobile hamburger hamburger--squeeze" aria-label="Open menu" tabindex="0">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </div>
    </div>

    <div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
        <div class="container-search-header">
            <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search" type="button">
                <img src="{{ asset('cozastore/images/icons/icon-close2.png') }}" alt="Close">
            </button>

            <form class="wrap-search-header flex-w p-l-15" method="GET" action="{{ url()->current() }}">
                @foreach(request()->except(['q', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach

                <button class="flex-c-m trans-04" type="submit">
                    <i class="zmdi zmdi-search"></i>
                </button>

                <input class="plh3"
                       type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="{{ __('site.search_products') }}">
            </form>
        </div>
    </div>
</header>
