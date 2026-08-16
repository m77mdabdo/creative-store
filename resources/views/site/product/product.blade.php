@extends('site.app.layout')

@section('title', __('site.shop'))
@section('meta_description', __('site.seo_description'))

@section('content')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-product-product.css') }}">

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
                                        {{ $val->label }}
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

        <div class="row products-grid">
            @forelse($products as $product)
                @php
                    $img = $product->primaryImage?->path
                        ? media_url($product->primaryImage->path)
                        : asset('cozastore/images/product-01.jpg');
                @endphp

                <div class="col-6 col-md-4 col-lg-3 p-b-30">
                    <div class="product-card">
                        <div class="product-card-media">
                            <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy" decoding="async">

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
                                {{ money($product->base_price, $product->currency) }}
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
