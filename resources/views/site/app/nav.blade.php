<div class="wrap-menu-desktop">
    <nav class="limiter-menu-desktop container">

        <!-- Logo desktop -->
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('cozastore/images/icons/logo-01.png') }}" alt="IMG-LOGO">
        </a>

        <!-- Menu desktop -->
        <div class="menu-desktop">
            <ul class="main-menu">
                <li class="active-menu">
                    <a href="{{ url('/') }}">Home</a>

                </li>

                <li>
                    <a href="{{ url('/products') }}">Shop</a>
                </li>

                {{-- <li class="label1" data-label1="hot">
                    <a href="{{ url('/cart') }}">Features</a>
                </li> --}}

                <li>
                    <a href="{{ url('/blog') }}">Blog</a>
                </li>

                <li>
                    <a href="{{ url('/about') }}">About</a>
                </li>

                <li>
                    <a href="{{ url('/contact') }}">Contact</a>
                </li>
            </ul>
        </div>

        <!-- Icon header -->
        <div class="wrap-icon-header flex-w flex-r-m">
            <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
                <i class="zmdi zmdi-search"></i>
            </div>

            <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart" data-notify="2">
                <i class="zmdi zmdi-shopping-cart"></i>
            </div>

            <a href="{{ url('/wishlist') }}"
               class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti"
               data-notify="0">
                <i class="zmdi zmdi-favorite-outline"></i>
            </a>
        </div>

    </nav>
</div>

