@extends('site.app.layout')

@section('content')

<style>
    .blog-hero {
        min-height: 260px;
        position: relative;
        background-size: cover;
        background-position: center;
        overflow: hidden;
    }

    .blog-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,.35), rgba(0,0,0,.45));
    }

    .blog-hero .container {
        position: relative;
        z-index: 2;
    }

    .blog-hero-title {
        font-size: 48px;
        font-weight: 800;
        color: #fff;
        margin: 0;
    }

    .blog-hero-subtitle {
        margin-top: 12px;
        font-size: 16px;
        color: rgba(255,255,255,.9);
    }

    .blog-page {
        padding-top: 70px;
        padding-bottom: 90px;
        background: #fff;
    }

    .blog-filters-note {
        border: 1px solid #ececec;
        background: #fafafa;
        border-radius: 18px;
        padding: 18px 20px;
        margin-bottom: 28px;
    }

    .blog-clear-link {
        display: inline-flex;
        align-items: center;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 999px;
        background: rgba(113,127,224,.08);
        color: #717fe0;
        text-decoration: none;
        font-weight: 600;
        transition: all .25s ease;
    }

    .blog-clear-link:hover {
        background: rgba(113,127,224,.14);
        color: #5f6ddb;
    }

    .blog-post-card {
        padding-bottom: 34px;
        margin-bottom: 34px;
        border-bottom: 1px solid #efefef;
    }

    .blog-post-card:last-child {
        border-bottom: 0;
        margin-bottom: 0;
    }

    .blog-post-link {
        display: block;
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        background: #f7f7f7;
        box-shadow: 0 12px 30px rgba(0,0,0,.05);
    }

    .blog-post-image-frame {
        width: 100%;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: #f5f5f5;
    }

    .blog-post-image-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .4s ease;
    }

    .blog-post-link:hover .blog-post-image-frame img {
        transform: scale(1.05);
    }

    .blog-post-date-badge {
        position: absolute;
        left: 22px;
        bottom: 22px;
        width: 78px;
        min-height: 84px;
        border-radius: 18px;
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(8px);
        box-shadow: 0 10px 24px rgba(0,0,0,.08);
    }

    .blog-post-title {
        margin-top: 24px;
        margin-bottom: 12px;
    }

    .blog-post-title a {
        font-size: 30px;
        line-height: 1.35;
        font-weight: 700;
        color: #222;
        text-decoration: none;
        transition: all .25s ease;
    }

    .blog-post-title a:hover {
        color: #717fe0;
    }

    .blog-post-excerpt {
        font-size: 15px;
        line-height: 1.9;
        color: #666;
        margin-bottom: 0;
    }

    .blog-post-meta {
        margin-top: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .blog-post-meta-left {
        display: flex;
        align-items: center;
        gap: 8px 10px;
        flex-wrap: wrap;
        color: #666;
        font-size: 14px;
    }

    .blog-post-meta-sep {
        color: #c8c8c8;
    }

    .blog-read-more {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 999px;
        border: 1px solid #ececec;
        background: #fff;
        color: #222;
        text-decoration: none;
        font-weight: 600;
        transition: all .25s ease;
    }

    .blog-read-more:hover {
        color: #717fe0;
        border-color: rgba(113,127,224,.25);
        background: rgba(113,127,224,.05);
    }

    .blog-sidebar {
        position: sticky;
        top: 120px;
    }

    .blog-sidebar-card {
        border: 1px solid #efefef;
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 12px 30px rgba(0,0,0,.04);
        padding: 22px 20px;
        margin-bottom: 22px;
    }

    .blog-sidebar-title {
        font-size: 22px;
        font-weight: 800;
        color: #222;
        margin-bottom: 22px;
    }

    .blog-search-form {
        position: relative;
        border: 1px solid #ececec;
        border-radius: 999px;
        overflow: hidden;
        background: #fafafa;
        min-height: 52px;
    }

    .blog-search-input {
        width: 100%;
        height: 52px;
        border: 0;
        background: transparent;
        color: #222;
        padding: 0 58px 0 20px;
        outline: none;
        font-size: 15px;
    }

    .blog-search-btn {
        position: absolute;
        top: 0;
        right: 0;
        width: 52px;
        height: 52px;
        border: 0;
        background: transparent;
        color: #666;
        transition: all .25s ease;
    }

    .blog-search-btn:hover {
        color: #717fe0;
    }

    .blog-category-list,
    .blog-category-list li {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .blog-category-item + .blog-category-item {
        margin-top: 10px;
    }

    .blog-category-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 48px;
        padding: 0 16px;
        border-radius: 14px;
        background: #fafafa;
        color: #444;
        text-decoration: none;
        transition: all .25s ease;
        border: 1px solid transparent;
    }

    .blog-category-link:hover {
        color: #717fe0;
        background: rgba(113,127,224,.06);
        border-color: rgba(113,127,224,.12);
    }

    .blog-category-count {
        color: #888;
        font-size: 13px;
    }

    .blog-empty-box {
        border-radius: 18px;
        border: 1px solid #eee;
        background: #fff8e8;
        color: #8a6d3b;
        padding: 18px 22px;
    }

    .blog-pagination {
        margin-top: 26px;
    }

    .blog-pagination nav {
        display: flex;
        justify-content: flex-start;
    }

    .blog-pagination svg {
        width: 18px;
        height: 18px;
    }

    html[dir="rtl"] .blog-page,
    html[dir="rtl"] .blog-filters-note,
    html[dir="rtl"] .blog-post-card,
    html[dir="rtl"] .blog-sidebar-card {
        text-align: right;
    }

    html[dir="rtl"] .blog-post-meta,
    html[dir="rtl"] .blog-post-meta-left {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .blog-post-date-badge {
        left: auto;
        right: 22px;
    }

    html[dir="rtl"] .blog-search-input {
        text-align: right;
        padding: 0 20px 0 58px;
    }

    html[dir="rtl"] .blog-search-btn {
        right: auto;
        left: 0;
    }

    html[dir="rtl"] .blog-category-link {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .blog-pagination nav {
        justify-content: flex-start;
    }

    @media (max-width: 1199px) {
        .blog-post-title a {
            font-size: 26px;
        }
    }

    @media (max-width: 991px) {
        .blog-sidebar {
            position: static;
            top: auto;
        }

        .blog-page {
            padding-top: 50px;
        }
    }

    @media (max-width: 767px) {
        .blog-hero {
            min-height: 210px;
        }

        .blog-hero-title {
            font-size: 34px;
        }

        .blog-page {
            padding-top: 35px;
            padding-bottom: 70px;
        }

        .blog-post-title a {
            font-size: 22px;
        }

        .blog-post-date-badge {
            width: 68px;
            min-height: 72px;
            border-radius: 15px;
        }
    }
</style>

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
                                    <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}">
                                </div>

                                <div class="blog-post-date-badge flex-col-c-m">
                                    <span class="ltext-107 cl2 txt-center">
                                        {{ optional($blog->published_at)->format('d') ?? $blog->created_at->format('d') }}
                                    </span>

                                    <span class="stext-109 cl3 txt-center">
                                        {{ optional($blog->published_at)->format('M Y') ?? $blog->created_at->format('M Y') }}
                                    </span>
                                </div>
                            </a>

                            <h3 class="blog-post-title">
                                <a href="{{ route('blog.show', $blog->slug) }}">
                                    {{ $blog->title }}
                                </a>
                            </h3>

                            <p class="blog-post-excerpt">
                                {{ $blog->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($blog->content), 160) }}
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
                                    <i class="fa fa-long-arrow-right"></i>
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
