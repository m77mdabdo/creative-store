<style>

/* 🔥 HERO SLIDER FIX FOR MOBILE */
.section-slide {
    position: relative;
}

.item-slick1 {
    height: 90vh;
    min-height: 500px;
    display: flex !important;
    align-items: center;
}

.item-slick1 .container {
    width: 100%;
}

.item-slick1 .wrap-content-slide1 {
    max-width: 100%;
}

/* الصورة */
.item-slick1 {
    background-position: center center !important;
    background-size: cover !important;
}

/* النص */
.item-slick1 .layer-slick1 {
    text-align: center;
}

/* زرار */
.item-slick1 .layer-slick1 a {
    display: inline-block;
    margin-top: 15px;
}

/* 📱 موبايل */
@media (max-width: 768px) {

    .item-slick1 {
        height: 75vh;
        min-height: 400px;
        padding: 40px 15px;
    }

    .item-slick1 .layer-slick1 {
        text-align: center !important;
    }

    .item-slick1 h2 {
        font-size: 28px !important;
        line-height: 1.3 !important;
    }

    .item-slick1 span {
        font-size: 14px !important;
    }

    .item-slick1 a {
        font-size: 14px !important;
        padding: 10px 20px !important;
    }

}

/* 📱 موبايل صغير */
@media (max-width: 480px) {

    .item-slick1 {
        height: 70vh;
        min-height: 350px;
    }

    .item-slick1 h2 {
        font-size: 24px !important;
    }

}
</style>


<section class="section-slide">
    <div class="wrap-slick1">
        <div class="slick1">

            <div class="item-slick1" style="background-image: url('{{ asset('cozastore/images/slide-01.jpg') }}');">
                <div class="container h-full">
                    <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                        <div class="layer-slick1 animated visible-false" data-appear="fadeInDown" data-delay="0">
                            <span class="ltext-101 cl2 respon2">
                                {{ __('site.women_collection_2026') }}
                            </span>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="fadeInUp" data-delay="800">
                            <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">
                                {{ __('site.new_season') }}
                            </h2>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="zoomIn" data-delay="1600">
                            <a href="{{ route('products.index') }}" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                {{ __('site.shop_now') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item-slick1" style="background-image: url('{{ asset('cozastore/images/slide-02.jpg') }}');">
                <div class="container h-full">
                    <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                        <div class="layer-slick1 animated visible-false" data-appear="rollIn" data-delay="0">
                            <span class="ltext-101 cl2 respon2">
                                {{ __('site.men_new_season') }}
                            </span>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="lightSpeedIn" data-delay="800">
                            <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">
                                {{ __('site.jackets_coats') }}
                            </h2>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="slideInUp" data-delay="1600">
                            <a href="{{ route('products.index') }}" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                {{ __('site.shop_now') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item-slick1" style="background-image: url('{{ asset('cozastore/images/slide-03.jpg') }}');">
                <div class="container h-full">
                    <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                        <div class="layer-slick1 animated visible-false" data-appear="rotateInDownLeft" data-delay="0">
                            <span class="ltext-101 cl2 respon2">
                                {{ __('site.men_collection_2026') }}
                            </span>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="rotateInUpRight" data-delay="800">
                            <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">
                                {{ __('site.new_arrivals') }}
                            </h2>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="rotateIn" data-delay="1600">
                            <a href="{{ route('products.index') }}" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                {{ __('site.shop_now') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
