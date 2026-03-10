@extends('site.app.layout')

@section('content')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<section class="bg0 p-b-140" style="padding-top:50px;">
    <div class="container">

        <div class="p-b-10">
            <h3 class="ltext-103 cl5">
                <br>
                <br>
                Product Overview
            </h3>
        </div>

        <div class="flex-w flex-sb-m p-b-52">

            {{-- ======= Category Tabs (Server Side) ======= --}}
            <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                <a class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 {{ request('category') ? '' : 'how-active1' }}"
                   href="{{ url()->current() }}">
                    All Products
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
                    Filter
                </div>

                <div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search">
                    <i class="icon-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
                    <i class="icon-close-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                    Search
                </div>
            </div>

            {{-- ======= Search (Real) ======= --}}
            <div class="dis-none panel-search w-full p-t-10 p-b-15">
                <form class="bor8 dis-flex p-l-15" method="GET" action="{{ url()->current() }}">

                    @foreach(request()->except(['q','page']) as $k => $v)
                        @if($v !== null && $v !== '')
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach

                    <button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04" type="submit">
                        <i class="zmdi zmdi-search"></i>
                    </button>

                    <input class="mtext-107 cl2 size-114 plh2 p-r-15"
                           type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Search products...">
                </form>
            </div>

            {{-- ======= Filter Panel (Real) ======= --}}
            <div class="dis-none panel-filter w-full p-t-10">
                <div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm">

                    {{-- SORT --}}
                    <div class="filter-col1 p-r-15 p-b-27">
                        <div class="mtext-102 cl2 p-b-15">Sort By</div>
                        <ul>
                            <li class="p-b-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'new', 'page' => 1]) }}"
                                   class="filter-link stext-106 trans-04 {{ request('sort','new') === 'new' ? 'filter-link-active' : '' }}">
                                    Newness
                                </a>
                            </li>

                            <li class="p-b-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc', 'page' => 1]) }}"
                                   class="filter-link stext-106 trans-04 {{ request('sort') === 'price_asc' ? 'filter-link-active' : '' }}">
                                    Price: Low to High
                                </a>
                            </li>

                            <li class="p-b-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc', 'page' => 1]) }}"
                                   class="filter-link stext-106 trans-04 {{ request('sort') === 'price_desc' ? 'filter-link-active' : '' }}">
                                    Price: High to Low
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- PRICE --}}
                    <div class="filter-col2 p-r-15 p-b-27">
                        <div class="mtext-102 cl2 p-b-15">Price</div>
                        <ul>
                            @foreach($priceRanges as $range)
                                @php
                                    $isActive =
                                        ($range['min'] === null && $range['max'] === null && !request()->hasAny(['min','max']))
                                        || ((string)request('min') === (string)$range['min'] && (string)request('max') === (string)$range['max']);
                                @endphp

                                <li class="p-b-6">
                                    <a href="{{ request()->fullUrlWithQuery(['min' => $range['min'], 'max' => $range['max'], 'page' => 1]) }}"
                                       class="filter-link stext-106 trans-04 {{ $isActive ? 'filter-link-active' : '' }}">
                                        {{ $range['label'] }}
                                    </a>
                                </li>
                            @endforeach

                            <li class="p-b-6">
                                <a href="{{ request()->fullUrlWithQuery(['min' => null, 'max' => null, 'page' => 1]) }}"
                                   class="filter-link stext-106 trans-04">
                                    Reset Price
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- COLOR --}}
                    <div class="filter-col3 p-r-15 p-b-27">
                        <div class="mtext-102 cl2 p-b-15">Color</div>
                        <ul>
                            <li class="p-b-6">
                                <a href="{{ request()->fullUrlWithQuery(['color' => null, 'page' => 1]) }}"
                                   class="filter-link stext-106 trans-04 {{ request('color') ? '' : 'filter-link-active' }}">
                                    All
                                </a>
                            </li>

                            @foreach($colorValues as $val)
                                <li class="p-b-6">
                                    <a href="{{ request()->fullUrlWithQuery(['color' => $val->id, 'page' => 1]) }}"
                                       class="filter-link stext-106 trans-04 {{ (string)request('color') === (string)$val->id ? 'filter-link-active' : '' }}">
                                        {{ $val->value }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- TAGS --}}
                    <div class="filter-col4 p-b-27">
                        <div class="mtext-102 cl2 p-b-15">Tags</div>

                        <div class="flex-w p-t-4 m-r--5">
                            <a href="{{ request()->fullUrlWithQuery(['tag' => null, 'page' => 1]) }}"
                               class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5 {{ request('tag') ? '' : 'filter-link-active' }}">
                                All
                            </a>

                            @foreach($tagValues as $val)
                                <a href="{{ request()->fullUrlWithQuery(['tag' => $val->id, 'page' => 1]) }}"
                                   class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5 {{ (string)request('tag') === (string)$val->id ? 'filter-link-active' : '' }}">
                                    {{ $val->value }}
                                </a>
                            @endforeach
                        </div>

                        <div class="p-t-20">
                            <a href="{{ url()->current() }}" class="stext-106 cl6 hov1 trans-04">
                                Reset All Filters
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ======= Products Grid (Dynamic) ======= --}}
        <div class="row isotope-grid">
            @forelse($products as $product)
                @php
                    $img = $product->primaryImage?->path
                        ? Storage::url($product->primaryImage->path)
                        : asset('cozastore/images/product-01.jpg');

                    $isInWishlist = in_array($product->id, $wishlistProductIds ?? []);
                @endphp

                <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item">
                    <div class="block2">
                        <div class="block2-pic hov-img0">
                            <img src="{{ $img }}" alt="{{ $product->name ?? $product->slug }}">

                            <a href="{{ url('/products/'.$product->slug) }}"
                               class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                                View
                            </a>
                        </div>

                        <div class="block2-txt flex-w flex-t p-t-14">
                            <div class="block2-txt-child1 flex-col-l">
                                <a href="{{ url('/products/'.$product->slug) }}"
                                   class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                    {{ $product->name ?? $product->slug }}
                                </a>

                                <span class="stext-105 cl3">
                                    {{ number_format((float)$product->base_price, 2) }} {{ $product->currency }}
                                </span>

                                {{-- لو بعدين جبتي average rating / reviews count من الكنترولر --}}
                                @if(isset($product->reviews_avg_rating) || isset($product->reviews_count))
                                    <span class="stext-105 cl3 p-t-4">
                                        ⭐ {{ number_format((float)($product->reviews_avg_rating ?? 0), 1) }}
                                        ({{ (int)($product->reviews_count ?? 0) }})
                                    </span>
                                @endif
                            </div>

                            <div class="block2-txt-child2 flex-r p-t-3">
                                @auth
                                    <form action="{{ route('wishlist.toggle') }}" method="POST" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="variant_id" value="">

                                        <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;">
                                            @if($isInWishlist)
                                                <img
                                                    src="{{ asset('cozastore/images/icons/icon-heart-02.png') }}"
                                                    alt="Wishlist"
                                                    class="dis-block trans-04">
                                            @else
                                                <img
                                                    src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}"
                                                    alt="Wishlist"
                                                    class="dis-block trans-04">
                                            @endif
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}">
                                        <img
                                            src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}"
                                            alt="Wishlist"
                                            class="dis-block trans-04">
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 p-t-30">
                    <div class="alert alert-warning text-center">
                        No products found for the selected filters.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- ======= Pagination بدل Load More ======= --}}
        @if($products->hasPages())
            <div class="flex-c-m flex-w w-full p-t-45">
                {{ $products->links() }}
            </div>
        @endif

    </div>
</section>

@endsection
