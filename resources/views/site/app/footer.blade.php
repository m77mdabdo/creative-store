<footer class="bg3 p-t-75 p-b-32">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">
                    {{ __('site.categories') }}
                </h4>

                <ul>
                    <li class="p-b-10">
                        <a href="{{ route('products.index', ['category' => 'women']) }}" class="stext-107 cl7 hov-cl1 trans-04">
                            {{ __('site.women') }}
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="{{ route('products.index', ['category' => 'men']) }}" class="stext-107 cl7 hov-cl1 trans-04">
                            {{ __('site.men') }}
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="{{ route('products.index') }}" class="stext-107 cl7 hov-cl1 trans-04">
                            {{ __('site.accessories') }}
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="{{ route('products.index') }}" class="stext-107 cl7 hov-cl1 trans-04">
                            {{ __('site.shop') }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">
                    {{ __('site.help') }}
                </h4>

                <ul>
                    <li class="p-b-10">
                        <a href="{{ route('tracking.search-form') }}" class="stext-107 cl7 hov-cl1 trans-04">
                            {{ __('site.track_order') }}
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                            {{ __('site.returns') }}
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                            {{ __('site.shipping') }}
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                            {{ __('site.faqs') }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">
                    {{ __('site.get_in_touch') }}
                </h4>

                <p class="stext-107 cl7 size-201">
                    {{ app()->getLocale() === 'ar'
                        ? 'هل لديك أي استفسار؟ تواصل معنا عبر صفحة التواصل الخاصة بنا.'
                        : 'Any questions? Let us know through our contact page.' }}
                </p>

                <div class="p-t-27">
                    <a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16"><i class="fa fa-facebook"></i></a>
                    <a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16"><i class="fa fa-instagram"></i></a>
                    <a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16"><i class="fa fa-pinterest-p"></i></a>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">
                    {{ __('site.newsletter') }}
                </h4>

                <form>
                    <div class="wrap-input1 w-full p-b-4">
                        <input class="input1 bg-none plh1 stext-107 cl7"
                               type="email"
                               name="email"
                               placeholder="{{ __('site.email_placeholder') }}">
                        <div class="focus-input1 trans-04"></div>
                    </div>

                    <div class="p-t-18">
                        <button type="submit" class="flex-c-m stext-101 cl0 size-103 bg1 bor1 hov-btn2 p-lr-15 trans-04">
                            {{ __('site.subscribe') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</footer>

<div class="btn-back-to-top" id="myBtn">
    <span class="symbol-btn-back-to-top">
        <i class="zmdi zmdi-chevron-up"></i>
    </span>
</div>
