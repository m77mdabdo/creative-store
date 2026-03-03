<header>

    <!-- ================= DESKTOP HEADER ================= -->
    <div class="container-menu-desktop">

        <!-- Topbar -->
        <div class="top-bar">
            <div class="content-topbar flex-sb-m h-full container">
                <div class="left-top-bar">
                    Free shipping for standard order over $100
                </div>

                <div class="right-top-bar flex-w h-full">

                    <a href="#" class="flex-c-m trans-04 p-lr-25">
                        Help & FAQs
                    </a>

                    @auth
                        <a href="{{ route('myaccount') }}" class="flex-c-m p-lr-10 trans-04">
                            My Account
                        </a>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="flex-c-m p-lr-10 trans-04">
                            Sign In
                        </a>

                        <a href="{{ route('register') }}" class="flex-c-m p-lr-10 trans-04">
                            Sign Up
                        </a>
                    @endguest

                    <a href="#" class="flex-c-m trans-04 p-lr-25">EN</a>
                    <a href="#" class="flex-c-m trans-04 p-lr-25">USD</a>

                </div>
            </div>
        </div>

        @include('site.app.nav')

        {{-- ================= DESKTOP ICONS ================= --}}
        @php
            $cart = session()->get('cart', []);
            $cartCount = collect($cart)->sum('quantity');
        @endphp


    </div>

    <!-- ================= MOBILE HEADER ================= -->
    <div class="wrap-header-mobile">

        <div class="logo-mobile">
            <a href="{{ url('/') }}">
                <img style="height:90px;width:auto;"
                     src="{{ asset('cozastore/images/icons/logo-01.png') }}"
                     alt="IMG-LOGO">
            </a>
        </div>



        <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </div>

    </div>


    <!-- ================= CART PANEL (نسخة واحدة فقط) ================= -->
    {{-- @include('site.Cart.cart-panel') --}}


    <!-- ================= SEARCH MODAL ================= -->
    <div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
        <div class="container-search-header">

            <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search" type="button">
                <img src="{{ asset('cozastore/images/icons/icon-close2.png') }}" alt="CLOSE">
            </button>

            <form class="wrap-search-header flex-w p-l-15"
                  method="GET"
                  action="{{ url()->current() }}">

                @foreach(request()->except(['q','page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach

                <button class="flex-c-m trans-04" type="submit">
                    <i class="zmdi zmdi-search"></i>
                </button>

                <input class="plh3"
                       type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Search products...">

            </form>
        </div>
    </div>

</header>
