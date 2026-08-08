@if ($categoryTiles->isNotEmpty())
<section class="home-section home-categories">
    <div class="container">
        <header class="home-section-head">
            <div>
                <h2>{{ __('site.shop_by_category') }}</h2>
                <p>{{ __('site.shop_by_category_text') }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="home-section-link">
                {{ __('site.view_all') }} <i class="zmdi zmdi-long-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
            </a>
        </header>

        <div class="home-category-grid">
            @foreach ($categoryTiles as $tile)
                <a href="{{ route('products.index', ['category' => $tile->slug]) }}" class="home-category-tile">
                    <img src="{{ $tile->cover }}" alt="{{ $tile->name }}" loading="lazy" decoding="async" width="400" height="500">
                    <span class="home-category-info">
                        <strong>{{ $tile->name }}</strong>
                        <small>{{ trans_choice('site.products_count', $tile->count, ['count' => $tile->count]) }}</small>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
