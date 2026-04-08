<style>
    .sec-banner .block1 {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        height: 100%;
    }

    .sec-banner .wrap-pic-w {
        display: block;
        width: 100%;
    }

    .sec-banner .wrap-pic-w img {
        width: 100%;
        height: 320px;
        object-fit: cover;
        display: block;
    }

    .sec-banner .block1-txt {
        z-index: 2;
        text-decoration: none;
    }

    .sec-banner .block1-name,
    .sec-banner .block1-info,
    .sec-banner .block1-link {
        word-break: break-word;
    }

    @media (max-width: 1199px) {
        .sec-banner .wrap-pic-w img {
            height: 280px;
        }
    }

    @media (max-width: 991px) {
        .sec-banner .wrap-pic-w img {
            height: 240px;
        }

        .sec-banner .block1-txt {
            padding-left: 25px !important;
            padding-right: 25px !important;
            padding-top: 25px !important;
            padding-bottom: 25px !important;
        }

        .sec-banner .block1-name {
            font-size: 24px !important;
            line-height: 1.3 !important;
        }

        .sec-banner .block1-info {
            font-size: 14px !important;
            line-height: 1.4 !important;
        }
    }

    @media (max-width: 767px) {
        .sec-banner {
            padding-top: 50px !important;
            padding-bottom: 20px !important;
        }

        .sec-banner .row {
            margin-left: -10px;
            margin-right: -10px;
        }

        .sec-banner .col-md-6,
        .sec-banner .col-xl-4 {
            padding-left: 10px;
            padding-right: 10px;
        }

        .sec-banner .wrap-pic-w img {
            height: 220px;
        }

        .sec-banner .block1-txt {
            padding-left: 20px !important;
            padding-right: 20px !important;
            padding-top: 20px !important;
            padding-bottom: 20px !important;
        }

        .sec-banner .block1-name {
            font-size: 22px !important;
            padding-bottom: 6px !important;
        }

        .sec-banner .block1-info {
            font-size: 13px !important;
        }

        .sec-banner .block1-link {
            font-size: 13px !important;
        }
    }

    @media (max-width: 575px) {
        .sec-banner .wrap-pic-w img {
            height: 200px;
        }

        .sec-banner .block1-name {
            font-size: 20px !important;
        }

        .sec-banner .block1-info {
            font-size: 12px !important;
        }

        .sec-banner .block1-link {
            font-size: 12px !important;
        }

        .sec-banner .block1-txt {
            padding-left: 16px !important;
            padding-right: 16px !important;
            padding-top: 16px !important;
            padding-bottom: 16px !important;
        }
    }
</style>

<div class="sec-banner bg0 p-t-80 p-b-50">
    <div class="container">
        <div class="row">

            <div class="col-md-6 col-xl-4 p-b-30 m-lr-auto">
                <div class="block1 wrap-pic-w">
                    <img src="{{ asset('cozastore/images/banner-01.jpg') }}" alt="IMG-BANNER">

                    <a href="{{ route('products.index') }}" class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
                        <div class="block1-txt-child1 flex-col-l">
                            <span class="block1-name ltext-102 trans-04 p-b-8">
                                {{ __('site.women') }}
                            </span>

                            <span class="block1-info stext-102 trans-04">
                                {{ __('site.spring_2026') }}
                            </span>
                        </div>

                        <div class="block1-txt-child2 p-b-4 trans-05">
                            <div class="block1-link stext-101 cl0 trans-09">
                                {{ __('site.shop_now') }}
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-xl-4 p-b-30 m-lr-auto">
                <div class="block1 wrap-pic-w">
                    <img src="{{ asset('cozastore/images/banner-02.jpg') }}" alt="IMG-BANNER">

                    <a href="{{ route('products.index') }}" class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
                        <div class="block1-txt-child1 flex-col-l">
                            <span class="block1-name ltext-102 trans-04 p-b-8">
                                {{ __('site.men') }}
                            </span>

                            <span class="block1-info stext-102 trans-04">
                                {{ __('site.spring_2026') }}
                            </span>
                        </div>

                        <div class="block1-txt-child2 p-b-4 trans-05">
                            <div class="block1-link stext-101 cl0 trans-09">
                                {{ __('site.shop_now') }}
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-xl-4 p-b-30 m-lr-auto">
                <div class="block1 wrap-pic-w">
                    <img src="{{ asset('cozastore/images/banner-03.jpg') }}" alt="IMG-BANNER">

                    <a href="{{ route('products.index') }}" class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
                        <div class="block1-txt-child1 flex-col-l">
                            <span class="block1-name ltext-102 trans-04 p-b-8">
                                {{ __('site.accessories') }}
                            </span>

                            <span class="block1-info stext-102 trans-04">
                                {{ __('site.new_trend') }}
                            </span>
                        </div>

                        <div class="block1-txt-child2 p-b-4 trans-05">
                            <div class="block1-link stext-101 cl0 trans-09">
                                {{ __('site.shop_now') }}
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
