<style>
/* ============================================================
   HERO SLIDER — MOBILE-FIRST FIX
   ============================================================ */

.section-slide {
    position: relative;
    overflow: hidden;
}

/* ── Slide wrapper ── */
.item-slick1 {
    position: relative;
    height: 92vh;
    min-height: 520px;
    display: flex !important;
    align-items: flex-end;          /* النص في الأسفل دايماً */
    justify-content: center;
    background-position: center top !important;
    background-size: cover !important;
    background-repeat: no-repeat !important;
}

/* ── Overlay شفاف يضمن قراءة النص ── */
.item-slick1::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0)   35%,
        rgba(0,0,0,0.55) 100%
    );
    z-index: 1;
    pointer-events: none;
}

/* ── Content container ── */
.item-slick1 .container {
    position: relative;
    z-index: 2;
    width: 100%;
    padding-bottom: 50px;
}

.item-slick1 .wrap-content-slide1,
.item-slick1 .flex-col-l-m {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding-top: 0 !important;
}

/* ── Typography ── */
.item-slick1 .layer-slick1 {
    text-align: center;
    width: 100%;
}

/* Subtitle */
.item-slick1 .ltext-101 {
    font-size: 15px;
    letter-spacing: 2px;
    color: #fff !important;
    text-shadow: 0 1px 6px rgba(0,0,0,0.35);
    display: block;
    margin-bottom: 8px;
}

/* Title */
.item-slick1 h2 {
    font-size: 42px !important;
    line-height: 1.2 !important;
    color: #fff !important;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    margin: 0 0 28px !important;
    padding: 0 !important;
}

/* Button */
.item-slick1 .layer-slick1 a,
.item-slick1 a.flex-c-m {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    min-width: 180px;
    max-width: 260px;
    width: auto !important;
    padding: 14px 32px !important;
    border-radius: 999px !important;
    font-size: 15px !important;
    font-weight: 700;
    letter-spacing: 1px;
    background: #717fe0 !important;
    color: #fff !important;
    border: none !important;
    box-shadow: 0 8px 24px rgba(113,127,224,0.4);
    transition: all .3s ease;
    text-decoration: none;
}

.item-slick1 .layer-slick1 a:hover,
.item-slick1 a.flex-c-m:hover {
    background: #5f6ddb !important;
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(113,127,224,0.5);
}

/* ── Slick arrows on mobile ── */
.slick1 .slick-prev,
.slick1 .slick-next {
    z-index: 10;
    width: 38px;
    height: 38px;
    background: rgba(255,255,255,0.2) !important;
    border-radius: 50%;
    backdrop-filter: blur(4px);
}

.slick1 .slick-prev { left: 12px; }
.slick1 .slick-next { right: 12px; }

.slick1 .slick-dots {
    bottom: 16px;
}

.slick1 .slick-dots li button:before {
    color: #fff;
    font-size: 10px;
    opacity: 0.6;
}

.slick1 .slick-dots li.slick-active button:before {
    color: #fff;
    opacity: 1;
}

/* ============================================================
   📱 TABLET  ≤ 991px
   ============================================================ */
@media (max-width: 991px) {

    .item-slick1 {
        height: 80vh;
        min-height: 460px;
    }

    .item-slick1 h2 {
        font-size: 36px !important;
    }
}

/* ============================================================
   📱 MOBILE  ≤ 768px
   ============================================================ */
@media (max-width: 768px) {

    .item-slick1 {
        height: 75vh;
        min-height: 420px;
        /* نص في الأسفل — صورة في الأعلى */
        align-items: flex-end;
        background-position: center 15% !important;
    }

    .item-slick1 .container {
        padding-bottom: 36px;
    }

    .item-slick1 .ltext-101 {
        font-size: 13px;
        letter-spacing: 1.5px;
    }

    .item-slick1 h2 {
        font-size: 28px !important;
        margin-bottom: 20px !important;
    }

    .item-slick1 .layer-slick1 a,
    .item-slick1 a.flex-c-m {
        min-width: 160px;
        max-width: 220px;
        padding: 12px 26px !important;
        font-size: 14px !important;
    }
}

/* ============================================================
   📱 SMALL MOBILE  ≤ 480px
   ============================================================ */
@media (max-width: 480px) {

    .item-slick1 {
        height: 72vh;
        min-height: 380px;
        background-position: center 10% !important;
    }

    .item-slick1 h2 {
        font-size: 24px !important;
        margin-bottom: 16px !important;
    }

    .item-slick1 .ltext-101 {
        font-size: 12px;
    }

    .item-slick1 .layer-slick1 a,
    .item-slick1 a.flex-c-m {
        min-width: 140px;
        padding: 11px 22px !important;
        font-size: 13px !important;
    }
}
</style>


<section class="section-slide">
    <div class="wrap-slick1">
        <div class="slick1">

            {{-- ── Slide 1 ── --}}
            <div class="item-slick1" style="background-image: url('{{ asset('cozastore/images/slide-01.jpg') }}');">
                <div class="container">
                    <div class="flex-col-l-m respon5">

                        <div class="layer-slick1 animated visible-false" data-appear="fadeInDown" data-delay="0">
                            <span class="ltext-101 cl2 respon2">
                                {{ __('site.women_collection_2026') }}
                            </span>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="fadeInUp" data-delay="800">
                            <h2 class="ltext-201 cl2 respon1">
                                {{ __('site.new_season') }}
                            </h2>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="zoomIn" data-delay="1600">
                            <a href="{{ route('products.index') }}"
                               class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                {{ __('site.shop_now') }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Slide 2 ── --}}
            <div class="item-slick1" style="background-image: url('{{ asset('cozastore/images/slide-02.jpg') }}');">
                <div class="container">
                    <div class="flex-col-l-m respon5">

                        <div class="layer-slick1 animated visible-false" data-appear="rollIn" data-delay="0">
                            <span class="ltext-101 cl2 respon2">
                                {{ __('site.men_new_season') }}
                            </span>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="lightSpeedIn" data-delay="800">
                            <h2 class="ltext-201 cl2 respon1">
                                {{ __('site.jackets_coats') }}
                            </h2>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="slideInUp" data-delay="1600">
                            <a href="{{ route('products.index') }}"
                               class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                {{ __('site.shop_now') }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Slide 3 ── --}}
            <div class="item-slick1" style="background-image: url('{{ asset('cozastore/images/slide-03.jpg') }}');">
                <div class="container">
                    <div class="flex-col-l-m respon5">

                        <div class="layer-slick1 animated visible-false" data-appear="rotateInDownLeft" data-delay="0">
                            <span class="ltext-101 cl2 respon2">
                                {{ __('site.men_collection_2026') }}
                            </span>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="rotateInUpRight" data-delay="800">
                            <h2 class="ltext-201 cl2 respon1">
                                {{ __('site.new_arrivals') }}
                            </h2>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="rotateIn" data-delay="1600">
                            <a href="{{ route('products.index') }}"
                               class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                {{ __('site.shop_now') }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
