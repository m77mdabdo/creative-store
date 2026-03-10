@extends('site.app.layout')

@section('content')

<br><br><br>

@php
    use Illuminate\Support\Facades\Storage;

    $name = $product->name ?? $product->slug;
    $desc = $product->description ?? '';

    $variantMinPrice = $product->variants?->min('price');
    $price = $variantMinPrice ?? $product->base_price;

    $fallbackMain = asset('cozastore/images/product-detail-01.jpg');
    $fallbackThumb = asset('cozastore/images/product-detail-02.jpg');

    $gallery = $galleryImages ?? collect();
    $firstImg = $gallery->first();

    $mainImgUrl = $firstImg?->path
        ? Storage::url($firstImg->path)
        : ($product->primaryImage?->path ? Storage::url($product->primaryImage->path) : $fallbackMain);

    $catName = $product->category?->name ?? $product->category?->slug ?? null;
    $catSlug = $product->category?->slug ?? null;

    $sku = $product->variants?->first()?->sku ?? 'N/A';
@endphp

<div class="container">
    <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
        <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">
            Home
            <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
        </a>

        @if($catSlug)
            <a href="{{ url('/products?category=' . $catSlug) }}" class="stext-109 cl8 hov-cl1 trans-04">
                {{ $catName }}
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>
        @endif

        <span class="stext-109 cl4">
            {{ $name }}
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

                        <div class="slick3 gallery-lb">
                            @forelse($gallery as $img)
                                @php
                                    $imgUrl = $img->path ? Storage::url($img->path) : $fallbackMain;
                                    $thumbUrl = $img->path ? Storage::url($img->path) : $fallbackThumb;
                                    $alt = $img->alt_text ?: $name;
                                @endphp

                                <div class="item-slick3" data-thumb="{{ $thumbUrl }}">
                                    <div class="wrap-pic-w pos-relative">
                                        <img src="{{ $imgUrl }}" alt="{{ $alt }}">

                                        <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                            href="{{ $imgUrl }}">
                                            <i class="fa fa-expand"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="item-slick3" data-thumb="{{ $mainImgUrl }}">
                                    <div class="wrap-pic-w pos-relative">
                                        <img src="{{ $mainImgUrl }}" alt="{{ $name }}">

                                        <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                            href="{{ $mainImgUrl }}">
                                            <i class="fa fa-expand"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-5 p-b-30">
                <div class="p-r-50 p-t-5 p-lr-0-lg">

                    <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                        {{ $name }}
                    </h4>

                    @if(isset($productCoupons) && $productCoupons->count())
                        <div class="p-t-10 p-b-10">
                            <h5 class="mtext-104 cl2 p-b-10">
                                Available Offers
                            </h5>

                            @foreach($productCoupons as $coupon)
                                <div class="bor10 p-lr-20 p-tb-15 m-b-10" style="background:#f9f9f9;">
                                    <div class="flex-w flex-sb-m">
                                        <div>
                                            <span class="stext-111 cl2" style="font-weight:600;">
                                                Coupon: <span style="color:#e63946;">{{ $coupon->code }}</span>
                                            </span>

                                            <p class="stext-115 cl6 m-t-5">
                                                @if($coupon->type === 'percent')
                                                    Get {{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}% off on this product
                                                @elseif($coupon->type === 'fixed')
                                                    Get {{ number_format($coupon->value, 2) }} {{ $product->currency ?? 'SAR' }} off on this product
                                                @endif
                                            </p>
                                        </div>

                                        <button type="button"
                                            onclick="copyCouponCode('{{ $coupon->code }}')"
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

                            @if(isset($sizes) && $sizes->count())
                                <div class="flex-w flex-r-m p-b-10">
                                    <div class="size-203 flex-c-m respon6">
                                        Size
                                    </div>

                                    <div class="size-204 respon6-next">
                                        <div class="rs1-select2 bor8 bg0">
                                            <select class="js-select2" name="size" id="sizeSelect">
                                                <option value="">Choose an option</option>
                                                @foreach($sizes as $s)
                                                    <option value="{{ $s->id }}">{{ $s->value }}</option>
                                                @endforeach
                                            </select>
                                            <div class="dropDownSelect2"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($colors) && $colors->count())
                                <div class="flex-w flex-r-m p-b-10">
                                    <div class="size-203 flex-c-m respon6">
                                        Color
                                    </div>

                                    <div class="size-204 respon6-next">
                                        <div class="rs1-select2 bor8 bg0">
                                            <select class="js-select2" name="color" id="colorSelect">
                                                <option value="">Choose an option</option>
                                                @foreach($colors as $c)
                                                    <option value="{{ $c->id }}">{{ $c->value }}</option>
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
                                        Add to cart
                                    </button>
                                </div>
                            </div>

                            <div class="p-t-15">
                                <div class="bor10 p-lr-20 p-tb-15">
                                    <div class="flex-w flex-m">
                                        <input
                                            type="text"
                                            name="coupon_code"
                                            id="couponCodeInput"
                                            value="{{ old('coupon_code') }}"
                                            placeholder="Enter coupon code"
                                            class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5"
                                            style="max-width:250px;"
                                        >

                                        <button
                                            type="button"
                                            id="applyCouponBtn"
                                            class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
                                            Apply Coupon
                                        </button>
                                    </div>

                                    <div class="p-t-10">
                                        <div class="stext-115 cl6">
                                            Original Price:
                                            <span id="priceOriginal">{{ number_format((float) $price, 2) }} {{ $product->currency }}</span>
                                        </div>

                                        <div id="discountRow" class="stext-115" style="display:none;color:#e63946;margin-top:6px;">
                                            Discounted Price:
                                            <span id="priceDiscounted"></span>
                                        </div>

                                        <div id="couponMsg" class="stext-102" style="display:none;margin-top:6px;"></div>

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
                                Please select Size and Color.
                            </p>
                        </form>
                    </div>

                    <div class="flex-w flex-m p-l-100 p-t-40 respon7">
                        @php
                            $wishlistExists = false;

                            if (auth()->check()) {
                                $wishlistExists = \App\Models\WishlistItem::whereHas('wishlist', function ($query) {
                                    $query->where('user_id', auth()->id());
                                })
                                    ->where('product_id', $product->id)
                                    ->whereNull('variant_id')
                                    ->exists();
                            }
                        @endphp

                        <div class="block2-txt-child2 flex-r p-t-3">
                            @auth
                                <form action="{{ route('wishlist.toggle') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="variant_id" value="">
                                    <button type="submit" style="background:transparent;border:none;padding:0;cursor:pointer">
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

                        <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100" data-tooltip="Facebook">
                            <i class="fa fa-facebook"></i>
                        </a>
                        <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100" data-tooltip="Twitter">
                            <i class="fa fa-twitter"></i>
                        </a>
                        <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100" data-tooltip="Google Plus">
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
                            <p class="stext-102 cl6">{{ $desc ?: 'No description yet.' }}</p>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_information" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                <ul class="p-lr-28 p-lr-15-sm">
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">Category</span>
                                        <span class="stext-102 cl6 size-206">{{ $catName ?: '-' }}</span>
                                    </li>
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">Currency</span>
                                        <span class="stext-102 cl6 size-206">{{ $product->currency ?: '-' }}</span>
                                    </li>
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">SKU</span>
                                        <span class="stext-102 cl6 size-206">{{ $sku }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_reviews" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                <div class="p-b-30 m-lr-15-sm">
                                    <p class="stext-102 cl6">Reviews section (لما تربط reviews table هنعرضها هنا).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="bg6 flex-c-m flex-w size-302 m-t-73 p-tb-15">
        <span class="stext-107 cl6 p-lr-25">SKU: {{ $sku }}</span>
        <span class="stext-107 cl6 p-lr-25">Category: {{ $catName ?: 'N/A' }}</span>
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
                    @php
                        $rpName = $rp->name ?? $rp->slug;
                        $rpImg = $rp->primaryImage?->path ? Storage::url($rp->primaryImage->path) : asset('cozastore/images/product-01.jpg');
                    @endphp

                    <div class="item-slick2 p-l-15 p-r-15 p-t-15 p-b-15">
                        <div class="block2">
                            <div class="block2-pic hov-img0">
                                <img src="{{ $rpImg }}" alt="{{ $rpName }}">

                                <a href="{{ route('products.show', $rp->slug) }}"
                                    class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                                    View
                                </a>
                            </div>

                            <div class="block2-txt flex-w flex-t p-t-14">
                                <div class="block2-txt-child1 flex-col-l ">
                                    <a href="{{ route('products.show', $rp->slug) }}"
                                        class="stext-104 cl4 hov-cl1 trans-04 p-b-6">
                                        {{ $rpName }}
                                    </a>

                                    <span class="stext-105 cl3">
                                        {{ number_format((float) $rp->base_price, 2) }} {{ $rp->currency }}
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

        const productId = {{ (int) $product->id }};
        const currency = @json($product->currency ?? 'SAR');

        function getVariantId() {
            const v = document.getElementById('variantInput');
            return v && v.value ? v.value : '';
        }

        async function runCouponPreview() {
            const code = (couponInput?.value || '').trim();

            if (!code) {
                discountRow.style.display = 'none';
                couponMsg.style.display = 'none';
                return;
            }

            const payload = new FormData();
            payload.append('_token', @json(csrf_token()));
            payload.append('product_id', productId);
            payload.append('variant_id', getVariantId());
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

        window.previewCoupon = runCouponPreview;

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

        const sizeSelect = document.getElementById('sizeSelect');
        const colorSelect = document.getElementById('colorSelect');

        [sizeSelect, colorSelect].forEach(el => {
            if (el) {
                el.addEventListener('change', () => setTimeout(runCouponPreview, 80));
            }
        });
    });
</script>

@endsection
