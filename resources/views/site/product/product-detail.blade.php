@extends('site.app.layout')

@section('content')

    <br><br><br>

    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">
                Home
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

    <section class="sec-product-detail bg0 p-t-65 p-b-60">
        <div class="container">
            <div class="row">

                <div class="col-md-6 col-lg-7 p-b-30">
                    <div class="p-l-25 p-r-30 p-lr-0-lg">
                        <div class="wrap-slick3 flex-sb flex-w">
                            <div class="wrap-slick3-dots"></div>
                            <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                            <div class="slick3 gallery-lb" id="productGallery">
                                @foreach($generalGallery as $img)
                                    <div class="item-slick3" data-thumb="{{ $img['thumb'] }}">
                                        <div class="wrap-pic-w pos-relative">
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

                <div class="col-md-6 col-lg-5 p-b-30">
                    <div class="p-r-50 p-t-5 p-lr-0-lg">

                        <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                            {{ $productName }}
                        </h4>

                        <span class="mtext-106 cl2" id="productPrice">
                            {{ number_format((float) $productPrice, 2) }} {{ $productCurrency }}
                        </span>

                        <div class="p-t-10">
                            <div class="stext-102 cl3">
                                SKU: <span id="dynamicSku">{{ $productSku }}</span>
                            </div>

                            <div class="stext-102 cl3 p-t-5">
                                Stock: <span id="dynamicStock">{{ $product->variants?->first()?->stock ?? 'N/A' }}</span>
                            </div>

                            <p class="stext-102 p-t-5" id="stockMessage" style="display:none;color:#e3342f;">
                                This variant is out of stock.
                            </p>
                        </div>

                        @if($reviewsCount > 0)
                            <div class="p-b-15 p-t-10">
                                <span class="fs-18 cl11">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            ⭐
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </span>

                                <span class="stext-102 cl3">
                                    {{ number_format((float) $averageRating, 1) }}/5
                                    ({{ $reviewsCount }} Reviews)
                                </span>
                            </div>
                        @else
                            <div class="p-b-15 p-t-10">
                                <span class="stext-102 cl3">
                                    No reviews yet
                                </span>
                            </div>
                        @endif

                        @if(count($productCoupons))
                            <div class="p-t-10 p-b-10">
                                <h5 class="mtext-104 cl2 p-b-10">
                                    Available Offers
                                </h5>

                                @foreach($productCoupons as $coupon)
                                    <div class="bor10 p-lr-20 p-tb-15 m-b-10" style="background:#f9f9f9;">
                                        <div class="flex-w flex-sb-m">
                                            <div>
                                                <span class="stext-111 cl2" style="font-weight:600;">
                                                    Coupon: <span style="color:#e63946;">{{ $coupon['code'] }}</span>
                                                </span>

                                                <p class="stext-115 cl6 m-t-5">
                                                    {{ $coupon['label'] }}
                                                </p>
                                            </div>

                                            <button type="button" onclick="copyCouponCode('{{ $coupon['code'] }}')"
                                                class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04">
                                                Copy Code
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="p-t-20">
                            <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                                @csrf

                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="variant_id" id="variantInput" value="">

                                @if(count($sizes))
                                    <div class="flex-w flex-r-m p-b-10">
                                        <div class="size-203 flex-c-m respon6">
                                            Size
                                        </div>

                                        <div class="size-204 respon6-next">
                                            <div class="rs1-select2 bor8 bg0">
                                                <select class="js-select2" name="size" id="sizeSelect">
                                                    <option value="">Choose an option</option>
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
                                    <div class="flex-w flex-r-m p-b-10">
                                        <div class="size-203 flex-c-m respon6">
                                            Color
                                        </div>

                                        <div class="size-204 respon6-next">

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
                                                    <option value="">Choose an option</option>
                                                    @foreach($colors as $color)
                                                        <option value="{{ $color['id'] }}">{{ $color['value'] }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="dropDownSelect2"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex-w flex-r-m p-b-10">
                                    <div class="size-204 flex-w flex-m respon6-next">
                                        <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                            <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-minus"></i>
                                            </div>

                                            <input class="mtext-104 cl3 txt-center num-product" type="number"
                                                name="quantity" value="1" min="1" id="qtyInput">

                                            <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-plus"></i>
                                            </div>
                                        </div>

                                        <button type="submit"
                                            class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04"
                                            id="addToCartBtn">
                                            Add to cart
                                        </button>
                                    </div>
                                </div>

                                <div class="p-t-15">
                                    <div class="bor10 p-lr-20 p-tb-15">
                                        <div class="flex-w flex-m">
                                            <input type="text" name="coupon_code" id="couponCodeInput"
                                                value="{{ old('coupon_code') }}" placeholder="Enter coupon code"
                                                class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5"
                                                style="max-width:250px;">

                                            <button type="button" id="applyCouponBtn"
                                                class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
                                                Apply Coupon
                                            </button>
                                        </div>

                                        <div class="p-t-10">
                                            <div class="stext-115 cl6">
                                                Original Price:
                                                <span id="priceOriginal">{{ number_format((float) $productPrice, 2) }}
                                                    {{ $productCurrency }}</span>
                                            </div>

                                            <div id="discountRow" class="stext-115"
                                                style="display:none;color:#e63946;margin-top:6px;">
                                                Discounted Price:
                                                <span id="priceDiscounted"></span>
                                            </div>

                                            <div id="couponMsg" class="stext-102" style="display:none;margin-top:6px;">
                                            </div>

                                            @if(session('cart_coupon'))
                                                <div class="p-t-8">
                                                    <span class="stext-102" style="color:#38a169;font-weight:600;">
                                                        Active Coupon: {{ session('cart_coupon.code') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <p class="stext-102 cl3 p-t-10" id="variantError" style="display:none;color:#e3342f;">
                                    Please select the required options.
                                </p>
                            </form>
                        </div>

                        <div class="flex-w flex-m p-l-100 p-t-40 respon7">
                            <div class="block2-txt-child2 flex-r p-t-3">
                                @auth
                                    <form action="{{ route('wishlist.toggle') }}" method="POST" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="variant_id" value="">
                                        <button type="submit"
                                            style="background:transparent;border:none;padding:0;cursor:pointer">
                                            @if($wishlistExists)
                                                <img src="{{ asset('cozastore/images/icons/icon-heart-02.png') }}" alt="wishlist">
                                            @else
                                                <img src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" alt="wishlist">
                                            @endif
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}">
                                        <img src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" alt="wishlist">
                                    </a>
                                @endauth
                            </div>

                            <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                data-tooltip="Facebook">
                                <i class="fa fa-facebook"></i>
                            </a>
                            <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                data-tooltip="Twitter">
                                <i class="fa fa-twitter"></i>
                            </a>
                            <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                data-tooltip="Google Plus">
                                <i class="fa fa-google-plus"></i>
                            </a>
                        </div>

                    </div>
                </div>

            </div>

            <div class="bor10 m-t-50 p-t-43 p-b-40">
                <div class="tab01">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item p-b-10">
                            <a class="nav-link active" data-toggle="tab" href="#tab_description" role="tab">Description</a>
                        </li>
                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#tab_information" role="tab">Additional information</a>
                        </li>
                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#tab_reviews" role="tab">Reviews</a>
                        </li>
                    </ul>

                    <div class="tab-content p-t-43">
                        <div class="tab-pane fade show active" id="tab_description" role="tabpanel">
                            <div class="how-pos2 p-lr-15-md">
                                <p class="stext-102 cl6">{{ $productDescription ?: 'No description yet.' }}</p>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab_information" role="tabpanel">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <ul class="p-lr-28 p-lr-15-sm">
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">Category</span>
                                            <span class="stext-102 cl6 size-206">{{ $categoryName ?: '-' }}</span>
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">Currency</span>
                                            <span class="stext-102 cl6 size-206">{{ $productCurrency ?: '-' }}</span>
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">SKU</span>
                                            <span class="stext-102 cl6 size-206" id="skuInfo">{{ $productSku }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab_reviews" role="tabpanel">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <div class="p-b-30 m-lr-15-sm">

                                        <h5 class="mtext-104 p-b-20">
                                            {{ $reviewsCount }} Reviews
                                        </h5>

                                        @forelse($reviews as $review)
                                            <div class="flex-w flex-t p-b-25">
                                                <div class="size-207">
                                                    <div class="flex-w flex-sb-m p-b-8">
                                                        <span class="mtext-107 cl2">
                                                            {{ $review['user_name'] }}
                                                        </span>

                                                        <span class="fs-18 cl11">
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
                                                        <p class="stext-102 cl3 m-b-5">
                                                            <strong>{{ $review['title'] }}</strong>
                                                        </p>
                                                    @endif

                                                    <p class="stext-102 cl6">
                                                        {{ $review['comment'] }}
                                                    </p>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="stext-102 cl6">
                                                No reviews yet.
                                            </p>
                                        @endforelse

                                    </div>

                                    @auth
                                        <div class="bor10 p-lr-20 p-tb-20">
                                            <h4 class="mtext-105 cl2 p-b-20">
                                                Add a review
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
                                                        Rating
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
                                                    <input type="text" name="title" value="{{ old('title') }}"
                                                        placeholder="Review title"
                                                        class="stext-111 cl2 plh3 size-116 bor8 p-lr-20">
                                                </div>

                                                <div class="p-b-15">
                                                    <textarea name="comment" rows="4" placeholder="Write your review..."
                                                        class="stext-111 cl2 plh3 size-116 bor8 p-lr-20 p-tb-10">{{ old('comment') }}</textarea>
                                                </div>

                                                <button type="submit"
                                                    class="flex-c-m stext-101 cl0 size-121 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                                    Submit Review
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="p-t-20">
                                            <a href="{{ route('login') }}" class="stext-102 cl3">
                                                Login to write a review
                                            </a>
                                        </div>
                                    @endauth

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg6 flex-c-m flex-w size-302 m-t-73 p-tb-15">
            <span class="stext-107 cl6 p-lr-25">SKU: <span id="footerSku">{{ $productSku }}</span></span>
            <span class="stext-107 cl6 p-lr-25">Category: {{ $categoryName ?: 'N/A' }}</span>
        </div>
    </section>

    <section class="sec-relate-product bg0 p-t-45 p-b-105">
        <div class="container">
            <div class="p-b-45">
                <h3 class="ltext-106 cl5 txt-center">Related Products</h3>
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
                                        View
                                    </a>
                                </div>

                                <div class="block2-txt flex-w flex-t p-t-14">
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
                            <p class="stext-102 cl6">No related products.</p>
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
                            <div class="wrap-pic-w pos-relative">
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

                if (dynamicSku) dynamicSku.textContent = baseSku || 'N/A';
                if (skuInfo) skuInfo.textContent = baseSku || 'N/A';
                if (footerSku) footerSku.textContent = baseSku || 'N/A';
                if (dynamicStock) dynamicStock.textContent = 'N/A';

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
                        if (dynamicSku) dynamicSku.textContent = exactVariant.sku || 'N/A';
                        if (skuInfo) skuInfo.textContent = exactVariant.sku || 'N/A';
                        if (footerSku) footerSku.textContent = exactVariant.sku || 'N/A';
                        if (dynamicStock) dynamicStock.textContent = exactVariant.stock ?? 'N/A';

                        if (variantError) {
                            variantError.style.display = 'none';
                        }

                        return;
                    }

                    resetBaseUI();

                    if (variantError) {
                        variantError.textContent = 'Selected combination is not available.';
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
                        if (dynamicSku) dynamicSku.textContent = skuFromButton || 'N/A';
                        if (skuInfo) skuInfo.textContent = skuFromButton || 'N/A';
                        if (footerSku) footerSku.textContent = skuFromButton || 'N/A';
                        if (dynamicStock) dynamicStock.textContent = stockFromButton || 'N/A';
                    }
                } else if (bestVariant) {
                    if (variantInput) variantInput.value = bestVariant.id || '';
                    if (productPrice) productPrice.textContent = `${Number(bestVariant.price).toFixed(2)} ${currency}`;
                    if (priceOriginal) priceOriginal.textContent = `${Number(bestVariant.price).toFixed(2)} ${currency}`;
                    if (dynamicSku) dynamicSku.textContent = bestVariant.sku || 'N/A';
                    if (skuInfo) skuInfo.textContent = bestVariant.sku || 'N/A';
                    if (footerSku) footerSku.textContent = bestVariant.sku || 'N/A';
                    if (dynamicStock) dynamicStock.textContent = bestVariant.stock ?? 'N/A';
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
                        variantError.textContent = 'Please select all required options.';
                        variantError.style.display = 'block';
                    }

                    return false;
                }

                const key = `${size}-${color}`;

                if (requireSize && requireColor && !variantMap[key]) {
                    e.preventDefault();

                    if (variantError) {
                        variantError.textContent = 'Selected combination is not available.';
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
                        couponMsg.textContent = data.message || 'Invalid coupon';
                        return;
                    }

                    discountRow.style.display = 'block';
                    priceDiscounted.textContent = data.final.toFixed(2) + ' ' + currency;
                    couponMsg.style.color = '#38a169';
                    couponMsg.textContent = `${data.code} applied ✅ (-${data.discount.toFixed(2)} ${currency})`;
                } catch (e) {
                    discountRow.style.display = 'none';
                    couponMsg.style.display = 'block';
                    couponMsg.style.color = '#e3342f';
                    couponMsg.textContent = 'Something went wrong while checking the coupon.';
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
