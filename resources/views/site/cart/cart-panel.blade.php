@extends('site.app.layout')

@section('content')

<style>
    .remove-cart-btn {
        background: transparent;
        border: none;
        font-size: 22px;
        color: #e74c3c;
        cursor: pointer;
        transition: all .25s ease;
    }

    .remove-cart-btn:hover {
        color: #111;
        transform: scale(1.05);
    }

    .column-6 {
        width: 70px;
        text-align: center;
    }

    .cart-page-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
    }

    .cart-summary-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        background: #fff;
    }

    .cart-product-name {
        font-weight: 600;
        color: #222;
        text-decoration: none;
        transition: all .25s ease;
        display: inline-block;
    }

    .cart-product-name:hover {
        color: #717fe0;
    }

    .cart-meta {
        font-size: 13px;
        color: #888;
        margin-top: 6px;
        line-height: 1.9;
    }

    .cart-option-row {
        display: block;
        margin-top: 2px;
    }

    .coupon-box {
        border-radius: 14px;
        background: #fff;
    }

    .empty-cart-box {
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
    }

    .qty-btn {
        width: 45px;
        height: 45px;
        border: none;
        background: #f5f5f5;
        transition: all .25s ease;
    }

    .qty-btn:hover {
        background: #e9e9e9;
    }

    .num-product {
        border-left: 1px solid #e6e6e6 !important;
        border-right: 1px solid #e6e6e6 !important;
    }

    .cart-actions-row {
        gap: 12px;
    }

    .cart-card-head {
        border-bottom: 1px solid #eee;
        margin-bottom: 20px;
        padding-bottom: 18px;
    }

    .coupon-active-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        background: #f3f3f3;
        color: #333;
        font-size: 13px;
        font-weight: 500;
        margin-right: 8px;
        margin-bottom: 8px;
    }

    .wrap-table-shopping-cart {
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
    }

    .table-shopping-cart {
        background: #fff;
    }

    .table-shopping-cart .table_head th {
        background: #fafafa;
        font-weight: 700;
    }

    .table-shopping-cart .table_row td {
        vertical-align: middle;
    }

    .how-itemcart1 {
        border-radius: 12px;
        overflow: hidden;
        background: #f8f8f8;
    }

    .how-itemcart1 img {
        object-fit: cover;
    }

    html[dir="rtl"] .table-shopping-cart th,
    html[dir="rtl"] .table-shopping-cart td {
        text-align: right;
    }

    html[dir="rtl"] .cart-product-name,
    html[dir="rtl"] .cart-meta,
    html[dir="rtl"] .cart-card-head,
    html[dir="rtl"] .cart-summary-card,
    html[dir="rtl"] .empty-cart-box {
        text-align: right;
    }

    html[dir="rtl"] .column-1,
    html[dir="rtl"] .column-4,
    html[dir="rtl"] .column-5,
    html[dir="rtl"] .column-6 {
        text-align: center;
    }

    html[dir="rtl"] .coupon-active-badge {
        margin-right: 0;
        margin-left: 8px;
    }

    html[dir="rtl"] .wrap-num-product {
        margin-left: 10px !important;
        margin-right: auto !important;
    }

    @media (max-width: 991px) {
        .cart-page-title {
            align-items: flex-start;
        }

        .wrap-table-shopping-cart {
            overflow-x: auto;
        }
    }
</style>

@php
    use Illuminate\Support\Facades\Storage;

    $items = collect($cart ?? []);
    $currency = $items->first()['currency'] ?? 'SAR';
@endphp

<section class="bg0 p-t-75 p-b-85">
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success m-b-20">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger m-b-20">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger m-b-20">
                <ul class="m-b-0 p-l-20">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="cart-page-title p-b-30">
            <div>
                <h3 class="ltext-103 cl5 m-b-5">{{ __('site.your_cart') }}</h3>
                <p class="stext-115 cl6">
                    {{ __('site.review_cart_message') }}
                </p>
            </div>

            @if($items->count())
                <form action="{{ route('cart.clear') }}" method="POST"
                      onsubmit="return confirm('{{ __('site.clear_cart_confirm') }}')">
                    @csrf
                    <button type="submit" class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04">
                        {{ __('site.clear_cart') }}
                    </button>
                </form>
            @endif
        </div>

        @if($items->count())
            <div class="row">
                <div class="col-lg-8 m-lr-auto m-b-50">
                    <div class="m-l-25 m-r--38 m-lr-0-xl">

                        <div class="wrap-table-shopping-cart bor10">
                            <table class="table-shopping-cart">
                                <tr class="table_head">
                                    <th class="column-1">{{ __('site.product') }}</th>
                                    <th class="column-2">{{ __('site.details') }}</th>
                                    <th class="column-3">{{ __('site.price') }}</th>
                                    <th class="column-4">{{ __('site.quantity') }}</th>
                                    <th class="column-5">{{ __('site.total') }}</th>
                                    <th class="column-6">{{ __('site.remove') }}</th>
                                </tr>

                                @foreach($items as $key => $item)
                                    @php
                                        $name = $item['name'] ?? __('site.product');
                                        $qty = (int) ($item['quantity'] ?? 1);
                                        $price = (float) ($item['price'] ?? 0);
                                        $image = $item['image'] ?? null;
                                        $productSlug = $item['slug'] ?? null;
                                        $sku = $item['sku'] ?? null;
                                        $variantOptions = collect($item['variant_options'] ?? []);

                                        if (!$image) {
                                            $imgUrl = asset('cozastore/images/item-cart-01.jpg');
                                        } elseif (str_starts_with($image, 'http')) {
                                            $imgUrl = $image;
                                        } elseif (str_starts_with($image, '/')) {
                                            $imgUrl = $image;
                                        } else {
                                            $imgUrl = Storage::url($image);
                                        }

                                        $safeKey = md5((string) $key);

                                        $displayOptions = $variantOptions->reject(function ($option) use ($item) {
                                            $slug = strtolower(trim((string) ($option['slug'] ?? '')));
                                            $name = strtolower(trim((string) ($option['name'] ?? '')));

                                            $isColor = in_array($slug, ['color', 'colors'], true)
                                                || in_array($name, ['color', 'colors', 'اللون', 'الألوان'], true);

                                            $isSize = in_array($slug, ['size', 'sizes'], true)
                                                || in_array($name, ['size', 'sizes', 'المقاس', 'الحجم'], true);

                                            if ($isColor && !empty($item['color'])) {
                                                return true;
                                            }

                                            if ($isSize && !empty($item['size'])) {
                                                return true;
                                            }

                                            return false;
                                        })->values();
                                    @endphp

                                    <tr class="table_row">
                                        <td class="column-1">
                                            <div class="how-itemcart1">
                                                <img src="{{ $imgUrl }}" alt="{{ $name }}">
                                            </div>
                                        </td>

                                        <td class="column-2">
                                            @if($productSlug)
                                                <a href="{{ url('/products/' . $productSlug) }}" class="cart-product-name">
                                                    {{ $name }}
                                                </a>
                                            @else
                                                <span class="cart-product-name">{{ $name }}</span>
                                            @endif

                                            <div class="cart-meta">
                                                @if($sku)
                                                    <span class="cart-option-row">
                                                        {{ __('site.sku') }}: {{ $sku }}
                                                    </span>
                                                @endif

                                                @if(!empty($item['color']))
                                                    <span class="cart-option-row">
                                                        {{ __('site.color') }}: {{ $item['color'] }}
                                                    </span>
                                                @endif

                                                @if(!empty($item['size']))
                                                    <span class="cart-option-row">
                                                        {{ __('site.modal_size') }}: {{ $item['size'] }}
                                                    </span>
                                                @endif

                                                @if($displayOptions->count())
                                                    @foreach($displayOptions as $option)
                                                        <span class="cart-option-row">
                                                            {{ $option['name'] ?? __('site.option') }}:
                                                            {{ $option['value'] ?? '-' }}
                                                        </span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>

                                        <td class="column-3">
                                            {{ number_format($price, 2) }} {{ $currency }}
                                        </td>

                                        <td class="column-4">
                                            <form action="{{ route('cart.update') }}" method="POST" class="flex-w flex-m">
                                                @csrf
                                                <input type="hidden" name="key" value="{{ $key }}">

                                                <div class="wrap-num-product flex-w m-l-auto m-r-0 m-r-10">
                                                    <button type="button"
                                                        class="qty-btn cl8 hov-btn3 trans-04 flex-c-m"
                                                        onclick="changeQty('{{ $safeKey }}', -1)">
                                                        <i class="fs-16 zmdi zmdi-minus"></i>
                                                    </button>

                                                    <input id="qty-{{ $safeKey }}"
                                                        class="mtext-104 cl3 txt-center num-product"
                                                        type="number"
                                                        name="quantity"
                                                        value="{{ $qty }}"
                                                        min="1"
                                                        max="20">

                                                    <button type="button"
                                                        class="qty-btn cl8 hov-btn3 trans-04 flex-c-m"
                                                        onclick="changeQty('{{ $safeKey }}', 1)">
                                                        <i class="fs-16 zmdi zmdi-plus"></i>
                                                    </button>
                                                </div>

                                                <button type="submit"
                                                    class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer"
                                                    style="height: 45px;">
                                                    {{ __('site.update') }}
                                                </button>
                                            </form>
                                        </td>

                                        <td class="column-5">
                                            {{ number_format($price * $qty, 2) }} {{ $currency }}
                                        </td>

                                        <td class="column-6">
                                            <form action="{{ route('cart.remove', ['key' => $key]) }}" method="POST"
                                                onsubmit="return confirm('{{ __('site.remove_item_confirm') }}')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="remove-cart-btn" title="{{ __('site.remove') }}">
                                                    <i class="zmdi zmdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="bor15 coupon-box p-t-20 p-b-20 p-lr-30 p-lr-15-sm m-t-25">
                            <div class="cart-card-head">
                                <h4 class="mtext-109 cl2">{{ __('site.coupon_actions') }}</h4>
                            </div>

                            <div class="flex-w flex-sb-m cart-actions-row">
                                <div class="flex-w flex-m m-tb-10">
                                    <form action="{{ route('cart.coupon') }}" method="POST" class="flex-w flex-m">
                                        @csrf

                                        <input class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5"
                                            type="text"
                                            name="coupon"
                                            value="{{ $cartCouponData['code'] ?? '' }}"
                                            placeholder="{{ __('site.cart_coupon_code') }}">

                                        <button type="submit"
                                            class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
                                            {{ __('site.apply_cart_coupon') }}
                                        </button>
                                    </form>

                                    @if($cartCouponData)
                                        <form action="{{ route('cart.coupon.remove') }}" method="POST" class="m-l-10 m-tb-5">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer">
                                                {{ __('site.remove_cart_coupon') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="m-tb-10">
                                    <a href="{{ route('cart.index') }}"
                                        class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer">
                                        {{ __('site.refresh_cart') }}
                                    </a>
                                </div>
                            </div>

                            @if($cartCouponData || !empty($productCouponsData))
                                <div class="p-t-15">
                                    @if($cartCouponData)
                                        <span class="coupon-active-badge">
                                            {{ __('site.cart_coupon') }}: {{ $cartCouponData['code'] }}
                                        </span>
                                    @endif

                                    @if(!empty($productCouponsData))
                                        @foreach($productCouponsData as $coupon)
                                            <span class="coupon-active-badge">
                                                {{ __('site.product_coupon') }}: {{ $coupon['code'] }}
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="flex-w p-t-30">
                            <a href="{{ url('/products') }}"
                                class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04">
                                {{ __('site.continue_shopping') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-10 col-lg-4 col-xl-4 m-lr-auto m-b-50">
                    <div class="cart-summary-card bor10 p-lr-40 p-t-30 p-b-40 m-l-25 m-lr-0-xl">
                        <div class="cart-card-head">
                            <h4 class="mtext-109 cl2">{{ __('site.cart_totals') }}</h4>
                        </div>

                        <div class="flex-w flex-t p-b-16">
                            <div class="size-208">
                                <span class="stext-110 cl2">{{ __('site.subtotal') }}</span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl2">{{ number_format($subtotal, 2) }} {{ $currency }}</span>
                            </div>
                        </div>

                        @if(!empty($productCouponsData))
                            @foreach($productCouponsData as $coupon)
                                <div class="flex-w flex-t p-b-16">
                                    <div class="size-208">
                                        <span class="stext-110 cl2">{{ __('site.product_coupon') }}</span>
                                    </div>
                                    <div class="size-209">
                                        <span class="mtext-110 cl2">{{ $coupon['code'] }}</span>
                                    </div>
                                </div>
                            @endforeach

                            <div class="flex-w flex-t p-b-16">
                                <div class="size-208">
                                    <span class="stext-110 cl2">{{ __('site.product_discount') }}</span>
                                </div>
                                <div class="size-209">
                                    <span class="mtext-110 cl2">
                                        - {{ number_format($productDiscount, 2) }} {{ $currency }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        @if($cartCouponData)
                            <div class="flex-w flex-t p-b-16">
                                <div class="size-208">
                                    <span class="stext-110 cl2">{{ __('site.cart_coupon') }}</span>
                                </div>
                                <div class="size-209">
                                    <span class="mtext-110 cl2">{{ $cartCouponData['code'] }}</span>
                                </div>
                            </div>

                            <div class="flex-w flex-t p-b-16">
                                <div class="size-208">
                                    <span class="stext-110 cl2">{{ __('site.cart_discount') }}</span>
                                </div>
                                <div class="size-209">
                                    <span class="mtext-110 cl2">- {{ number_format($cartDiscount, 2) }} {{ $currency }}</span>
                                </div>
                            </div>
                        @endif

                        @if($discount > 0)
                            <div class="flex-w flex-t p-b-16">
                                <div class="size-208">
                                    <span class="stext-110 cl2">{{ __('site.total_discount') }}</span>
                                </div>
                                <div class="size-209">
                                    <span class="mtext-110 cl2">- {{ number_format($discount, 2) }} {{ $currency }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="flex-w flex-t p-b-16">
                            <div class="size-208">
                                <span class="stext-110 cl2">{{ __('site.net_total') }}</span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl2">{{ number_format($netTotal, 2) }} {{ $currency }}</span>
                            </div>
                        </div>

                        <div class="flex-w flex-t p-b-16">
                            <div class="size-208">
                                <span class="stext-110 cl2">{{ __('site.vat') }} (15%)</span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl2">{{ number_format($vatValue, 2) }} {{ $currency }}</span>
                            </div>
                        </div>

                        <div class="bor12 m-b-25"></div>

                        <div class="flex-w flex-t p-b-25">
                            <div class="size-208">
                                <span class="stext-110 cl2">{{ __('site.total') }}</span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl2">{{ number_format($grandTotal, 2) }} {{ $currency }}</span>
                            </div>
                        </div>

                        <a href="{{ route('shipping.index') }}"
                           class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer m-t-10">
                            {{ __('site.continue_to_shipping') }}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-cart-box text-center p-t-100 p-b-100 p-lr-30">
                <h4 class="mtext-109 cl2 p-b-15">{{ __('site.empty_cart_title') }}</h4>
                <p class="stext-115 cl6 p-b-25">
                    {{ __('site.empty_cart_desc') }}
                </p>

                <a href="{{ url('/products') }}"
                    class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer m-lr-auto"
                    style="display: inline-flex; width: auto;">
                    {{ __('site.continue_shopping') }}
                </a>
            </div>
        @endif

    </div>
</section>

<script>
    function changeQty(safeKey, delta) {
        const input = document.getElementById('qty-' + safeKey);
        if (!input) return;

        let val = parseInt(input.value || '1', 10);
        if (isNaN(val)) val = 1;

        val += delta;

        if (val < 1) val = 1;
        if (val > 20) val = 20;

        input.value = val;
    }
</script>

@endsection
