<style>
    footer.bg3 {
        overflow: hidden;
        position: relative;
        padding-top: 45px !important;
        padding-bottom: 18px !important;
    }

    .footer-logo {
        text-align: center;
        margin-bottom: 18px;
    }

    .footer-logo img {
        max-width: 110px;
        width: 100%;
        height: auto;
        transition: transform .3s ease;
    }

    .footer-logo a:hover img {
        transform: translateY(-2px);
    }

    footer.bg3 h4 {
        line-height: 1.4;
        word-break: break-word;
        padding-bottom: 16px !important;
        margin-bottom: 0;
        font-size: 18px;
    }

    footer.bg3 ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    footer.bg3 ul li {
        padding-bottom: 8px !important;
    }

    footer.bg3 ul li a,
    footer.bg3 p,
    footer.bg3 input,
    footer.bg3 button {
        word-break: break-word;
    }

    footer.bg3 .p-b-50 {
        padding-bottom: 22px !important;
    }

    footer.bg3 p.size-201 {
        margin-bottom: 0;
        line-height: 1.8;
    }

    footer.bg3 a {
        transition: all .3s ease;
    }

    footer.bg3 ul li a:hover {
        padding-inline-start: 4px;
    }

    .footer-social {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .footer-social.p-t-20 {
        padding-top: 14px !important;
    }

    .footer-social a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.08);
        transition: all .3s ease;
    }

    .footer-social a:hover {
        background: #717fe0;
        color: #fff;
        transform: translateY(-3px);
    }

    .newsletter-box {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.10);
        padding: 14px;
        border-radius: 12px;
        transition: all .3s ease;
    }

    .newsletter-box:hover {
        border-color: rgba(255,255,255,0.16);
        transform: translateY(-2px);
    }

    .newsletter-box .wrap-input1 {
        width: 100%;
    }

    .newsletter-box .input1 {
        width: 100%;
        min-height: 42px;
    }

    .newsletter-box button {
        width: 100%;
        min-height: 42px;
        border: none;
        cursor: pointer;
        transition: all .3s ease;
    }

    .newsletter-box button:hover {
        transform: translateY(-1px);
    }

    .footer-payments {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 66px;
        height: 38px;
        padding: 0 12px;
        border-radius: 10px;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.10);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        transition: all .3s ease;
    }

    .payment-badge:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.10);
    }

    .footer-bottom {
        margin-top: 6px;
        padding-top: 16px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    .footer-bottom-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .footer-copy {
        color: #b2b2b2;
        font-size: 13px;
        line-height: 1.7;
    }

    .footer-bottom-links {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    .footer-bottom-links a {
        color: #b2b2b2;
        font-size: 13px;
        position: relative;
    }

    .footer-bottom-links a:hover {
        color: #fff;
    }

    .btn-back-to-top {
        transition: all .3s ease;
    }

    .btn-back-to-top:hover {
        transform: translateY(-4px);
    }

    @media (max-width: 991px) {
        footer.bg3 {
            padding-top: 38px !important;
            padding-bottom: 16px !important;
        }

        footer.bg3 .p-b-50 {
            padding-bottom: 18px !important;
        }

        .footer-logo {
            margin-bottom: 14px;
        }

        .footer-logo img {
            max-width: 100px;
        }
    }

    @media (max-width: 767px) {
        footer.bg3 {
            text-align: center;
            padding-top: 34px !important;
            padding-bottom: 14px !important;
        }

        .footer-logo {
            margin-bottom: 12px;
        }

        .footer-social,
        .footer-payments,
        .footer-bottom-links {
            justify-content: center;
        }

        .footer-bottom-wrap {
            justify-content: center;
            text-align: center;
        }

        .newsletter-box {
            margin-top: 6px;
        }
    }

    @media (max-width: 575px) {
        footer.bg3 h4 {
            font-size: 16px;
            padding-bottom: 12px !important;
        }

        footer.bg3 .stext-107,
        footer.bg3 p,
        footer.bg3 .input1,
        footer.bg3 button,
        .footer-copy,
        .footer-bottom-links a {
            font-size: 13px !important;
        }

        .payment-badge {
            min-width: 60px;
            height: 36px;
            font-size: 11px;
        }
    }
</style>

<footer class="bg3 p-t-75 p-b-32">
    <div class="container">

        <div class="footer-logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('cozastore/images/icons/logo-01.png') }}" alt="LOGO">
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

                <div class="footer-social p-t-20">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                    <a href="#"><i class="fa fa-pinterest-p"></i></a>
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
                    <form>
                        <div class="wrap-input1 w-full p-b-4">
                            <input class="input1 bg-none plh1 stext-107 cl7"
                                   type="email"
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
                    <a href="#">{{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}</a>
                    <a href="#">{{ app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms & Conditions' }}</a>
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
