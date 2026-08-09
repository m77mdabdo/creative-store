@extends('site.app.layout')

@section('title', __('site.blog'))

@section('content')

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-blog-blog.css') }}">

<section class="blog-hero bg-img1 txt-center p-lr-15 p-tb-92"
         style="background-image: url('{{ asset('cozastore/images/bg-02.jpg') }}');">
    <div class="container">
        <h2 class="blog-hero-title">
            {{ __('site.blog') }}
        </h2>
        <p class="blog-hero-subtitle">
            {{ __('site.continue_reading') }}
        </p>
    </div>
</section>

<section class="blog-page bg0">
    <div class="container">
        <div class="row">

            <div class="col-md-8 col-lg-9 p-b-80">
                <div class="p-r-45 p-r-0-lg">

                    @if(request('search') || request('category'))
                        <div class="blog-filters-note">

                            @if(request('search'))
                                <h5 class="mtext-107 cl2 p-b-10">
                                    {{ __('site.search_results_for') }} "{{ request('search') }}"
                                </h5>
                            @endif

                            @if(request('category'))
                                <p class="stext-115 cl6 m-b-0">
                                    {{ __('site.filtered_by_category') }}:
                                    <strong>{{ request('category') }}</strong>
                                </p>
                            @endif

                            <div class="p-t-12">
                                <a href="{{ route('blog.index') }}" class="blog-clear-link">
                                    {{ __('site.clear_filters') }}
                                </a>
                            </div>

                        </div>
                    @endif

                    @forelse($blogs as $blog)
                        <article class="blog-post-card">

                            <a href="{{ route('blog.show', $blog->slug) }}" class="blog-post-link hov-img0 how-pos5-parent">
                                <div class="blog-post-image-frame">
                                    <img src="{{ $blog->image_url }}" alt="{{ $blog->localized_title }}" loading="lazy" decoding="async">
                                </div>

                                <div class="blog-post-date-badge flex-col-c-m">
                                    <span class="ltext-107 cl2 txt-center">
                                        {{ optional($blog->published_at)->format('d') ?? $blog->created_at->format('d') }}
                                    </span>

                                    <span class="stext-109 cl3 txt-center">
                                        {{ optional($blog->published_at)->translatedFormat('M Y') ?? $blog->created_at->translatedFormat('M Y') }}
                                    </span>
                                </div>
                            </a>

                            <h3 class="blog-post-title">
                                <a href="{{ route('blog.show', $blog->slug) }}">
                                    {{ $blog->localized_title }}
                                </a>
                            </h3>

                            <p class="blog-post-excerpt">
                                {{ $blog->localized_excerpt ?? \Illuminate\Support\Str::limit(strip_tags($blog->content), 160) }}
                            </p>

                            <div class="blog-post-meta">
                                <div class="blog-post-meta-left">
                                    <span>
                                        <span class="cl4">{{ __('site.by') }}</span>
                                        {{ $blog->author ?? 'Admin' }}
                                    </span>

                                    <span class="blog-post-meta-sep">|</span>

                                    <span>
                                        {{ $blog->category->name ?? __('site.uncategorized') }}
                                    </span>

                                    <span class="blog-post-meta-sep">|</span>

                                    <span>
                                        {{ $blog->views ?? 0 }} {{ __('site.views') }}
                                    </span>
                                </div>

                                <a href="{{ route('blog.show', $blog->slug) }}" class="blog-read-more">
                                    {{ __('site.continue_reading') }}
                                    <i class="fa fa-long-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
                                </a>
                            </div>

                        </article>
                    @empty
                        <div class="blog-empty-box">
                            {{ __('site.no_blog_posts') }}
                        </div>
                    @endforelse

                    <div class="blog-pagination flex-l-m flex-w w-full p-t-10">
                        {{ $blogs->withQueryString()->links() }}
                    </div>

                </div>
            </div>

            <div class="col-md-4 col-lg-3 p-b-80">
                <aside class="blog-sidebar">

                    <div class="blog-sidebar-card">
                        <h4 class="blog-sidebar-title">
                            {{ __('site.search') }}
                        </h4>

                        <form action="{{ route('blog.index') }}" method="GET" class="blog-search-form">
                            <input class="blog-search-input"
                                   type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('site.search') }}">

                            <button type="submit" class="blog-search-btn">
                                <i class="zmdi zmdi-search"></i>
                            </button>
                        </form>
                    </div>

                    <div class="blog-sidebar-card">
                        <h4 class="blog-sidebar-title">
                            {{ __('site.categories') }}
                        </h4>

                        <ul class="blog-category-list">
                            <li class="blog-category-item">
                                <a href="{{ route('blog.index') }}" class="blog-category-link">
                                    <span>{{ __('site.all_categories') }}</span>
                                </a>
                            </li>

                            @foreach($categories as $category)
                                <li class="blog-category-item">
                                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                                       class="blog-category-link">
                                        <span>{{ $category->name }}</span>
                                        <span class="blog-category-count">({{ $category->blogs_count }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </aside>
            </div>

        </div>
    </div>
</section>

@endsection
