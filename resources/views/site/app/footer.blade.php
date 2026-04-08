<style>
    footer.bg3 {
        overflow: hidden;
        position: relative;
    }

    /* Logo */
    .footer-logo {
        text-align: center;
        margin-bottom: 40px;
    }

    .footer-logo img {
        max-width: 160px;
        width: 100%;
        height: auto;
        transition: transform .3s ease;
    }

    .footer-logo a:hover img {
        transform: translateY(-3px);
    }

    /* Lists */
    footer.bg3 ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    footer.bg3 ul li a,
    footer.bg3 p,
    footer.bg3 input,
    footer.bg3 button {
        word-break: break-word;
    }

    footer.bg3 h4 {
        line-height: 1.4;
        word-break: break-word;
    }

    /* Footer links hover */
    footer.bg3 a {
        transition: all .3s ease;
    }

    footer.bg3 ul li a:hover {
        padding-inline-start: 6px;
    }

    /* Social */
    .footer-social {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .footer-social a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.08);
        transition: all .3s ease;
    }

    .footer-social a:hover {
        background: #717fe0;
        color: #fff;
        transform: translateY(-4px) scale(1.05);
        box-shadow: 0 8px 20px rgba(113, 127, 224, 0.25);
    }

    /* Newsletter */
    .newsletter-box {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.10);
        padding: 20px;
        border-radius: 14px;
        transition: all .3s ease;
    }

    .newsletter-box:hover {
        border-color: rgba(255,255,255,0.18);
        transform: translateY(-3px);
    }

    .newsletter-box .wrap-input1 {
        width: 100%;
    }

    .newsletter-box .input1 {
        width: 100%;
        min-height: 45px;
    }

    .newsletter-box button {
        width: 100%;
        min-height: 46px;
        border: none;
        cursor: pointer;
        transition: all .3s ease;
    }

    .newsletter-box button:hover {
        transform: translateY(-2px);
    }

    /* Payment icons */
    .footer-payments {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 72px;
        height: 42px;
        padding: 0 14px;
        border-radius: 10px;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.10);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .3px;
        transition: all .3s ease;
    }

    .payment-badge:hover {
        transform: translateY(-3px);
        background: rgba(255,255,255,0.10);
        border-color: rgba(255,255,255,0.18);
    }

    /* Bottom bar */
    .footer-bottom {
        margin-top: 20px;
        padding-top: 22px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    .footer-bottom-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .footer-copy {
        color: #b2b2b2;
        font-size: 14px;
        line-height: 1.7;
    }

    .footer-bottom-links {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px;
    }

    .footer-bottom-links a {
        color: #b2b2b2;
        font-size: 14px;
        position: relative;
    }

    .footer-bottom-links a:hover {
        color: #fff;
    }

    /* Back to top */
    .btn-back-to-top {
        transition: all .3s ease;
    }

    .btn-back-to-top:hover {
        transform: translateY(-4px);
    }

    /* Tablet */
    @media (max-width: 991px) {
        footer.bg3 {
            padding-top: 55px !important;
            padding-bottom: 25px !important;
        }

        footer.bg3 .p-b-50 {
            padding-bottom: 35px !important;
        }

        footer.bg3 h4 {
            font-size: 18px;
            padding-bottom: 20px !important;
        }
    }

    /* Mobile */
    @media (max-width: 767px) {
        footer.bg3 {
            text-align: center;
            padding-top: 45px !important;
            padding-bottom: 20px !important;
        }

        .footer-logo {
            margin-bottom: 25px;
        }

        .footer-social,
        .footer-payments,
        .footer-bottom-links {
            justify-content: center;
        }

        .newsletter-box {
            margin-top: 10px;
        }

        .footer-bottom-wrap {
            justify-content: center;
            text-align: center;
        }
    }

    @media (max-width: 575px) {
        footer.bg3 {
            padding-top: 40px !important;
            padding-bottom: 18px !important;
        }

        footer.bg3 .p-b-50 {
            padding-bottom: 28px !important;
        }

        footer.bg3 h4 {
            font-size: 17px;
            padding-bottom: 16px !important;
        }

        footer.bg3 ul li {
            padding-bottom: 8px !important;
        }

        footer.bg3 .stext-107,
        footer.bg3 p,
        footer.bg3 .input1,
        footer.bg3 button,
        .footer-copy,
        .footer-bottom-links a {
            font-size: 14px !important;
        }

        .payment-badge {
            min-width: 66px;
            height: 40px;
            font-size: 12px;
        }

        .btn-back-to-top {
            right: 15px !important;
            bottom: 15px !important;
        }

        html[dir="rtl"] .btn-back-to-top {
            right: auto !important;
            left: 15px !important;
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
