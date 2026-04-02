@extends('site.app.layout')

@section('content')

    @php
        use Illuminate\Support\Facades\Storage;

        $cart = session()->get('cart', []);
        $items = collect($cart);

        $cartTotal = $items->sum(function ($item) {
            return (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0);
        });
    @endphp

    <div class="wrap-header-cart js-panel-cart">
        <div class="s-full js-hide-cart"></div>

        <div class="header-cart flex-col-l p-l-65 p-r-25">
            <div class="header-cart-title flex-w flex-sb-m p-b-8">
                <span class="mtext-103 cl2">
                    {{ __('site.your_cart') }} ({{ $items->sum('quantity') }})
                </span>

                <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                    <i class="zmdi zmdi-close"></i>
                </div>
            </div>

            <div class="header-cart-content flex-w js-pscroll">
                <ul class="header-cart-wrapitem w-full">
                    @forelse($items as $key => $item)
                        @php
                            $name = $item['name'] ?? __('site.product');
                            $qty = (int) ($item['quantity'] ?? 1);
                            $price = (float) ($item['price'] ?? 0);
                            $image = $item['image'] ?? null;

                            if (! $image) {
                                $imgUrl = asset('cozastore/images/item-cart-01.jpg');
                            } elseif (str_starts_with($image, 'http')) {
                                $imgUrl = $image;
                            } else {
                                $imgUrl = Storage::url($image);
                            }
                        @endphp

                        <li class="header-cart-item flex-w flex-t m-b-12">
                            <div class="header-cart-item-img">
                                <form action="{{ route('cart.remove', $key) }}" method="POST"
                                      onsubmit="return confirm('{{ __('site.remove_item_confirm') }}')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" style="all:unset;cursor:pointer;">
                                        <img src="{{ $imgUrl }}" alt="{{ $name }}">
                                    </button>
                                </form>
                            </div>

                            <div class="header-cart-item-txt p-t-8">
                                <span class="header-cart-item-name m-b-18">
                                    {{ $name }}
                                </span>

                                <span class="header-cart-item-info">
                                    {{ $qty }} x {{ number_format($price, 2) }}
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="p-t-20 p-b-20">
                            <p class="stext-102 cl3">{{ __('site.your_cart_empty') }}</p>
                        </li>
                    @endforelse
                </ul>

                <div class="w-full">
                    <div class="header-cart-total w-full p-tb-40">
                        {{ __('site.total') }}: {{ number_format($cartTotal, 2) }}
                    </div>

                    <div class="header-cart-buttons flex-w w-full">
                        <a href="{{ route('cart.index') }}"
                           class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                            {{ __('site.view_cart') }}
                        </a>

                        <a href="{{ route('checkout.index') }}"
                           class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                            {{ __('site.checkout') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="section-slide">
        <div class="wrap-slick1">
            <div class="slick1">
                <div class="item-slick1" style="background-image: url('{{ asset('cozastore/images/slide-01.jpg') }}');">
                    <div class="container h-full">
                        <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                            <div class="layer-slick1 animated visible-false" data-appear="fadeInDown" data-delay="0">
                                <span class="ltext-101 cl2 respon2">{{ __('site.women_collection_2026') }}</span>
                            </div>

                            <div class="layer-slick1 animated visible-false" data-appear="fadeInUp" data-delay="800">
                                <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">{{ __('site.new_season') }}</h2>
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

                <div class="item-slick1" style="background-image: url('{{ asset('cozastore/images/slide-02.jpg') }}');">
                    <div class="container h-full">
                        <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                            <div class="layer-slick1 animated visible-false" data-appear="rollIn" data-delay="0">
                                <span class="ltext-101 cl2 respon2">{{ __('site.men_new_season') }}</span>
                            </div>

                            <div class="layer-slick1 animated visible-false" data-appear="lightSpeedIn" data-delay="800">
                                <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">{{ __('site.jackets_coats') }}</h2>
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

                <div class="item-slick1" style="background-image: url('{{ asset('cozastore/images/slide-03.jpg') }}');">
                    <div class="container h-full">
                        <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                            <div class="layer-slick1 animated visible-false" data-appear="rotateInDownLeft" data-delay="0">
                                <span class="ltext-101 cl2 respon2">{{ __('site.men_collection_2026') }}</span>
                            </div>

                            <div class="layer-slick1 animated visible-false" data-appear="rotateInUpRight" data-delay="800">
                                <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">{{ __('site.new_arrivals') }}</h2>
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

    <div class="sec-banner bg0 p-t-80 p-b-50">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-xl-4 p-b-30 m-lr-auto">
                    <div class="block1 wrap-pic-w">
                        <img src="{{ asset('cozastore/images/banner-01.jpg') }}" alt="IMG-BANNER">

                        <a href="{{ route('products.index') }}"
                           class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
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

                        <a href="{{ route('products.index') }}"
                           class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
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

                        <a href="{{ route('products.index') }}"
                           class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
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

    <section class="bg0 p-t-23 p-b-140">
        <div class="container">
            <div class="p-b-10">
                <h3 class="ltext-103 cl5">{{ __('site.product_overview') }}</h3>
            </div>

            <div class="flex-w flex-sb-m p-b-52">
                <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                    <a class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 {{ request('category') ? '' : 'how-active1' }}"
                       href="{{ url()->current() }}">
                        {{ __('site.all_products') }}
                    </a>

                    @foreach($categories as $cat)
                        <a class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 {{ request('category') === $cat->slug ? 'how-active1' : '' }}"
                           href="{{ request()->fullUrlWithQuery(['category' => $cat->slug, 'page' => 1]) }}">
                            {{ $cat->name ?? $cat->slug }}
                        </a>
                    @endforeach
                </div>

                <div class="flex-w flex-c-m m-tb-10">
                    <div class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4 js-show-filter">
                        <i class="icon-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
                        <i class="icon-close-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                        {{ __('site.filter') }}
                    </div>

                    <div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search">
                        <i class="icon-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
                        <i class="icon-close-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                        {{ __('site.search') }}
                    </div>
                </div>

                <div class="dis-none panel-search w-full p-t-10 p-b-15">
                    <form class="bor8 dis-flex p-l-15" method="GET" action="{{ url()->current() }}">
                        @foreach(request()->except(['q', 'page']) as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach

                        <button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04" type="submit">
                            <i class="zmdi zmdi-search"></i>
                        </button>

                        <input class="mtext-107 cl2 size-114 plh2 p-r-15"
                               type="text"
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="{{ __('site.search_products') }}">
                    </form>
                </div>

                <div class="dis-none panel-filter w-full p-t-10">
                    <div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm">
                        <div class="filter-col1 p-r-15 p-b-27">
                            <div class="mtext-102 cl2 p-b-15">{{ __('site.sort_by') }}</div>
                            <ul>
                                <li class="p-b-6">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'new', 'page' => 1]) }}"
                                       class="filter-link stext-106 trans-04 {{ request('sort', 'new') === 'new' ? 'filter-link-active' : '' }}">
                                        {{ __('site.newness') }}
                                    </a>
                                </li>

                                <li class="p-b-6">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc', 'page' => 1]) }}"
                                       class="filter-link stext-106 trans-04 {{ request('sort') === 'price_asc' ? 'filter-link-active' : '' }}">
                                        {{ __('site.price_low_high') }}
                                    </a>
                                </li>

                                <li class="p-b-6">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc', 'page' => 1]) }}"
                                       class="filter-link stext-106 trans-04 {{ request('sort') === 'price_desc' ? 'filter-link-active' : '' }}">
                                        {{ __('site.price_high_low') }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="filter-col2 p-r-15 p-b-27">
                            <div class="mtext-102 cl2 p-b-15">{{ __('site.price') }}</div>
                            <ul>
                                @foreach($priceRanges as $range)
                                    @php
                                        $isActive =
                                            ($range['min'] === null && $range['max'] === null && !request()->hasAny(['min', 'max']))
                                            || ((string) request('min') === (string) $range['min'] && (string) request('max') === (string) $range['max']);
                                    @endphp

                                    <li class="p-b-6">
                                        <a href="{{ request()->fullUrlWithQuery([
                                            'min' => $range['min'],
                                            'max' => $range['max'],
                                            'page' => 1
                                        ]) }}"
                                           class="filter-link stext-106 trans-04 {{ $isActive ? 'filter-link-active' : '' }}">
                                            {{ $range['label'] }}
                                        </a>
                                    </li>
                                @endforeach

                                <li class="p-b-6">
                                    <a href="{{ request()->fullUrlWithQuery(['min' => null, 'max' => null, 'page' => 1]) }}"
                                       class="filter-link stext-106 trans-04">
                                        {{ __('site.reset_price') }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="filter-col3 p-r-15 p-b-27">
                            <div class="mtext-102 cl2 p-b-15">{{ __('site.color') }}</div>
                            <ul>
                                <li class="p-b-6">
                                    <a href="{{ request()->fullUrlWithQuery(['color' => null, 'page' => 1]) }}"
                                       class="filter-link stext-106 trans-04 {{ request('color') ? '' : 'filter-link-active' }}">
                                        {{ __('site.all') }}
                                    </a>
                                </li>

                                @foreach($colorValues as $val)
                                    <li class="p-b-6">
                                        <a href="{{ request()->fullUrlWithQuery(['color' => $val->id, 'page' => 1]) }}"
                                           class="filter-link stext-106 trans-04 {{ (string) request('color') === (string) $val->id ? 'filter-link-active' : '' }}">
                                            {{ $val->value }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="filter-col4 p-b-27">
                            <div class="mtext-102 cl2 p-b-15">{{ __('site.tags') }}</div>

                            <div class="flex-w p-t-4 m-r--5">
                                <a href="{{ request()->fullUrlWithQuery(['tag' => null, 'page' => 1]) }}"
                                   class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5 {{ request('tag') ? '' : 'filter-link-active' }}">
                                    {{ __('site.all') }}
                                </a>

                                @foreach($tagValues as $val)
                                    <a href="{{ request()->fullUrlWithQuery(['tag' => $val->id, 'page' => 1]) }}"
                                       class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5 {{ (string) request('tag') === (string) $val->id ? 'filter-link-active' : '' }}">
                                        {{ $val->value }}
                                    </a>
                                @endforeach
                            </div>

                            <div class="p-t-20">
                                <a href="{{ url()->current() }}" class="stext-106 cl6 hov1 trans-04">
                                    {{ __('site.reset_all_filters') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row isotope-grid">
                @forelse($products as $product)
                    @php
                        $img = $product->primaryImage?->path
                            ? Storage::url($product->primaryImage->path)
                            : asset('cozastore/images/product-01.jpg');

                        $wishlistExists = false;

                        if (auth()->check() && auth()->user()->wishlist) {
                            $wishlistExists = auth()->user()->wishlist->items()
                                ->where('product_id', $product->id)
                                ->whereNull('variant_id')
                                ->exists();
                        }
                    @endphp

                    <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item">
                        <div class="block2">
                            <div class="block2-pic hov-img0">
                                <img src="{{ $img }}" alt="{{ $product->name ?? $product->slug }}">

                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                                    {{ __('site.view') }}
                                </a>
                            </div>

                            <div class="block2-txt flex-w flex-t p-t-14">
                                <div class="block2-txt-child1 flex-col-l">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                        {{ $product->name ?? $product->slug }}
                                    </a>

                                    <span class="stext-105 cl3">
                                        {{ number_format((float) $product->base_price, 2) }} {{ $product->currency }}
                                    </span>
                                </div>

                                <div class="block2-txt-child2 flex-r p-t-3">
                                    @auth
                                        <form action="{{ route('wishlist.toggle') }}" method="POST" style="margin:0">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="variant_id" value="">

                                            <button type="submit" style="background:none;border:none;padding:0;cursor:pointer">
                                                @if($wishlistExists)
                                                    <img src="{{ asset('cozastore/images/icons/icon-heart-02.png') }}" class="trans-04" style="width:20px" alt="wishlist">
                                                @else
                                                    <img src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" class="trans-04" style="width:20px" alt="wishlist">
                                                @endif
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}">
                                            <img src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" style="width:20px" alt="wishlist">
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 p-t-30">
                        <p>{{ __('site.no_products_found') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="flex-c-m flex-w w-full p-t-45">
                {{ $products->links() }}
            </div>
        </div>
    </section>

@endsection
