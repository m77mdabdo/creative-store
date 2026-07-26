<link rel="stylesheet" href="{{ versioned_asset('css/site/site-app-footer.css') }}">

<footer class="bg3 p-t-75 p-b-32">
    <div class="container">

        <div class="footer-logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('cozastore/images/icons/logo-01.png') }}" alt="Creative Store" decoding="async">
            </a>
        </div>

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
                        <a href="{{ route('frontend.pages.show', 'returns') }}" class="stext-107 cl7 hov-cl1 trans-04">
                            {{ __('site.returns') }}
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="{{ route('frontend.pages.show', 'shipping-policy') }}" class="stext-107 cl7 hov-cl1 trans-04">
                            {{ __('site.shipping') }}
                        </a>
                    </li>

                    <li class="p-b-10">
                        <a href="{{ route('frontend.pages.show', 'faq') }}" class="stext-107 cl7 hov-cl1 trans-04">
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

                <div class="footer-social p-t-20">
                    @php($socialIcons = ['instagram' => 'fa-instagram', 'tiktok' => 'fa-music', 'x' => 'fa-twitter', 'facebook' => 'fa-facebook', 'snapchat' => 'fa-snapchat-ghost'])
                    @foreach (config('store.social', []) as $network => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($network) }}"><i class="fa {{ $socialIcons[$network] ?? 'fa-link' }}"></i></a>
                    @endforeach
                </div>

                <div class="footer-payments">
                    <span class="payment-badge">VISA</span>
                    <span class="payment-badge">MADA</span>
                    <span class="payment-badge">Apple Pay</span>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">
                    {{ __('site.newsletter') }}
                </h4>

                <div class="newsletter-box">
                    <form action="{{ route('newsletter.store') }}" method="POST">
                        @csrf
                        <div class="wrap-input1 w-full p-b-4">
                            <input class="input1 bg-none plh1 stext-107 cl7"
                                   type="email"
                                   required
                                   autocomplete="email"
                                   name="email"
                                   placeholder="{{ __('site.email_placeholder') }}">
                            <div class="focus-input1 trans-04"></div>
                        </div>

                        <button type="submit" class="flex-c-m stext-101 cl0 size-103 bg1 bor1 hov-btn2 p-lr-15 trans-04">
                            {{ __('site.subscribe') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-wrap">
                <div class="footer-copy">
                    &copy; {{ date('Y') }} Creative Store. {{ app()->getLocale() === 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}
                </div>

                <div class="footer-bottom-links">
                    <a href="{{ route('frontend.pages.show', 'privacy') }}">{{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}</a>
                    <a href="{{ route('frontend.pages.show', 'terms') }}">{{ app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms & Conditions' }}</a>
                    <a href="{{ route('contact.index') }}">{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Us' }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="btn-back-to-top" id="myBtn">
    <span class="symbol-btn-back-to-top">
        <i class="zmdi zmdi-chevron-up"></i>
    </span>
</div>
