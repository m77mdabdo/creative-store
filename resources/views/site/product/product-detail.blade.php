@extends('site.app.layout')

@section('content')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<style>
    .product-page {
        padding-top: 38px;
        padding-bottom: 90px;
        background: #fff;
    }

    .product-breadcrumb {
        margin-bottom: 24px;
    }

    .product-breadcrumb .bread-crumb {
        padding-top: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .product-breadcrumb a,
    .product-breadcrumb span {
        font-size: 14px;
    }

    .product-main-row {
        align-items: flex-start;
    }

    .product-gallery-wrap {
        position: relative;
    }

    .product-gallery-shell {
        background: #fff;
        border: 1px solid #efefef;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(0,0,0,.05);
        padding: 18px;
        overflow: hidden;
    }

    .product-image-frame {
        width: 100%;
        aspect-ratio: 4 / 5;
        background: #f7f7f7;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
    }

    .product-image-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .4s ease;
    }

    .product-image-frame:hover img {
        transform: scale(1.04);
    }

    .item-slick3 {
        padding: 0;
    }

    .wrap-slick3-dots {
        margin-top: 14px;
    }

    .slick3-dots {
        display: flex !important;
        flex-wrap: wrap;
        gap: 10px;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .slick3-dots li {
        width: 72px;
        height: 88px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #ececec;
        cursor: pointer;
        transition: all .25s ease;
        background: #fff;
    }

    .slick3-dots li img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .slick3-dots li.slick-active {
        border-color: rgba(113,127,224,.35);
        box-shadow: 0 8px 18px rgba(113,127,224,.15);
    }

    .wrap-slick3-arrows .arrow-slick3 {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 0;
        background: #fff;
        box-shadow: 0 8px 24px rgba(0,0,0,.08);
        color: #222;
        transition: all .25s ease;
    }

    .wrap-slick3-arrows .arrow-slick3:hover {
        color: #717fe0;
    }

    .product-details-box {
        background: #fff;
        border: 1px solid #efefef;
        border-radius: 24px;
        padding: 28px 26px;
        box-shadow: 0 12px 30px rgba(0,0,0,.05);
    }

    .product-title {
        font-size: 34px;
        line-height: 1.25;
        font-weight: 700;
        color: #222;
        margin-bottom: 10px;
    }

    .product-price {
        display: inline-block;
        font-size: 28px;
        line-height: 1.2;
        font-weight: 800;
        color: #222;
        margin-bottom: 14px;
    }

    .product-meta {
        margin-top: 8px;
        margin-bottom: 10px;
    }

    .product-meta .stext-102 {
        font-size: 15px;
        color: #666;
    }

    .product-rating-wrap {
        padding-top: 6px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 18px;
    }

    .product-rating-stars {
        font-size: 22px;
        line-height: 1;
    }

    .product-rating-text {
        font-size: 15px;
        color: #666;
        margin-top: 6px;
        display: inline-block;
    }

    .product-offers-box {
        padding-top: 4px;
        padding-bottom: 8px;
    }

    .offer-card {
        border: 1px solid #ececec;
        background: #fafafa;
        border-radius: 16px;
        padding: 16px 18px;
        margin-bottom: 12px;
    }

    .offer-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #222;
    }

    .offer-card-title span {
        color: #e63946;
    }

    .offer-card-desc {
        font-size: 14px;
        color: #666;
        margin-top: 6px;
        margin-bottom: 0;
    }

    .offer-copy-btn {
        min-height: 42px;
        padding: 0 16px;
        border-radius: 999px;
    }

    .product-form-box {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid #f0f0f0;
    }

    .option-row {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 14px;
    }

    .option-label {
        width: 110px;
        min-width: 110px;
        font-size: 15px;
        font-weight: 700;
        color: #222;
        padding-top: 12px;
    }

    .option-field {
        flex: 1;
        min-width: 0;
    }

    .product-details-box .rs1-select2,
    .product-details-box .bor8.bg0 {
        width: 100%;
        border-radius: 14px !important;
        overflow: hidden;
    }

    .product-details-box .select2-container .select2-selection--single {
        height: 48px !important;
        border: 1px solid #e8e8e8 !important;
        border-radius: 14px !important;
        background: #fff !important;
    }

    .product-details-box .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 48px !important;
        padding-left: 16px !important;
        padding-right: 16px !important;
        color: #333 !important;
    }

    .product-details-box .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px !important;
        right: 10px !important;
    }

    .color-variant-btn {
        border-radius: 12px !important;
        padding: 8px 16px !important;
        min-height: 42px;
        border: 1px solid #dcdcdc !important;
        background: #fff !important;
        color: #333 !important;
        transition: all .25s ease !important;
        box-shadow: none !important;
    }

    .color-variant-btn:hover {
        border-color: rgba(113,127,224,.35) !important;
        color: #717fe0 !important;
        background: rgba(113,127,224,.05) !important;
    }

    .color-variant-btn.active {
        background: #222 !important;
        color: #fff !important;
        border-color: #222 !important;
    }

    .product-qty-row {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 6px;
    }

    .product-details-box .wrap-num-product {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #e8e8e8;
        background: #fff;
    }

    .product-details-box .btn-num-product-down,
    .product-details-box .btn-num-product-up {
        width: 48px;
        height: 48px;
        background: #fafafa;
        transition: all .25s ease;
    }

    .product-details-box .btn-num-product-down:hover,
    .product-details-box .btn-num-product-up:hover {
        background: #f1f1f1;
    }

    .product-details-box .num-product {
        width: 58px;
        height: 48px;
        border-left: 1px solid #e8e8e8;
        border-right: 1px solid #e8e8e8;
    }

    #addToCartBtn {
        min-width: 190px;
        min-height: 50px;
        border-radius: 999px;
        background: #717fe0 !important;
        border: 0 !important;
        font-weight: 700;
        box-shadow: 0 12px 25px rgba(113,127,224,.28);
    }

    #addToCartBtn:hover {
        background: #5f6ddb !important;
    }

    .coupon-preview-box {
        margin-top: 16px;
        border: 1px solid #ececec;
        border-radius: 18px;
        background: #fafafa;
        padding: 18px 18px 14px;
    }

    .coupon-preview-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .coupon-preview-box input[type="text"] {
        max-width: 250px;
        border-radius: 999px;
        min-height: 44px;
        border: 1px solid #e2e2e2;
        background: #fff;
    }

    #applyCouponBtn {
        min-height: 44px;
        border-radius: 999px;
    }

    .product-actions-extra {
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .product-share-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .product-share-group a,
    .wishlist-circle-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid #ececec;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #444;
        background: #fff;
        transition: all .25s ease;
    }

    .product-share-group a:hover,
    .wishlist-circle-btn:hover {
        color: #717fe0;
        border-color: rgba(113,127,224,.25);
        background: rgba(113,127,224,.06);
    }

    .wishlist-circle-btn {
        padding: 0;
        cursor: pointer;
    }

    .product-tabs-box {
        margin-top: 48px;
        border: 1px solid #efefef;
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 12px 30px rgba(0,0,0,.04);
        overflow: hidden;
    }

    .product-tabs-box .nav-tabs {
        border-bottom: 1px solid #f0f0f0;
        padding: 18px 24px 0;
        gap: 10px;
    }

    .product-tabs-box .nav-tabs .nav-link {
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: #666;
        font-weight: 600;
        padding: 10px 18px;
        transition: all .25s ease;
    }

    .product-tabs-box .nav-tabs .nav-link.active {
        background: rgba(113,127,224,.1);
        color: #717fe0;
    }

    .product-tabs-box .tab-content {
        padding: 28px 26px 26px;
    }

    .product-info-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .product-info-list li {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        padding: 10px 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .product-info-list li:last-child {
        border-bottom: 0;
    }

    .product-info-label {
        font-size: 15px;
        font-weight: 700;
        color: #222;
    }

    .product-info-value {
        font-size: 15px;
        color: #666;
    }

    .review-card {
        padding-bottom: 22px;
        margin-bottom: 22px;
        border-bottom: 1px solid #f2f2f2;
    }

    .review-card:last-child {
        border-bottom: 0;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .review-author {
        font-size: 17px;
        font-weight: 700;
        color: #222;
    }

    .review-stars {
        font-size: 18px;
        line-height: 1;
    }

    .review-title {
        font-size: 15px;
        font-weight: 700;
        color: #222;
        margin: 8px 0 6px;
    }

    .review-text {
        font-size: 15px;
        color: #666;
        line-height: 1.85;
        margin: 0;
    }

    .review-form-box {
        margin-top: 24px;
        border: 1px solid #ececec;
        border-radius: 18px;
        background: #fafafa;
        padding: 20px;
    }

    .review-form-box input,
    .review-form-box textarea,
    .review-form-box select {
        border-radius: 14px;
        border: 1px solid #e2e2e2;
        background: #fff;
    }

    .product-footer-meta {
        margin-top: 38px;
        border-radius: 18px;
        background: #f8f8f8;
        min-height: 66px;
    }

    .product-footer-meta .stext-107 {
        font-size: 14px;
    }

    .related-products-section {
        padding-top: 30px;
        padding-bottom: 90px;
    }

    .related-products-section .block2 {
        border-radius: 18px;
        border: 1px solid #efefef;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 10px 26px rgba(0,0,0,.04);
        transition: all .25s ease;
    }

    .related-products-section .block2:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 35px rgba(0,0,0,.08);
    }

    .related-products-section .block2-pic {
        background: #f7f7f7;
        aspect-ratio: 4 / 5;
        overflow: hidden;
    }

    .related-products-section .block2-pic img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    html[dir="rtl"] .product-page,
    html[dir="rtl"] .product-details-box,
    html[dir="rtl"] .product-tabs-box,
    html[dir="rtl"] .product-footer-meta,
    html[dir="rtl"] .review-form-box,
    html[dir="rtl"] .offer-card,
    html[dir="rtl"] .coupon-preview-box {
        text-align: right;
    }

    html[dir="rtl"] .product-breadcrumb .bread-crumb {
        justify-content: flex-start;
    }

    html[dir="rtl"] .option-row {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .product-qty-row {
        flex-direction: row-reverse;
        justify-content: flex-start;
    }

    html[dir="rtl"] .coupon-preview-row,
    html[dir="rtl"] .product-actions-extra,
    html[dir="rtl"] .product-share-group,
    html[dir="rtl"] .review-form-box .flex-w,
    html[dir="rtl"] .offer-card .flex-w {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .product-info-list li {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .product-details-box .select2-container--default .select2-selection--single .select2-selection__rendered {
        text-align: right;
    }

    html[dir="rtl"] .product-details-box .select2-container--default .select2-selection--single .select2-selection__arrow {
        left: 10px !important;
        right: auto !important;
    }

    html[dir="rtl"] .product-details-box .wrap-num-product {
        margin-right: 0 !important;
        margin-left: 16px !important;
    }

    html[dir="rtl"] .product-rating-wrap,
    html[dir="rtl"] .product-meta,
    html[dir="rtl"] .product-title,
    html[dir="rtl"] .product-price {
        text-align: right;
    }

    @media (max-width: 1199px) {
        .product-title {
            font-size: 30px;
        }

        .product-price {
            font-size: 24px;
        }
    }

    @media (max-width: 991px) {
        .product-page {
            padding-top: 20px;
        }

        .product-gallery-shell,
        .product-details-box,
        .product-tabs-box {
            border-radius: 20px;
        }

        .product-gallery-wrap {
            margin-bottom: 18px;
        }

        .product-title {
            font-size: 28px;
        }
    }

    @media (max-width: 767px) {
        .product-title {
            font-size: 24px;
        }

        .product-price {
            font-size: 22px;
        }

        .option-row {
            flex-direction: column !important;
            gap: 8px;
        }

        .option-label {
            width: 100%;
            min-width: 100%;
            padding-top: 0;
        }

        .product-qty-row {
            align-items: stretch;
        }

        #addToCartBtn {
            width: 100%;
        }

        .coupon-preview-box input[type="text"] {
            max-width: 100%;
            width: 100%;
        }

        .product-actions-extra {
            align-items: flex-start;
        }
    }
</style>

<section class="product-page">
    <div class="container">

        <div class="product-breadcrumb">
            <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
                <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">
                    {{ __('site.home') }}
                    <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
                </a>

                @if($categorySlug)
                    <a href="{{ url('/products?category=' . $categorySlug) }}" class="stext-109 cl8 hov-cl1 trans-04">
                        {{ $categoryName }}
                        <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
                    </a>
                @endif

                <span class="stext-109 cl4">
                    {{ $productName }}
                </span>
            </div>
        </div>

        <div class="row product-main-row">

            <div class="col-md-6 col-lg-7 p-b-30">
                <div class="product-gallery-wrap">
                    <div class="product-gallery-shell">
                        <div class="wrap-slick3 flex-sb flex-w">
                            <div class="wrap-slick3-dots"></div>
                            <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                            <div class="slick3 gallery-lb" id="productGallery">
                                @foreach($generalGallery as $img)
                                    <div class="item-slick3" data-thumb="{{ $img['thumb'] }}">
                                        <div class="product-image-frame">
                                            <img src="{{ $img['url'] }}" alt="{{ $img['alt'] }}">

                                            <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                               href="{{ $img['url'] }}">
                                                <i class="fa fa-expand"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-5 p-b-30">
                <div class="product-details-box">

                    <h1 class="product-title js-name-detail">
                        {{ $productName }}
                    </h1>

                    <span class="product-price" id="productPrice">
                        {{ number_format((float) $productPrice, 2) }} {{ $productCurrency }}
                    </span>

                    <div class="product-meta">
                        <div class="stext-102 cl3">
                            {{ __('site.sku') }}:
                            <span id="dynamicSku">{{ $productSku }}</span>
                        </div>

                        <div class="stext-102 cl3 p-t-5">
                            {{ __('site.stock') }}:
                            <span id="dynamicStock">{{ $product->variants?->first()?->stock ?? __('site.n_a') }}</span>
                        </div>

                        <p class="stext-102 p-t-5" id="stockMessage" style="display:none;color:#e3342f;">
                            {{ __('site.out_of_stock_variant') }}
                        </p>
                    </div>

                    <div class="product-rating-wrap">
                        @if($reviewsCount > 0)
                            <div class="product-rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($averageRating))
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>

                            <span class="product-rating-text">
                                {{ number_format((float) $averageRating, 1) }}/5
                                ({{ $reviewsCount }} {{ __('site.reviews') }})
                            </span>
                        @else
                            <span class="product-rating-text">
                                {{ __('site.no_reviews') }}
                            </span>
                        @endif
                    </div>

                    @if(count($productCoupons))
                        <div class="product-offers-box">
                            <h5 class="mtext-104 cl2 p-b-10">
                                {{ __('site.available_offers') }}
                            </h5>

                            @foreach($productCoupons as $coupon)
                                <div class="offer-card">
                                    <div class="flex-w flex-sb-m">
                                        <div>
                                            <div class="offer-card-title">
                                                {{ __('site.coupon') }}:
                                                <span>{{ $coupon['code'] }}</span>
                                            </div>

                                            <p class="offer-card-desc">
                                                {{ $coupon['label'] }}
                                            </p>
                                        </div>

                                        <button type="button"
                                                onclick="copyCouponCode('{{ $coupon['code'] }}')"
                                                class="offer-copy-btn flex-c-m stext-101 cl2 bg8 bor13 hov-btn3 p-lr-15 trans-04">
                                            {{ __('site.copy_code') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="product-form-box">
                        <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                            @csrf

                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="variantInput" value="">

                            @if(count($sizes))
                                <div class="option-row">
                                    <div class="option-label">
                                        {{ __('site.modal_size') }}
                                    </div>

                                    <div class="option-field">
                                        <div class="rs1-select2 bor8 bg0">
                                            <select class="js-select2" name="size" id="sizeSelect">
                                                <option value="">{{ __('site.choose_option') }}</option>
                                                @foreach($sizes as $size)
                                                    <option value="{{ $size['id'] }}">{{ $size['value'] }}</option>
                                                @endforeach
                                            </select>
                                            <div class="dropDownSelect2"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(count($colors))
                                <div class="option-row">
                                    <div class="option-label">
                                        {{ __('site.modal_color') }}
                                    </div>

                                    <div class="option-field">
                                        @if(isset($colorButtons) && $colorButtons->count())
                                            <div class="d-flex flex-wrap m-b-10" id="colorVariantButtons">
                                                @foreach($colorButtons as $colorVariant)
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-dark mr-2 mb-2 color-variant-btn"
                                                        data-variant-id="{{ $colorVariant['variant_id'] }}"
                                                        data-color-id="{{ $colorVariant['color_id'] ?? '' }}"
                                                        data-color-name="{{ $colorVariant['color_name'] }}"
                                                        data-color-slug="{{ $colorVariant['color_slug'] }}"
                                                        data-image="{{ $colorVariant['image'] }}"
                                                        data-gallery='@json($colorVariant['gallery'])'
                                                        data-price="{{ $colorVariant['price'] }}"
                                                        data-stock="{{ $colorVariant['stock'] }}"
                                                        data-sku="{{ $colorVariant['sku'] }}"
                                                    >
                                                        {{ $colorVariant['color_name'] }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="rs1-select2 bor8 bg0">
                                            <select class="js-select2" name="color" id="colorSelect">
                                                <option value="">{{ __('site.choose_option') }}</option>
                                                @foreach($colors as $color)
                                                    <option value="{{ $color['id'] }}">{{ $color['value'] }}</option>
                                                @endforeach
                                            </select>
                                            <div class="dropDownSelect2"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="option-row">
                                <div class="option-label"></div>

                                <div class="option-field">
                                    <div class="product-qty-row">
                                        <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                            <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-minus"></i>
                                            </div>

                                            <input class="mtext-104 cl3 txt-center num-product"
                                                   type="number"
                                                   name="quantity"
                                                   value="1"
                                                   min="1"
                                                   id="qtyInput">

                                            <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-plus"></i>
                                            </div>
                                        </div>

                                        <button type="submit"
                                                class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04"
                                                id="addToCartBtn">
                                            {{ __('site.add_to_cart') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="coupon-preview-box">
                                <div class="coupon-preview-row">
                                    <input type="text"
                                           name="coupon_code"
                                           id="couponCodeInput"
                                           value="{{ old('coupon_code') }}"
                                           placeholder="{{ __('site.enter_coupon') }}"
                                           class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5">

                                    <button type="button"
                                            id="applyCouponBtn"
                                            class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
                                        {{ __('site.apply_coupon') }}
                                    </button>
                                </div>

                                <div class="p-t-10">
                                    <div class="stext-115 cl6">
                                        {{ __('site.original_price') }}:
                                        <span id="priceOriginal">
                                            {{ number_format((float) $productPrice, 2) }} {{ $productCurrency }}
                                        </span>
                                    </div>

                                    <div id="discountRow" class="stext-115"
                                         style="display:none;color:#e63946;margin-top:6px;">
                                        {{ __('site.discounted_price') }}:
                                        <span id="priceDiscounted"></span>
                                    </div>

                                    <div id="couponMsg" class="stext-102" style="display:none;margin-top:6px;"></div>

                                    @if(session('cart_coupon'))
                                        <div class="p-t-8">
                                            <span class="stext-102" style="color:#38a169;font-weight:600;">
                                                {{ __('site.active_coupon') }}: {{ session('cart_coupon.code') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <p class="stext-102 cl3 p-t-10" id="variantError" style="display:none;color:#e3342f;">
                                {{ __('site.select_required_options') }}
                            </p>
                        </form>
                    </div>

                    <div class="product-actions-extra">
                        <div class="product-share-group">
                            @auth
                                <form action="{{ route('wishlist.toggle') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="variant_id" value="">

                                    <button type="submit" class="wishlist-circle-btn">
                                        @if($wishlistExists)
                                            <img src="{{ asset('cozastore/images/icons/icon-heart-02.png') }}" alt="wishlist">
                                        @else
                                            <img src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" alt="wishlist">
                                        @endif
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="wishlist-circle-btn">
                                    <img src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" alt="wishlist">
                                </a>
                            @endauth

                            <a href="#" class="tooltip100" data-tooltip="{{ __('site.facebook') }}">
                                <i class="fa fa-facebook"></i>
                            </a>
                            <a href="#" class="tooltip100" data-tooltip="{{ __('site.twitter') }}">
                                <i class="fa fa-twitter"></i>
                            </a>
                            <a href="#" class="tooltip100" data-tooltip="{{ __('site.google_plus') }}">
                                <i class="fa fa-google-plus"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="product-tabs-box">
            <div class="tab01">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item p-b-10">
                        <a class="nav-link active" data-toggle="tab" href="#tab_description" role="tab">
                            {{ __('site.description') }}
                        </a>
                    </li>
                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#tab_information" role="tab">
                            {{ __('site.additional_information') }}
                        </a>
                    </li>
                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#tab_reviews" role="tab">
                            {{ __('site.reviews') }}
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab_description" role="tabpanel">
                        <div class="how-pos2 p-lr-15-md">
                            <p class="stext-102 cl6">
                                {{ $productDescription ?: __('site.no_description_yet') }}
                            </p>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_information" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-7 m-lr-auto">
                                <ul class="product-info-list">
                                    <li>
                                        <span class="product-info-label">{{ __('site.category') }}</span>
                                        <span class="product-info-value">{{ $categoryName ?: '-' }}</span>
                                    </li>
                                    <li>
                                        <span class="product-info-label">{{ __('site.currency_label') }}</span>
                                        <span class="product-info-value">{{ $productCurrency ?: '-' }}</span>
                                    </li>
                                    <li>
                                        <span class="product-info-label">{{ __('site.sku') }}</span>
                                        <span class="product-info-value" id="skuInfo">{{ $productSku }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_reviews" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-7 m-lr-auto">
                                <div class="p-b-10">

                                    <h5 class="mtext-104 p-b-20">
                                        {{ $reviewsCount }} {{ __('site.reviews') }}
                                    </h5>

                                    @forelse($reviews as $review)
                                        <div class="review-card">
                                            <div class="flex-w flex-sb-m p-b-8">
                                                <span class="review-author">
                                                    {{ $review['user_name'] }}
                                                </span>

                                                <span class="review-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review['rating'])
                                                            ⭐
                                                        @else
                                                            ☆
                                                        @endif
                                                    @endfor
                                                </span>
                                            </div>

                                            @if($review['title'])
                                                <div class="review-title">
                                                    {{ $review['title'] }}
                                                </div>
                                            @endif

                                            <p class="review-text">
                                                {{ $review['comment'] }}
                                            </p>
                                        </div>
                                    @empty
                                        <p class="stext-102 cl6">
                                            {{ __('site.no_reviews') }}
                                        </p>
                                    @endforelse

                                </div>

                                @auth
                                    <div class="review-form-box">
                                        <h4 class="mtext-105 cl2 p-b-20">
                                            {{ __('site.add_review') }}
                                        </h4>

                                        @if(session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        @if(session('error'))
                                            <div class="alert alert-danger">
                                                {{ session('error') }}
                                            </div>
                                        @endif

                                        @if($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="m-b-0 p-l-20">
                                                    @foreach($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <form action="{{ route('reviews.store') }}" method="POST">
                                            @csrf

                                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                                            <div class="p-b-15">
                                                <label class="stext-102 cl3">
                                                    {{ __('site.rating') }}
                                                </label>

                                                <select name="rating" class="bor8 p-lr-10 p-tb-5">
                                                    <option value="5">⭐⭐⭐⭐⭐</option>
                                                    <option value="4">⭐⭐⭐⭐</option>
                                                    <option value="3">⭐⭐⭐</option>
                                                    <option value="2">⭐⭐</option>
                                                    <option value="1">⭐</option>
                                                </select>
                                            </div>

                                            <div class="p-b-15">
                                                <input type="text"
                                                       name="title"
                                                       value="{{ old('title') }}"
                                                       placeholder="{{ __('site.review_title') }}"
                                                       class="stext-111 cl2 plh3 size-116 bor8 p-lr-20">
                                            </div>

                                            <div class="p-b-15">
                                                <textarea name="comment"
                                                          rows="4"
                                                          placeholder="{{ __('site.write_review') }}"
                                                          class="stext-111 cl2 plh3 size-116 bor8 p-lr-20 p-tb-10">{{ old('comment') }}</textarea>
                                            </div>

                                            <button type="submit"
                                                    class="flex-c-m stext-101 cl0 size-121 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                                {{ __('site.submit_review') }}
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="p-t-20">
                                        <a href="{{ route('login') }}" class="stext-102 cl3">
                                            {{ __('site.login_to_review') }}
                                        </a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="product-footer-meta bg6 flex-c-m flex-w size-302 m-t-40 p-tb-15">
            <span class="stext-107 cl6 p-lr-25">
                {{ __('site.sku') }}:
                <span id="footerSku">{{ $productSku }}</span>
            </span>
            <span class="stext-107 cl6 p-lr-25">
                {{ __('site.category') }}:
                {{ $categoryName ?: __('site.n_a') }}
            </span>
        </div>

    </div>
</section>

<section class="related-products-section sec-relate-product bg0">
    <div class="container">
        <div class="p-b-35">
            <h3 class="ltext-106 cl5 txt-center">
                {{ __('site.related_products') }}
            </h3>
        </div>

        <div class="wrap-slick2">
            <div class="slick2">
                @forelse($related as $rp)
                    <div class="item-slick2 p-l-15 p-r-15 p-t-15 p-b-15">
                        <div class="block2">
                            <div class="block2-pic hov-img0">
                                <img src="{{ $rp['image'] }}" alt="{{ $rp['name'] }}">

                                <a href="{{ route('products.show', $rp['slug']) }}"
                                   class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                                    {{ __('site.view') }}
                                </a>
                            </div>

                            <div class="block2-txt flex-w flex-t p-t-14 p-lr-15 p-b-16">
                                <div class="block2-txt-child1 flex-col-l ">
                                    <a href="{{ route('products.show', $rp['slug']) }}"
                                       class="stext-104 cl4 hov-cl1 trans-04 p-b-6">
                                        {{ $rp['name'] }}
                                    </a>

                                    <span class="stext-105 cl3">
                                        {{ $rp['price'] }} {{ $rp['currency'] }}
                                    </span>
                                </div>

                                <div class="block2-txt-child2 flex-r p-t-3">
                                    <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
                                        <img class="icon-heart1 dis-block trans-04"
                                             src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" alt="ICON">
                                        <img class="icon-heart2 dis-block trans-04 ab-t-l"
                                             src="{{ asset('cozastore/images/icons/icon-heart-02.png') }}" alt="ICON">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-l-15 p-r-15">
                        <p class="stext-102 cl6">{{ __('site.no_related_products') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<script>
    function copyCouponCode(code) {
        const input = document.getElementById('couponCodeInput');

        if (input) {
            input.value = code;
            input.dispatchEvent(new Event('input'));
        }

        if (navigator.clipboard) {
            navigator.clipboard.writeText(code);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const couponInput = document.getElementById('couponCodeInput');
        const applyBtn = document.getElementById('applyCouponBtn');
        const priceDiscounted = document.getElementById('priceDiscounted');
        const discountRow = document.getElementById('discountRow');
        const couponMsg = document.getElementById('couponMsg');

        const sizeSelect = document.getElementById('sizeSelect');
        const colorSelect = document.getElementById('colorSelect');
        const variantInput = document.getElementById('variantInput');
        const variantError = document.getElementById('variantError');
        const productPrice = document.getElementById('productPrice');
        const dynamicSku = document.getElementById('dynamicSku');
        const dynamicStock = document.getElementById('dynamicStock');
        const skuInfo = document.getElementById('skuInfo');
        const footerSku = document.getElementById('footerSku');
        const priceOriginal = document.getElementById('priceOriginal');

        const colorButtons = document.querySelectorAll('.color-variant-btn');

        const productId = {{ (int) $product->id }};
        const currency = @json($productCurrency);
        const basePrice = {{ (float) $productPrice }};
        const baseSku = @json($productSku);

        const variantMap = @json($variantMap);
        const colorDefaultVariantMap = @json($colorDefaultVariantMap ?? []);
        const colorGalleryMap = @json($colorGalleryMap ?? []);
        const defaultGallery = @json($generalGallery);

        const i18n = {
            copied: @json(__('site.copy_coupon_success')),
            invalidCoupon: @json(__('site.invalid_coupon')),
            checkFailed: @json(__('site.coupon_check_failed')),
            selectRequired: @json(__('site.select_required_options')),
            combinationUnavailable: @json(__('site.selected_combination_unavailable')),
            couponApplied: @json(__('site.apply_coupon')),
            outOfStock: @json(__('site.out_of_stock_variant')),
            na: @json(__('site.n_a')),
        };

        function selectedSize() {
            return sizeSelect ? String(sizeSelect.value || '') : '';
        }

        function selectedColor() {
            return colorSelect ? String(colorSelect.value || '') : '';
        }

        function hasSizes() {
            return !!sizeSelect;
        }

        function hasColors() {
            return !!colorSelect;
        }

        function uniqueImages(images) {
            const seen = new Set();

            return (images || []).filter(img => {
                const key = String(img.path || img.url || `${img.id || ''}`);
                if (!key) return false;
                if (seen.has(key)) return false;
                seen.add(key);
                return true;
            });
        }

        function destroySlickIfExists() {
            if (!(window.jQuery && $('.wrap-slick3').length)) {
                return;
            }

            const $slider = $('.wrap-slick3 .slick3');

            if ($slider.hasClass('slick-initialized')) {
                $slider.slick('unslick');
            }

            $('.wrap-slick3-dots').html('');
            $('.wrap-slick3-arrows').html('');
        }

        function initProductGallery(imageCount) {
            if (!(window.jQuery && $('.wrap-slick3').length)) {
                return;
            }

            destroySlickIfExists();

            $('.wrap-slick3').each(function () {
                $(this).find('.slick3').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    fade: true,
                    infinite: false,
                    autoplay: false,
                    autoplaySpeed: 6000,
                    arrows: imageCount > 1,
                    appendArrows: $(this).find('.wrap-slick3-arrows'),
                    prevArrow: '<button class="arrow-slick3 prev-slick3"><i class="fa fa-angle-left" aria-hidden="true"></i></button>',
                    nextArrow: '<button class="arrow-slick3 next-slick3"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
                    dots: imageCount > 1,
                    appendDots: $(this).find('.wrap-slick3-dots'),
                    dotsClass: 'slick3-dots',
                    customPaging: function (slick, index) {
                        const portrait = $(slick.$slides[index]).data('thumb');
                        return '<img src="' + portrait + '"/><div class="slick3-dot-overlay"></div>';
                    }
                });
            });
        }

        function setGallery(images) {
            const gallery = document.getElementById('productGallery');
            if (!gallery) return;

            let finalImages = uniqueImages((images && images.length) ? images : defaultGallery);

            if (!finalImages.length) {
                finalImages = uniqueImages(defaultGallery);
            }

            gallery.innerHTML = finalImages.map(img => {
                const thumb = img.thumb || img.url;
                const url = img.url || img;
                const alt = img.alt || @json($productName);

                return `
                    <div class="item-slick3" data-thumb="${thumb}">
                        <div class="product-image-frame">
                            <img src="${url}" alt="${alt}">
                            <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="${url}">
                                <i class="fa fa-expand"></i>
                            </a>
                        </div>
                    </div>
                `;
            }).join('');

            initProductGallery(finalImages.length);
        }

        function setActiveColorButtonByColorId(colorId) {
            colorButtons.forEach(btn => {
                const btnColorId = String(btn.dataset.colorId || '');
                btn.classList.toggle('active', colorId && btnColorId === String(colorId));
            });
        }

        function selectColorButton(button) {
            if (!button) return;

            colorButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const colorId = button.dataset.colorId || '';

            if (colorSelect) {
                colorSelect.value = colorId;

                if (window.jQuery && $(colorSelect).hasClass('select2-hidden-accessible')) {
                    $(colorSelect).trigger('change.select2');
                }
            }

            updateVariantUI(true, button);
        }

        function getBestVariant() {
            const size = selectedSize();
            const color = selectedColor();

            if (size && color && variantMap[`${size}-${color}`]) {
                return variantMap[`${size}-${color}`];
            }

            if (color && colorDefaultVariantMap[color]) {
                return colorDefaultVariantMap[color];
            }

            if (size && variantMap[size]) {
                return variantMap[size];
            }

            return null;
        }

        function getBestGallery() {
            const size = selectedSize();
            const color = selectedColor();

            if (size && color && variantMap[`${size}-${color}`]?.images?.length) {
                return variantMap[`${size}-${color}`].images;
            }

            if (color && colorGalleryMap[color]?.length) {
                return colorGalleryMap[color];
            }

            if (size && variantMap[size]?.images?.length) {
                return variantMap[size].images;
            }

            if (color && colorDefaultVariantMap[color]?.images?.length) {
                return colorDefaultVariantMap[color].images;
            }

            return defaultGallery;
        }

        function resetBaseUI() {
            if (productPrice) {
                productPrice.textContent = `${Number(basePrice).toFixed(2)} ${currency}`;
            }

            if (priceOriginal) {
                priceOriginal.textContent = `${Number(basePrice).toFixed(2)} ${currency}`;
            }

            if (dynamicSku) dynamicSku.textContent = baseSku || i18n.na;
            if (skuInfo) skuInfo.textContent = baseSku || i18n.na;
            if (footerSku) footerSku.textContent = baseSku || i18n.na;
            if (dynamicStock) dynamicStock.textContent = i18n.na;

            if (variantInput) {
                variantInput.value = '';
            }
        }

        function updateVariantUI(fromColorButton = false, clickedButton = null) {
            const size = selectedSize();
            const color = selectedColor();

            const bestVariant = getBestVariant();
            const bestGallery = getBestGallery();

            if (!fromColorButton) {
                setActiveColorButtonByColorId(color);
            }

            setGallery(bestGallery);

            if (size && color) {
                const exactKey = `${size}-${color}`;

                if (variantMap[exactKey]) {
                    const exactVariant = variantMap[exactKey];

                    if (variantInput) variantInput.value = exactVariant.id || '';
                    if (productPrice) productPrice.textContent = `${Number(exactVariant.price).toFixed(2)} ${currency}`;
                    if (priceOriginal) priceOriginal.textContent = `${Number(exactVariant.price).toFixed(2)} ${currency}`;
                    if (dynamicSku) dynamicSku.textContent = exactVariant.sku || i18n.na;
                    if (skuInfo) skuInfo.textContent = exactVariant.sku || i18n.na;
                    if (footerSku) footerSku.textContent = exactVariant.sku || i18n.na;
                    if (dynamicStock) dynamicStock.textContent = exactVariant.stock ?? i18n.na;

                    if (variantError) {
                        variantError.style.display = 'none';
                    }

                    return;
                }

                resetBaseUI();

                if (variantError) {
                    variantError.textContent = i18n.combinationUnavailable;
                    variantError.style.display = 'block';
                }

                return;
            }

            if (fromColorButton && clickedButton) {
                const clickedColorId = String(clickedButton.dataset.colorId || '');
                const galleryFromColorMap = colorGalleryMap[clickedColorId] || [];
                const galleryFromButton = JSON.parse(clickedButton.dataset.gallery || '[]');
                const imageFromButton = clickedButton.dataset.image || '';
                const priceFromButton = clickedButton.dataset.price || '';
                const stockFromButton = clickedButton.dataset.stock || '';
                const skuFromButton = clickedButton.dataset.sku || '';
                const variantIdFromButton = clickedButton.dataset.variantId || '';

                if (galleryFromColorMap.length) {
                    setGallery(galleryFromColorMap);
                } else if (galleryFromButton.length) {
                    setGallery(galleryFromButton.map(url => ({
                        url: url,
                        thumb: url,
                        alt: @json($productName)
                    })));
                } else if (imageFromButton) {
                    setGallery([{
                        url: imageFromButton,
                        thumb: imageFromButton,
                        alt: @json($productName)
                    }]);
                }

                if (!size) {
                    if (variantInput) variantInput.value = variantIdFromButton;
                    if (productPrice && priceFromButton) productPrice.textContent = `${Number(priceFromButton).toFixed(2)} ${currency}`;
                    if (priceOriginal && priceFromButton) priceOriginal.textContent = `${Number(priceFromButton).toFixed(2)} ${currency}`;
                    if (dynamicSku) dynamicSku.textContent = skuFromButton || i18n.na;
                    if (skuInfo) skuInfo.textContent = skuFromButton || i18n.na;
                    if (footerSku) footerSku.textContent = skuFromButton || i18n.na;
                    if (dynamicStock) dynamicStock.textContent = stockFromButton || i18n.na;
                }
            } else if (bestVariant) {
                if (variantInput) variantInput.value = bestVariant.id || '';
                if (productPrice) productPrice.textContent = `${Number(bestVariant.price).toFixed(2)} ${currency}`;
                if (priceOriginal) priceOriginal.textContent = `${Number(bestVariant.price).toFixed(2)} ${currency}`;
                if (dynamicSku) dynamicSku.textContent = bestVariant.sku || i18n.na;
                if (skuInfo) skuInfo.textContent = bestVariant.sku || i18n.na;
                if (footerSku) footerSku.textContent = bestVariant.sku || i18n.na;
                if (dynamicStock) dynamicStock.textContent = bestVariant.stock ?? i18n.na;
            } else {
                resetBaseUI();
            }

            if (variantError) {
                variantError.style.display = 'none';
            }
        }

        function validateVariantBeforeSubmit(e) {
            const requireSize = hasSizes();
            const requireColor = hasColors();

            const size = selectedSize();
            const color = selectedColor();

            if (!requireSize && !requireColor) {
                return true;
            }

            if ((requireSize && !size) || (requireColor && !color)) {
                e.preventDefault();

                if (variantError) {
                    variantError.textContent = i18n.selectRequired;
                    variantError.style.display = 'block';
                }

                return false;
            }

            const key = `${size}-${color}`;

            if (requireSize && requireColor && !variantMap[key]) {
                e.preventDefault();

                if (variantError) {
                    variantError.textContent = i18n.combinationUnavailable;
                    variantError.style.display = 'block';
                }

                return false;
            }

            if (variantError) {
                variantError.style.display = 'none';
            }

            return true;
        }

        async function runCouponPreview() {
            const code = (couponInput?.value || '').trim();

            if (!code) {
                if (discountRow) discountRow.style.display = 'none';
                if (couponMsg) couponMsg.style.display = 'none';
                return;
            }

            const payload = new FormData();
            payload.append('_token', @json(csrf_token()));
            payload.append('product_id', productId);
            payload.append('variant_id', variantInput?.value || '');
            payload.append('coupon_code', code);

            try {
                const res = await fetch(@json(route('cart.coupon.preview')), {
                    method: 'POST',
                    body: payload
                });

                const data = await res.json();

                couponMsg.style.display = 'block';

                if (!data.ok) {
                    discountRow.style.display = 'none';
                    couponMsg.style.color = '#e3342f';
                    couponMsg.textContent = data.message || i18n.invalidCoupon;
                    return;
                }

                discountRow.style.display = 'block';
                priceDiscounted.textContent = data.final.toFixed(2) + ' ' + currency;
                couponMsg.style.color = '#38a169';
                couponMsg.textContent = `${data.code} ✅ (-${data.discount.toFixed(2)} ${currency})`;
            } catch (e) {
                discountRow.style.display = 'none';
                couponMsg.style.display = 'block';
                couponMsg.style.color = '#e3342f';
                couponMsg.textContent = i18n.checkFailed;
            }
        }

        if (applyBtn) {
            applyBtn.addEventListener('click', runCouponPreview);
        }

        if (couponInput) {
            couponInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    runCouponPreview();
                }
            });
        }

        if (sizeSelect) {
            sizeSelect.addEventListener('change', function () {
                updateVariantUI();
                setTimeout(runCouponPreview, 80);
            });
        }

        if (colorSelect) {
            colorSelect.addEventListener('change', function () {
                updateVariantUI();
                setTimeout(runCouponPreview, 80);
            });
        }

        colorButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                selectColorButton(this);
                setTimeout(runCouponPreview, 80);
            });
        });

        const addToCartForm = document.getElementById('addToCartForm');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', validateVariantBeforeSubmit);
        }

        setGallery(defaultGallery);
        resetBaseUI();

        const initialColorBtn = document.querySelector('.color-variant-btn');
        if (initialColorBtn && colorSelect && !colorSelect.value) {
            selectColorButton(initialColorBtn);
        } else {
            updateVariantUI();
        }
    });
</script>
@endsection
