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

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-home-1.css') }}">










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
                                $imgUrl = media_url($image);
                            }
                        @endphp

                        <li class="header-cart-item flex-w flex-t m-b-12">
                            <div class="header-cart-item-img">
                                <form action="{{ route('cart.remove', $key) }}" method="POST" onsubmit="return confirm('{{ __('site.remove_item_confirm') }}')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" style="all:unset;cursor:pointer;">
                                        <img src="{{ $imgUrl }}" alt="{{ $name }}" loading="lazy" decoding="async">
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

    <link rel="stylesheet" href="{{ versioned_asset('css/site/home-sections.css') }}">

    @include('site.home._trust')
    @include('site.home._categories')

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-home-2.css') }}">

<section class="products-page">
    <div class="container">

        <div class="products-heading">
            <h3 class="ltext-103">
                {{ __('site.product_overview') }}
            </h3>

            <p class="products-subtitle">
                {{ __('site.browse_our_latest_products') }}
            </p>
        </div>

        <div class="products-toolbar">
            <div class="products-toolbar-row">

                <div class="products-categories">
                    <a class="{{ request('category') ? '' : 'how-active1' }}"
                       href="{{ url()->current() }}">
                        {{ __('site.all_products') }}
                    </a>

                    @foreach ($categories as $cat)
                        <a class="{{ request('category') === $cat->slug ? 'how-active1' : '' }}"
                           href="{{ request()->fullUrlWithQuery(['category' => $cat->slug, 'page' => 1]) }}">
                            {{ $cat->name ?? $cat->slug }}
                        </a>
                    @endforeach
                </div>

                <div class="products-actions">
                    <button type="button" class="products-action-btn js-show-filter">
                        <i class="zmdi zmdi-filter-list"></i>
                        <span>{{ __('site.filter') }}</span>
                    </button>

                    <button type="button" class="products-action-btn js-show-search">
                        <i class="zmdi zmdi-search"></i>
                        <span>{{ __('site.search') }}</span>
                    </button>
                </div>
            </div>

            <div class="dis-none panel-search products-search-panel">
                <form class="products-search-form" method="GET" action="{{ url()->current() }}">
                    @foreach (request()->except(['q', 'page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach


                    <button type="submit">
                        <i class="zmdi zmdi-search"></i>
                    </button>

                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="{{ __('site.search_products') }}">
                </form>
            </div>

            <div class="dis-none panel-filter products-filter-panel">
                <div class="products-filter-grid">

                    <div class="filter-col1">
                        <div class="filter-box-title">{{ __('site.sort_by') }}</div>
                        <ul class="filter-link-list">
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'new', 'page' => 1]) }}"
                                   class="filter-link {{ request('sort', 'new') === 'new' ? 'filter-link-active' : '' }}">
                                    {{ __('site.newness') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc', 'page' => 1]) }}"
                                   class="filter-link {{ request('sort') === 'price_asc' ? 'filter-link-active' : '' }}">
                                    {{ __('site.price_low_high') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc', 'page' => 1]) }}"
                                   class="filter-link {{ request('sort') === 'price_desc' ? 'filter-link-active' : '' }}">
                                    {{ __('site.price_high_low') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="filter-col2">
                        <div class="filter-box-title">{{ __('site.price') }}</div>
                        <ul class="filter-link-list">
                            @foreach ($priceRanges as $range)
                                @php
                                    $isActive =
                                        ($range['min'] === null && $range['max'] === null && !request()->hasAny(['min', 'max']))
                                        || ((string) request('min') === (string) $range['min']
                                        && (string) request('max') === (string) $range['max']);
                                @endphp

                                <li>
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'min'  => $range['min'],
                                        'max'  => $range['max'],
                                        'page' => 1
                                    ]) }}"
                                       class="filter-link {{ $isActive ? 'filter-link-active' : '' }}">
                                        {{ $range['label'] }}
                                    </a>
                                </li>
                            @endforeach

                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['min' => null, 'max' => null, 'page' => 1]) }}"
                                   class="filter-link">
                                    {{ __('site.reset_price') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="filter-col3">
                        <div class="filter-box-title">{{ __('site.color') }}</div>
                        <ul class="filter-link-list">
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['color' => null, 'page' => 1]) }}"
                                   class="filter-link {{ request('color') ? '' : 'filter-link-active' }}">
                                    {{ __('site.all') }}
                                </a>
                            </li>

                            @foreach ($colorValues as $val)
                                <li>
                                    <a href="{{ request()->fullUrlWithQuery(['color' => $val->id, 'page' => 1]) }}"
                                       class="filter-link {{ (string) request('color') === (string) $val->id ? 'filter-link-active' : '' }}">
                                        {{ $val->label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="filter-col4">
                        <div class="filter-box-title">{{ __('site.tags') }}</div>

                        <div class="products-tags">
                            <a href="{{ request()->fullUrlWithQuery(['tag' => null, 'page' => 1]) }}"
                               class="tag {{ request('tag') ? '' : 'filter-link-active' }}">
                                {{ __('site.all') }}
                            </a>

                            @foreach ($tagValues as $val)
                                <a href="{{ request()->fullUrlWithQuery(['tag' => $val->id, 'page' => 1]) }}"
                                   class="tag {{ (string) request('tag') === (string) $val->id ? 'filter-link-active' : '' }}">
                                    {{ $val->label }}
                                </a>
                            @endforeach
                        </div>

                        <a href="{{ url()->current() }}" class="products-reset-link">
                            {{ __('site.reset_all_filters') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>

        @if ($products->count())
            <div class="wrap-slick2">
                <div class="slick2">
                    @foreach ($products as $product)
                        @php
                            $img = $product->primaryImage?->path
                                ? media_url($product->primaryImage->path)
                                : asset('cozastore/images/product-01.jpg');

                            $wishlistExists = isset($wishlistedProducts[$product->id]);
                        @endphp

                        <div class="item-slick2 p-l-15 p-r-15 p-b-30">
                            <div class="product-card">
                                <div class="product-card-media">
                                    <img src="{{ $img }}" alt="{{ $product->display_name }}" loading="lazy" decoding="async" width="600" height="750">

                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="product-card-btn">
                                        {{ __('site.view') }}
                                    </a>
                                </div>

                                <div class="product-card-body">
                                    <div class="product-card-content">
                                        <a href="{{ route('products.show', $product->slug) }}"
                                           class="product-card-title">
                                            {{ $product->name ?? $product->slug }}
                                        </a>

                                        <span class="product-card-price">
                                            {{ money((float) $product->base_price, $product->currency) }}
                                        </span>
                                    </div>

                                    <div class="product-card-side">
                                        @auth
                                            <form action="{{ route('wishlist.toggle') }}" method="POST" style="margin:0">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="variant_id" value="">

                                                <button type="submit" style="background:none;border:none;padding:0;cursor:pointer">
                                                    @if ($wishlistExists)
                                                        <img src="{{ asset('cozastore/images/icons/icon-heart-02.png') }}" style="width:20px" alt="wishlist" loading="lazy" decoding="async">
                                                    @else
                                                        <img src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" style="width:20px" alt="wishlist" loading="lazy" decoding="async">
                                                    @endif
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('login') }}">
                                                <img src="{{ asset('cozastore/images/icons/icon-heart-01.png') }}" style="width:20px" alt="wishlist" loading="lazy" decoding="async">
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="wrap-slick2-dots"></div>
            </div>
        @else
            <div class="products-empty">
                {{ __('site.no_products_found') }}
            </div>
        @endif

        {{-- @if (method_exists($products, 'links'))
            <div class="products-pagination text-center">
                {{ $products->links() }}
            </div>
        @endif --}}

    </div>
</section>

@include('site.home._featured')


<link rel="stylesheet" href="{{ versioned_asset('css/site/site-home-3.css') }}">

<section class="home-blog-section">
    <div class="container">
        <div class="home-blog-header">
            <div>
                <span class="home-blog-kicker">{{ __('site.blog') }}</span>
                <h2 class="home-blog-heading">{{ __('site.from_our_blog') }}</h2>
            </div>

            <a href="{{ route('blog.index') }}" class="home-blog-view-all">
                {{ __('site.view_all') }}
                <i class="fa fa-long-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
            </a>
        </div>

        @if(isset($blogs) && $blogs->count())
            <div class="wrap-slick-blog">
                <div class="slick-blog">
                    @foreach($blogs as $blog)
                        <div class="item-slick-blog">
                            <article class="blog-card">
                                <a href="{{ route('blog.show', $blog->slug) }}" class="blog-card-image-link">
                                    <img
                                        src="{{ $blog->image_url }}"
                                        alt="{{ $blog->localized_title }}"
                                        class="blog-card-image" loading="lazy" decoding="async">

                                    <span class="blog-date-badge">
                                        {{ optional($blog->published_at ?? $blog->created_at)->translatedFormat('d M') }}
                                    </span>
                                </a>

                                <div class="blog-content">
                                    <div class="blog-meta">
                                        @if(!empty($blog->category?->name))
                                            <span>{{ $blog->category->localized_name }}</span>
                                        @endif

                                        <span>{{ optional($blog->published_at ?? $blog->created_at)->format('Y') }}</span>
                                    </div>

                                    <h4 class="blog-title">
                                        <a href="{{ route('blog.show', $blog->slug) }}">
                                            {{ $blog->localized_title }}
                                        </a>
                                    </h4>

                                    <p class="blog-excerpt">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($blog->localized_excerpt ?? $blog->content ?? ''), 110) }}
                                    </p>

                                    <a href="{{ route('blog.show', $blog->slug) }}" class="blog-read-more">
                                        {{ __('site.continue_reading') }}
                                        <i class="fa fa-long-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <div class="wrap-slick-blog-dots"></div>
            </div>
        @else
            <div class="home-blog-empty">
                {{ __('site.no_blog_posts') }}
            </div>
        @endif

    </div>
</section>



@include('site.home._newsletter')

@endsection



