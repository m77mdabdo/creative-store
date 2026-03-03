<div class="wrap-menu-desktop">
    <nav class="limiter-menu-desktop container">

        <!-- Logo desktop -->
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('cozastore/images/icons/logo-01.png') }}" alt="IMG-LOGO">
        </a>

        <!-- Menu desktop -->
        <div class="menu-desktop">
            <ul class="main-menu">
                <li class="{{ request()->is('/') ? 'active-menu' : '' }}">
                    <a href="{{ url('/') }}">Home</a>
                </li>

                <li class="{{ request()->is('products*') ? 'active-menu' : '' }}">
                    <a href="{{ url('/products') }}">Shop</a>
                </li>

                <li class="{{ request()->is('blog*') ? 'active-menu' : '' }}">
                    <a href="{{ url('/blog') }}">Blog</a>
                </li>

                <li class="{{ request()->is('about') ? 'active-menu' : '' }}">
                    <a href="{{ url('/about') }}">About</a>
                </li>

                <li class="{{ request()->is('contact') ? 'active-menu' : '' }}">
                    <a href="{{ url('/contact') }}">Contact</a>
                </li>
            </ul>
        </div>

        {{-- ================= ICONS (نسخة واحدة فقط) ================= --}}
        @php
            $cart = session()->get('cart', []);
            $cartCount = collect($cart)->sum('quantity');
        @endphp

        <div class="wrap-icon-header flex-w flex-r-m">

            {{-- Search --}}
            <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
                <i class="zmdi zmdi-search"></i>
            </div>

            {{-- Cart --}}
            <a href="{{ route('cart.index') }}">
            @php
                $cartCount = collect(session('cart', []))->sum('quantity');
            @endphp

            <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart"
                data-notify="{{ $cartCount }}">
                <i class="zmdi zmdi-shopping-cart"></i>
            </div>
            </a>

            {{-- Wishlist --}}
            <a href="{{ url('/wishlist') }}"
                class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="0">
                <i class="zmdi zmdi-favorite-outline"></i>
            </a>

        </div>

    </nav>
</div>
