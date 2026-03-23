@extends('site.app.layout')

@section('title', $productName)
@section('meta_description', $productDescription ?: $productName)
@section('og_image', $generalGallery[0]['url'] ?? '')
@section('og_type', 'product')

@php
    \App\Support\Analytics\Analytics::track('view_item', [
        'currency' => $productCurrency ?: 'SAR',
        'value'    => round((float) $productPrice, 2),
        'items'    => [\App\Support\Analytics\Analytics::item($product->id, (string) $productName, (float) $productPrice, 1, $categoryName ?: null)],
    ]);
@endphp

@push('head')
@php
    $variantStocks = collect($variantMatrix);
    $structuredProduct = array_filter([
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $productName,
        'description' => \Illuminate\Support\Str::limit(strip_tags((string) $productDescription), 300),
        'sku'         => $productSku,
        'image'       => collect($generalGallery)->pluck('url')->filter()->values()->all(),
        'category'    => $categoryName,
        'offers'      => [
            '@type'         => 'Offer',
            'url'           => url()->current(),
            'priceCurrency' => $productCurrency,
            'price'         => number_format((float) $productPrice, 2, '.', ''),
            'availability'  => $variantStocks->isEmpty() || $variantStocks->sum('stock') > 0
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
        ],
        'aggregateRating' => $reviewsCount > 0 ? [
            '@type'       => 'AggregateRating',
            'ratingValue' => $averageRating,
            'reviewCount' => $reviewsCount,
        ] : null,
    ]);
@endphp
<script type="application/ld+json">{!! json_encode($structuredProduct, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-product-product-detail.css') }}">

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

            {{-- ============================================================ --}}
            {{-- Gallery                                                       --}}
            {{-- ============================================================ --}}
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
                                            <img src="{{ $img['url'] }}" alt="{{ $img['alt'] }}" loading="lazy" decoding="async">

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

            {{-- ============================================================ --}}
            {{-- Details                                                       --}}
            {{-- ============================================================ --}}
            <div class="col-md-6 col-lg-5 p-b-30">
                <div class="product-details-box">

                    <h1 class="product-title js-name-detail">
                        {{ $productName }}
                    </h1>

                    <span class="product-price" id="productPrice">
                        {{ money((float) $productPrice, $productCurrency) }}
                    </span>

                    <div class="product-meta">
                        <div class="stext-102 cl3">
                            {{ __('site.sku') }}:
                            <span id="dynamicSku">{{ $productSku }}</span>
                        </div>

                        <div class="stext-102 cl3 p-t-5">
                            {{ __('site.stock') }}:
                            <span id="dynamicStock">{{ count($variantMatrix) ? __('site.choose_options_first') : __('site.n_a') }}</span>
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

                    {{-- Coupons --}}
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
                                                onclick="copyCouponCode({{ Js::from($coupon['code']) }})"
                                                class="offer-copy-btn flex-c-m stext-101 cl2 bg8 bor13 hov-btn3 p-lr-15 trans-04">
                                            {{ __('site.copy_code') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- ======================================================= --}}
                    {{-- Product Form                                             --}}
                    {{-- ======================================================= --}}
                    <div class="product-form-box">
                        <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                            @csrf

                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="variantInput" value="" autocomplete="off">

                            {{-- Dynamic Option Attributes --}}
                            @foreach($optionAttributes as $attribute)
                                <div class="option-row">
                                    <div class="option-label">
                                        {{ $attribute['name'] }}
                                    </div>

                                    <div class="option-field">
                                        @if(in_array($attribute['slug'], ['color', 'colors', 'colour', 'colours']))
                                            {{-- Color: show as clickable buttons --}}
                                            <div class="d-flex flex-wrap m-b-10 variant-option-buttons"
                                                 data-attribute-slug="{{ $attribute['slug'] }}">
                                                @foreach($attribute['values'] as $value)
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-dark mr-2 mb-2 variant-option-btn color-variant-btn"
                                                        data-attribute-slug="{{ $attribute['slug'] }}"
                                                        data-value-id="{{ $value['id'] }}"
                                                    >
                                                        {{ $value['value'] }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @else
                                            {{-- Other attributes: show as select dropdown --}}
                                            <div class="rs1-select2 bor8 bg0">
                                                <select class="js-select2 variant-option-select"
                                                        name="options[{{ $attribute['slug'] }}]"
                                                        data-attribute-slug="{{ $attribute['slug'] }}">
                                                    <option value="">{{ __('site.choose_option') }}</option>

                                                    @foreach($attribute['values'] as $value)
                                                        <option value="{{ $value['id'] }}">
                                                            {{ $value['value'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="dropDownSelect2"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            {{-- Quantity + Add to Cart --}}
                            <div class="option-row">
                                <div class="option-label"></div>

                                <div class="option-field">
                                    <div class="product-qty-row">
                                        <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                            <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m" role="button" tabindex="0" aria-label="{{ __('site.decrease_quantity') }}">
                                                <i class="fs-16 zmdi zmdi-minus"></i>
                                            </div>

                                            <input class="mtext-104 cl3 txt-center num-product"
                                                   type="number"
                                                   name="quantity"
                                                   value="1"
                                                   min="1"
                                                   max="20"
                                                   inputmode="numeric"
                                                   aria-label="{{ __('site.quantity') }}"
                                                   id="qtyInput">

                                            <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m" role="button" tabindex="0" aria-label="{{ __('site.increase_quantity') }}">
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

                            {{-- Coupon Preview --}}
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
                                            {{ money((float) $productPrice, $productCurrency) }}
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

                    {{-- Wishlist + Share --}}
                    <div class="product-actions-extra">
                        <div class="product-share-group">
                            @auth
                                <form action="{{ route('wishlist.toggle') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="variant_id" value="">

                                    <button type="submit" class="wishlist-circle-btn">
                                        @if($wishlistExists)
                                            <img src="{{ asset('cozastore/images/icons/icon-heart-02.png') }}" alt="wishlist" loading="lazy" decoding="async">
                                        @else
                                            <img src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" alt="wishlist" loading="lazy" decoding="async">
                                        @endif
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="wishlist-circle-btn">
                                    <img src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" alt="wishlist" loading="lazy" decoding="async">
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

        {{-- ================================================================== --}}
        {{-- Tabs                                                                --}}
        {{-- ================================================================== --}}
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

                    {{-- Description --}}
                    <div class="tab-pane fade show active" id="tab_description" role="tabpanel">
                        <div class="how-pos2 p-lr-15-md">
                            <p class="stext-102 cl6">
                                {{ $productDescription ?: __('site.no_description_yet') }}
                            </p>
                        </div>
                    </div>

                    {{-- Additional Information --}}
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
                                        <span class="product-info-value">{{ strtoupper((string) $productCurrency) === 'SAR' ? __('SAR') : ($productCurrency ?: '-') }}</span>
                                    </li>
                                    <li>
                                        <span class="product-info-label">{{ __('site.sku') }}</span>
                                        <span class="product-info-value" id="skuInfo">{{ $productSku }}</span>
                                    </li>

                                    {{-- Dynamic specifications from variant attributes --}}
                                    @foreach($specifications as $spec)
                                        <li>
                                            <span class="product-info-label">{{ $spec['label'] }}</span>
                                            <span class="product-info-value">{{ $spec['value'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Reviews --}}
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

{{-- Related Products --}}
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
                                <img src="{{ $rp['image'] }}" alt="{{ $rp['name'] }}" loading="lazy" decoding="async">

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
                                        {{ money($rp['price'], $rp['currency']) }}
                                    </span>
                                </div>

                                <div class="block2-txt-child2 flex-r p-t-3">
                                    <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
                                        <img class="icon-heart1 dis-block trans-04"
                                             src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" alt="ICON" loading="lazy" decoding="async">
                                        <img class="icon-heart2 dis-block trans-04 ab-t-l"
                                             src="{{ asset('cozastore/images/icons/icon-heart-02.png') }}" alt="ICON" loading="lazy" decoding="async">
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

        // ----------------------------------------------------------------
        // DOM refs
        // ----------------------------------------------------------------
        const couponInput     = document.getElementById('couponCodeInput');
        const applyBtn        = document.getElementById('applyCouponBtn');
        const priceDiscounted = document.getElementById('priceDiscounted');
        const discountRow     = document.getElementById('discountRow');
        const couponMsg       = document.getElementById('couponMsg');

        const variantInput  = document.getElementById('variantInput');
        const variantError  = document.getElementById('variantError');
        const productPrice  = document.getElementById('productPrice');
        const dynamicSku    = document.getElementById('dynamicSku');
        const dynamicStock  = document.getElementById('dynamicStock');
        const skuInfo       = document.getElementById('skuInfo');
        const footerSku     = document.getElementById('footerSku');
        const priceOriginal = document.getElementById('priceOriginal');

        const optionSelects = document.querySelectorAll('.variant-option-select');
        const optionButtons = document.querySelectorAll('.variant-option-btn');

        // ----------------------------------------------------------------
        // PHP data
        // ----------------------------------------------------------------
        const productId     = {{ (int) $product->id }};
        const currency      = @json(strtoupper((string) $productCurrency) === 'SAR' || ! $productCurrency ? __('SAR') : $productCurrency);
        const basePrice     = {{ (float) $productPrice }};
        const baseSku       = @json($productSku);
        const variantMatrix = @json($variantMatrix);
        const defaultGallery = @json($generalGallery);

        const i18n = {
            copied:                   @json(__('site.copy_coupon_success')),
            invalidCoupon:            @json(__('site.invalid_coupon')),
            checkFailed:              @json(__('site.coupon_check_failed')),
            selectRequired:           @json(__('site.select_required_options')),
            combinationUnavailable:   @json(__('site.selected_combination_unavailable')),
            couponApplied:            @json(__('site.apply_coupon')),
            outOfStock:               @json(__('site.out_of_stock_variant')),
            na:                       @json(__('site.n_a')),
            chooseOptions:            @json(__('site.choose_options_first')),
        };

        // ----------------------------------------------------------------
        // Helpers
        // ----------------------------------------------------------------
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
            if (!(window.jQuery && $('.wrap-slick3').length)) return;

            const $slider = $('.wrap-slick3 .slick3');

            if ($slider.hasClass('slick-initialized')) {
                $slider.slick('unslick');
            }

            $('.wrap-slick3-dots').html('');
            $('.wrap-slick3-arrows').html('');
        }

        function initProductGallery(imageCount) {
            if (!(window.jQuery && $('.wrap-slick3').length)) return;

            destroySlickIfExists();

            $('.wrap-slick3').each(function () {
                $(this).find('.slick3').slick({
                    slidesToShow:   1,
                    slidesToScroll: 1,
                    fade:           true,
                    infinite:       false,
                    autoplay:       false,
                    autoplaySpeed:  6000,
                    arrows:         imageCount > 1,
                    appendArrows:   $(this).find('.wrap-slick3-arrows'),
                    prevArrow: '<button class="arrow-slick3 prev-slick3"><i class="fa fa-angle-left" aria-hidden="true"></i></button>',
                    nextArrow: '<button class="arrow-slick3 next-slick3"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
                    dots:         imageCount > 1,
                    appendDots:   $(this).find('.wrap-slick3-dots'),
                    dotsClass:    'slick3-dots',
                    customPaging: function (slick, index) {
                        const portrait = $(slick.$slides[index]).data('thumb');
                        return '<img src="' + portrait + '" loading="lazy" decoding="async" /><div class="slick3-dot-overlay"></div>';
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

            // Built with DOM nodes, never HTML strings, so image names and
            // alt text from the catalogue are always treated as plain text.
            const safeUrl = (value) => {
                try {
                    const parsed = new URL(String(value || ''), window.location.href);
                    return ['http:', 'https:'].includes(parsed.protocol) ? parsed.href : '';
                } catch (e) {
                    return '';
                }
            };

            gallery.replaceChildren(...finalImages.map(img => {
                const url   = safeUrl(img.url || img);
                const thumb = safeUrl(img.thumb) || url;
                const alt   = String(img.alt || @json($productName));

                const item = document.createElement('div');
                item.className = 'item-slick3';
                item.dataset.thumb = thumb;

                const frame = document.createElement('div');
                frame.className = 'product-image-frame';

                const image = document.createElement('img');
                image.src = url;
                image.alt = alt;
                image.loading = 'lazy';
                image.decoding = 'async';

                const zoom = document.createElement('a');
                zoom.className = 'flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04';
                zoom.href = url;
                const icon = document.createElement('i');
                icon.className = 'fa fa-expand';
                zoom.appendChild(icon);

                frame.append(image, zoom);
                item.appendChild(frame);

                return item;
            }));

            initProductGallery(finalImages.length);
        }

        // ----------------------------------------------------------------
        // Dynamic Option Logic
        // ----------------------------------------------------------------

        /**
         * Read the currently selected attribute values from selects + active buttons.
         * Returns: { slug: "valueId", ... }
         */
        function getSelectedOptions() {
            const selected = {};

            // From <select> dropdowns
            optionSelects.forEach(select => {
                const slug  = select.dataset.attributeSlug;
                const value = select.value;
                if (slug && value) {
                    selected[slug] = String(value);
                }
            });

            // From toggle buttons (only those marked active)
            optionButtons.forEach(btn => {
                const slug = btn.dataset.attributeSlug;
                if (!slug) return;

                if (btn.classList.contains('active')) {
                    selected[slug] = String(btn.dataset.valueId || '');
                }
            });

            return selected;
        }

        /**
         * Build a sorted variant key from a selected-options map.
         */
        function buildVariantKey(selected) {
            return Object.keys(selected)
                .sort()
                .map(slug => `${slug}:${selected[slug]}`)
                .join('|');
        }

        /**
         * Find the best matching variant for the currently selected options.
         * Tries exact key first, then partial matching.
         */
        function requiredOptionCount() {
            return document.querySelectorAll('.variant-option-select').length +
                document.querySelectorAll('.variant-option-buttons').length;
        }

        /**
         * Return the variant for the selected options, or null until every
         * option group has a value. A partial choice (colour without size)
         * must never resolve to an arbitrary variant.
         */
        function findMatchingVariant(selected) {
            if (Object.keys(selected).length < requiredOptionCount()) {
                return null;
            }

            return variantMatrix[buildVariantKey(selected)] || null;
        }

        /**
         * Mark a button active and deactivate siblings for the same attribute.
         */
        function setActiveOptionButton(button) {
            const slug = button.dataset.attributeSlug;

            document.querySelectorAll(`.variant-option-btn[data-attribute-slug="${slug}"]`)
                .forEach(btn => btn.classList.remove('active'));

            button.classList.add('active');
        }

        // ----------------------------------------------------------------
        // UI Update
        // ----------------------------------------------------------------
        function resetBaseUI() {
            if (productPrice) {
                productPrice.textContent = `${Number(basePrice).toFixed(2)} ${currency}`;
            }
            if (priceOriginal) {
                priceOriginal.textContent = `${Number(basePrice).toFixed(2)} ${currency}`;
            }
            if (dynamicSku)  dynamicSku.textContent  = baseSku || i18n.na;
            if (skuInfo)      skuInfo.textContent      = baseSku || i18n.na;
            if (footerSku)    footerSku.textContent    = baseSku || i18n.na;
            if (dynamicStock) dynamicStock.textContent = requiredOptionCount() ? i18n.chooseOptions : i18n.na;
            if (variantInput) variantInput.value       = '';
        }

        function updateVariantUI() {
            const selected      = getSelectedOptions();
            const selectedCount = Object.keys(selected).length;

            if (!selectedCount) {
                resetBaseUI();
                setGallery(defaultGallery);
                if (variantError) variantError.style.display = 'none';
                return;
            }

            const variant = findMatchingVariant(selected);

            if (!variant) {
                resetBaseUI();
                const complete = selectedCount >= requiredOptionCount();
                if (variantError) {
                    variantError.textContent = i18n.combinationUnavailable;
                    variantError.style.display = complete ? 'block' : 'none';
                }
                return;
            }

            // Apply variant data to UI
            if (variantInput)  variantInput.value           = variant.id    || '';
            if (productPrice)  productPrice.textContent     = `${Number(variant.price).toFixed(2)} ${currency}`;
            if (priceOriginal) priceOriginal.textContent    = `${Number(variant.price).toFixed(2)} ${currency}`;
            if (dynamicSku)    dynamicSku.textContent       = variant.sku   || i18n.na;
            if (skuInfo)       skuInfo.textContent          = variant.sku   || i18n.na;
            if (footerSku)     footerSku.textContent        = variant.sku   || i18n.na;
            if (dynamicStock)  dynamicStock.textContent     = variant.stock ?? i18n.na;

            setGallery(
                (variant.images && variant.images.length) ? variant.images : defaultGallery
            );

            if (variantError) variantError.style.display = 'none';
        }

        // ----------------------------------------------------------------
        // Form Validation
        // ----------------------------------------------------------------
        function validateVariantBeforeSubmit(e) {
            const totalOptions =
                document.querySelectorAll('.variant-option-select').length +
                document.querySelectorAll('.variant-option-buttons').length;

            if (!totalOptions) return true;

            const selected = getSelectedOptions();
            const variant  = findMatchingVariant(selected);

            if (!variant || Number(variant.stock) < 1) {
                e.preventDefault();
                if (variantError) {
                    variantError.textContent   = variant ? i18n.outOfStock : i18n.selectRequired;
                    variantError.style.display = 'block';
                }
                return false;
            }

            if (variantInput) variantInput.value = variant.id;

            return true;
        }

        // ----------------------------------------------------------------
        // Coupon Preview
        // ----------------------------------------------------------------
        async function runCouponPreview() {
            const code = (couponInput?.value || '').trim();

            if (!code) {
                if (discountRow) discountRow.style.display = 'none';
                if (couponMsg)   couponMsg.style.display   = 'none';
                return;
            }

            const payload = new FormData();
            payload.append('_token',      @json(csrf_token()));
            payload.append('product_id',  productId);
            payload.append('variant_id',  variantInput?.value || '');
            payload.append('coupon_code', code);

            try {
                const res  = await fetch(@json(route('cart.coupon.preview')), {
                    method: 'POST',
                    body:   payload
                });
                const data = await res.json();

                couponMsg.style.display = 'block';

                if (!data.ok) {
                    discountRow.style.display = 'none';
                    couponMsg.style.color     = '#e3342f';
                    couponMsg.textContent     = data.message || i18n.invalidCoupon;
                    return;
                }

                discountRow.style.display   = 'block';
                priceDiscounted.textContent = data.final.toFixed(2) + ' ' + currency;
                couponMsg.style.color       = '#38a169';
                couponMsg.textContent       = `${data.code} ✅ (-${data.discount.toFixed(2)} ${currency})`;
            } catch (err) {
                discountRow.style.display = 'none';
                couponMsg.style.display   = 'block';
                couponMsg.style.color     = '#e3342f';
                couponMsg.textContent     = i18n.checkFailed;
            }
        }

        // ----------------------------------------------------------------
        // Event Bindings
        // ----------------------------------------------------------------
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

        // Select dropdowns
        // Select2 dispatches jQuery events, which native listeners never see.
        const onOptionChange = function () {
            updateVariantUI();
            setTimeout(runCouponPreview, 80);
        };

        optionSelects.forEach(select => {
            if (window.jQuery) {
                window.jQuery(select).on('change', onOptionChange);
            } else {
                select.addEventListener('change', onOptionChange);
            }
        });

        // Toggle buttons (color, etc.)
        optionButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                setActiveOptionButton(this);
                updateVariantUI();
                setTimeout(runCouponPreview, 80);
            });
        });

        // Form submit validation
        const addToCartForm = document.getElementById('addToCartForm');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', validateVariantBeforeSubmit);
        }

        // ----------------------------------------------------------------
        // Init
        // ----------------------------------------------------------------
        setGallery(defaultGallery);
        resetBaseUI();

        // Pre-select any option group that has a single choice (e.g. one colour).
        document.querySelectorAll('.variant-option-buttons').forEach(group => {
            const buttons = group.querySelectorAll('.variant-option-btn');
            if (buttons.length === 1) setActiveOptionButton(buttons[0]);
        });

        optionSelects.forEach(select => {
            const choices = Array.from(select.options).filter(option => option.value !== '');
            if (choices.length === 1) {
                select.value = choices[0].value;
                if (window.jQuery) window.jQuery(select).trigger('change.select2');
            }
        });

        updateVariantUI();
    });
</script>
@endsection
