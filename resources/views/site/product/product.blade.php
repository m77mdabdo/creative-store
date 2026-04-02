@extends('site.app.layout')

@section('content')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<style>
    .products-page {
        padding-top: 55px;
        padding-bottom: 110px;
        background: #fff;
    }

    .products-toolbar {
        margin-bottom: 35px;
    }

    .products-heading {
        margin-bottom: 28px;
    }

    .products-heading .ltext-103 {
        font-size: 46px;
        line-height: 1.2;
        font-weight: 700;
        color: #222;
        margin-bottom: 10px;
    }

    .products-subtitle {
        font-size: 15px;
        color: #777;
        margin: 0;
    }

    .products-toolbar-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .products-categories {
        display: flex;
        align-items: center;
        gap: 10px 16px;
        flex-wrap: wrap;
    }

    .products-categories a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 16px;
        border-radius: 999px;
        text-decoration: none;
        color: #666;
        background: transparent;
        border: 1px solid transparent;
        transition: all .25s ease;
        font-size: 15px;
        font-weight: 500;
    }

    .products-categories a:hover {
        color: #717fe0;
        background: rgba(113,127,224,.08);
        border-color: rgba(113,127,224,.12);
    }

    .products-categories a.how-active1 {
        color: #717fe0 !important;
        background: rgba(113,127,224,.12);
        border-color: rgba(113,127,224,.14);
        font-weight: 700;
    }

    .products-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .products-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #e9e9e9;
        color: #444;
        cursor: pointer;
        transition: all .25s ease;
        box-shadow: 0 4px 14px rgba(0,0,0,.03);
    }

    .products-action-btn:hover {
        color: #717fe0;
        border-color: rgba(113,127,224,.2);
        background: rgba(113,127,224,.05);
    }

    .products-search-panel,
    .products-filter-panel {
        width: 100%;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #efefef;
        box-shadow: 0 12px 30px rgba(0,0,0,.05);
    }

    .products-search-panel {
        padding: 16px;
        margin-top: 12px;
    }

    .products-search-form {
        display: flex;
        align-items: center;
        min-height: 56px;
        border: 1px solid #ececec;
        border-radius: 14px;
        overflow: hidden;
        background: #fafafa;
    }

    .products-search-form button {
        width: 58px;
        height: 56px;
        border: 0;
        background: transparent;
        color: #333;
    }

    .products-search-form input {
        flex: 1;
        height: 56px;
        border: 0;
        background: transparent;
        color: #222;
        font-size: 15px;
        padding: 0 16px;
        outline: none;
    }

    .products-filter-panel {
        padding: 24px 26px 8px;
        margin-top: 14px;
    }

    .products-filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 24px;
    }

    .filter-box-title {
        font-size: 18px;
        font-weight: 700;
        color: #222;
        margin-bottom: 16px;
    }

    .filter-link-list,
    .filter-link-list li {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .filter-link-list li + li {
        margin-top: 10px;
    }

    .filter-link,
    .products-filter-panel .filter-col3 a,
    .products-filter-panel .filter-col4 a {
        color: #666;
        text-decoration: none;
        transition: all .25s ease;
        font-size: 14px;
    }

    .filter-link:hover,
    .products-filter-panel .filter-col3 a:hover,
    .products-filter-panel .filter-col4 a:hover {
        color: #717fe0;
    }

    .products-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .products-tags .tag {
        margin: 0;
        min-height: 36px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #f7f7f7;
        color: #555;
        text-decoration: none;
        transition: all .25s ease;
        border: 1px solid transparent;
    }

    .products-tags .tag:hover {
        color: #717fe0;
        background: rgba(113,127,224,.08);
        border-color: rgba(113,127,224,.12);
    }

    .products-reset-link {
        display: inline-block;
        margin-top: 14px;
        font-size: 14px;
        font-weight: 600;
        color: #717fe0;
        text-decoration: none;
    }

    .products-grid {
        margin-top: 26px;
    }

    .product-card {
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        border: 1px solid #efefef;
        box-shadow: 0 10px 28px rgba(0,0,0,.04);
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 35px rgba(0,0,0,.08);
        border-color: rgba(113,127,224,.14);
    }

    .product-card-media {
        position: relative;
        overflow: hidden;
        background: #f7f7f7;
        aspect-ratio: 4 / 5;
    }

    .product-card-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .35s ease;
    }

    .product-card:hover .product-card-media img {
        transform: scale(1.04);
    }

    .product-card-btn {
        position: absolute;
        left: 50%;
        bottom: 18px;
        transform: translateX(-50%) translateY(10px);
        min-width: 150px;
        min-height: 42px;
        border-radius: 999px;
        background: #222;
        color: #fff;
        text-decoration: none;
        opacity: 0;
        visibility: hidden;
        transition: all .25s ease;
        box-shadow: 0 10px 25px rgba(0,0,0,.18);
    }

    .product-card:hover .product-card-btn {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    .product-card-body {
        padding: 18px 16px 20px;
    }

    .product-card-title {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #222;
        text-decoration: none;
        line-height: 1.7;
        min-height: 52px;
        transition: all .25s ease;
        margin-bottom: 8px;
    }

    .product-card-title:hover {
        color: #717fe0;
    }

    .product-card-price {
        display: inline-block;
        font-size: 16px;
        font-weight: 700;
        color: #444;
    }

    .products-empty {
        border-radius: 18px;
        border: 1px solid #eee;
        background: #fff8e8;
        color: #8a6d3b;
        padding: 18px 22px;
    }

    .products-pagination {
        margin-top: 18px;
    }

    .products-pagination nav {
        display: flex;
        justify-content: center;
    }

    .products-pagination svg {
        width: 18px;
        height: 18px;
    }

    html[dir="rtl"] .products-heading,
    html[dir="rtl"] .products-subtitle,
    html[dir="rtl"] .products-filter-panel,
    html[dir="rtl"] .filter-box-title,
    html[dir="rtl"] .product-card-body {
        text-align: right;
    }

    html[dir="rtl"] .products-toolbar-row {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .products-categories {
        justify-content: flex-start;
    }

    html[dir="rtl"] .products-actions {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .products-search-form {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .products-search-form input {
        text-align: right;
    }

    html[dir="rtl"] .products-filter-grid {
        direction: rtl;
    }

    html[dir="rtl"] .filter-link-list,
    html[dir="rtl"] .products-tags {
        text-align: right;
    }

    @media (max-width: 1199px) {
        .products-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991px) {
        .products-heading .ltext-103 {
            font-size: 36px;
        }

        .products-toolbar-row {
            align-items: stretch;
        }

        .products-actions {
            width: 100%;
        }
    }

    @media (max-width: 767px) {
        .products-page {
            padding-top: 30px;
            padding-bottom: 80px;
        }

        .products-heading .ltext-103 {
            font-size: 30px;
        }

        .products-filter-grid {
            grid-template-columns: 1fr;
        }

        .products-categories,
        .products-actions {
            width: 100%;
        }

        .products-action-btn {
            width: 100%;
        }

        .product-card-btn {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }
    }
</style>

<section class="products-page">
    <div class="container">

        <div class="products-heading">
            <h3 class="ltext-103">
                {{ __('site.product_overview') }}
            </h3>
            <p class="products-subtitle">
                {{ __('site.search_products') }}
            </p>
        </div>

        <div class="products-toolbar">
            <div class="products-toolbar-row">

                <div class="products-categories">
                    <a class="{{ request('category') ? '' : 'how-active1' }}"
                       href="{{ url()->current() }}">
                        {{ __('site.all_products') }}
                    </a>

                    @foreach($categories as $cat)
                        <a class="{{ request('category') === $cat->slug ? 'how-active1' : '' }}"
                           href="{{ request()->fullUrlWithQuery(['category' => $cat->slug, 'page' => 1]) }}">
                            {{ $cat->name ?? $cat->slug }}
                        </a>
                    @endforeach
                </div>

                <div class="products-actions">
                    <div class="products-action-btn js-show-filter">
                        <i class="zmdi zmdi-filter-list"></i>
                        <span>{{ __('site.filter') }}</span>
                    </div>

                    <div class="products-action-btn js-show-search">
                        <i class="zmdi zmdi-search"></i>
                        <span>{{ __('site.search') }}</span>
                    </div>
                </div>
            </div>

            <div class="dis-none panel-search products-search-panel">
                <form class="products-search-form" method="GET" action="{{ url()->current() }}">
                    @foreach(request()->except(['q','page']) as $k => $v)
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
                                <a href="{{ request()->fullUrlWithQuery(['sort'=>'new']) }}" class="filter-link">
                                    {{ __('site.newness') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['sort'=>'price_asc']) }}" class="filter-link">
                                    {{ __('site.price_low_high') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['sort'=>'price_desc']) }}" class="filter-link">
                                    {{ __('site.price_high_low') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="filter-col2">
                        <div class="filter-box-title">{{ __('site.price') }}</div>
                        <ul class="filter-link-list">
                            @foreach($priceRanges as $range)
                                <li>
                                    <a href="{{ request()->fullUrlWithQuery(['min'=>$range['min'],'max'=>$range['max']]) }}"
                                       class="filter-link">
                                        {{ $range['label'] }}
                                    </a>
                                </li>
                            @endforeach

                            <li>
                                <a href="{{ url()->current() }}" class="filter-link">
                                    {{ __('site.reset_price') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="filter-col3">
                        <div class="filter-box-title">{{ __('site.color') }}</div>
                        <ul class="filter-link-list">
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['color'=>null]) }}">
                                    {{ __('site.all') }}
                                </a>
                            </li>

                            @foreach($colorValues as $val)
                                <li>
                                    <a href="{{ request()->fullUrlWithQuery(['color'=>$val->id]) }}">
                                        {{ $val->value }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="filter-col4">
                        <div class="filter-box-title">{{ __('site.tags') }}</div>

                        <div class="products-tags">
                            <a href="{{ request()->fullUrlWithQuery(['tag'=>null]) }}" class="tag">
                                {{ __('site.all') }}
                            </a>

                            @foreach($tagValues as $val)
                                <a href="{{ request()->fullUrlWithQuery(['tag'=>$val->id]) }}" class="tag">
                                    {{ $val->value }}
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

        <div class="row products-grid">
            @forelse($products as $product)
                @php
                    $img = $product->primaryImage?->path
                        ? Storage::url($product->primaryImage->path)
                        : asset('cozastore/images/product-01.jpg');
                @endphp

                <div class="col-sm-6 col-md-4 col-lg-3 p-b-30">
                    <div class="product-card">
                        <div class="product-card-media">
                            <img src="{{ $img }}" alt="{{ $product->name }}">

                            <a href="{{ route('products.show', $product->slug) }}"
                               class="product-card-btn flex-c-m">
                                {{ __('site.view') }}
                            </a>
                        </div>

                        <div class="product-card-body">
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="product-card-title">
                                {{ $product->name }}
                            </a>

                            <span class="product-card-price">
                                {{ number_format($product->base_price, 2) }} {{ $product->currency }}
                            </span>
                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="products-empty text-center">
                        {{ __('site.no_products_found') }}
                    </div>
                </div>
            @endforelse
        </div>

        <div class="products-pagination text-center">
            {{ $products->links() }}
        </div>

    </div>
</section>

@endsection
