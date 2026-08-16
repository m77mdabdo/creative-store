@if ($featuredProducts->isNotEmpty())
<section class="home-section home-featured">
    <div class="container">
        <header class="home-section-head">
            <div>
                <h2>{{ __('site.featured_products') }}</h2>
                <p>{{ __('site.featured_products_text') }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="home-section-link">
                {{ __('site.view_all') }} <i class="zmdi zmdi-long-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
            </a>
        </header>

        <div class="home-product-grid">
            @foreach ($featuredProducts as $product)
                <article class="home-product-card">
                    <a href="{{ route('products.show', $product->slug) }}" class="home-product-media">
                        <img src="{{ media_url($product->primaryImage?->path, asset('cozastore/images/product-01.jpg')) }}"
                             alt="{{ $product->display_name }}" loading="lazy" decoding="async" width="600" height="750">
                        @if ($product->is_featured)
                            <span class="home-product-badge">{{ __('site.featured_badge') }}</span>
                        @endif
                    </a>
                    <div class="home-product-body">
                        @if ($product->category_name)
                            <span class="home-product-category">{{ $product->category_name }}</span>
                        @endif
                        <a href="{{ route('products.show', $product->slug) }}" class="home-product-name">{{ $product->display_name }}</a>
                        <span class="home-product-price">{{ money($product->base_price) }}</span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
