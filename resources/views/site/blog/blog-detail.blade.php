@extends('site.app.layout')

@section('content')

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-blog-blog-detail.css') }}">

<section class="blog-details-page">

    <div class="container">
        <div class="blog-details-breadcrumb">
            <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
                <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">
                    {{ __('site.home') }}
                    <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
                </a>

                <a href="{{ route('blog.index') }}" class="stext-109 cl8 hov-cl1 trans-04">
                    {{ __('site.blog') }}
                    <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
                </a>

                <span class="stext-109 cl4">
                    {{ $blog->localized_title }}
                </span>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">

            <div class="col-md-8 col-lg-9 p-b-80">
                <div class="p-r-45 p-r-0-lg">

                    <article class="blog-main-card">
                        <div class="blog-cover-wrap">
                            <div class="blog-cover-frame">
                                <img src="{{ $blog->image_url }}" alt="{{ $blog->localized_title }}" loading="lazy" decoding="async">
                            </div>

                            <div class="blog-date-badge flex-col-c-m">
                                <span class="ltext-107 cl2 txt-center">
                                    {{ optional($blog->published_at)->format('d') ?? $blog->created_at->format('d') }}
                                </span>

                                <span class="stext-109 cl3 txt-center">
                                    {{ optional($blog->published_at)->format('M Y') ?? $blog->created_at->format('M Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="blog-details-body">
                            <div class="blog-meta-row">
                                <span>
                                    <span class="cl4">{{ __('site.by') }}</span>
                                    {{ $blog->author ?? 'Admin' }}
                                </span>

                                <span class="blog-meta-sep">|</span>

                                <span>
                                    {{ optional($blog->published_at)->format('d M, Y') ?? $blog->created_at->format('d M, Y') }}
                                </span>

                                <span class="blog-meta-sep">|</span>

                                <span>
                                    {{ $blog->category->name ?? __('site.uncategorized') }}
                                </span>

                                <span class="blog-meta-sep">|</span>

                                <span>
                                    {{ $blog->views ?? 0 }} {{ __('site.views') }}
                                </span>
                            </div>

                            <h1 class="blog-details-title">
                                {{ $blog->localized_title }}
                            </h1>

                            @if($blog->localized_excerpt)
                                <div class="blog-excerpt-box">
                                    <p>{{ $blog->localized_excerpt }}</p>
                                </div>
                            @endif

                            <div class="blog-content-box">
                                {!! $blog->safe_content !!}
                            </div>
                        </div>
                    </article>

                    @if($relatedBlogs->count())
                        <div class="blog-section-card">
                            <h5 class="blog-section-title">
                                {{ __('site.related_posts') }}
                            </h5>

                            <div class="row">
                                @foreach($relatedBlogs as $item)
                                    <div class="col-sm-6 col-md-4 p-b-30">
                                        <div class="related-post-card">
                                            <div class="related-post-image">
                                                <a href="{{ route('blog.show', $item->slug) }}">
                                                    <img src="{{ $item->image_url }}" alt="{{ $item->localized_title }}" loading="lazy" decoding="async">
                                                </a>
                                            </div>

                                            <div class="related-post-body">
                                                <a href="{{ route('blog.show', $item->slug) }}" class="related-post-title">
                                                    {{ \Illuminate\Support\Str::limit($item->localized_title, 45) }}
                                                </a>

                                                <span class="related-post-date">
                                                    {{ optional($item->published_at)->format('d M Y') ?? $item->created_at->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="blog-section-card">
                        <h5 class="blog-section-title">
                            {{ __('site.comments') }} ({{ $blog->comments->count() }})
                        </h5>

                        @forelse($blog->comments as $comment)
                            <div class="comment-card">
                                <div class="comment-head">
                                    <strong class="comment-author">{{ $comment->name }}</strong>
                                    <span class="comment-date">{{ $comment->created_at->format('d M Y') }}</span>
                                </div>

                                <p class="comment-text">
                                    {{ $comment->comment }}
                                </p>

                                @if($comment->website)
                                    <a href="{{ $comment->website }}" target="_blank" class="comment-link">
                                        {{ $comment->website }}
                                    </a>
                                @endif
                            </div>
                        @empty
                            <p class="stext-107 cl6 m-b-0">
                                {{ __('site.no_comments_yet') }}
                            </p>
                        @endforelse
                    </div>

                    <div class="comment-form-card">
                        <h5 class="blog-section-title">
                            {{ __('site.leave_comment') }}
                        </h5>

                        <p class="stext-107 cl6 p-b-25">
                            {{ __('site.comment_notice') }}
                        </p>

                        @if(session('success'))
                            <div class="alert alert-success m-b-20">
                                {{ session('success') }}
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

                        <form action="{{ route('blog.comment.store', $blog->id) }}" method="POST">
                            @csrf

                            <div class="bor19 m-b-20">
                                <textarea class="stext-111 cl2 plh3 size-124 p-lr-18 p-tb-15"
                                          name="comment"
                                          placeholder="{{ __('site.comment_placeholder') }}"
                                          required>{{ old('comment') }}</textarea>
                            </div>

                            <div class="bor19 size-218 m-b-20">
                                <input class="stext-111 cl2 plh3 size-116 p-lr-18"
                                       type="text"
                                       name="name"
                                       placeholder="{{ __('site.name_placeholder') }}"
                                       value="{{ old('name') }}"
                                       required>
                            </div>

                            <div class="bor19 size-218 m-b-20">
                                <input class="stext-111 cl2 plh3 size-116 p-lr-18"
                                       type="email"
                                       name="email"
                                       placeholder="{{ __('site.email_placeholder_comment') }}"
                                       value="{{ old('email') }}"
                                       required>
                            </div>

                            <div class="bor19 size-218 m-b-30">
                                <input class="stext-111 cl2 plh3 size-116 p-lr-18"
                                       type="text"
                                       name="website"
                                       placeholder="{{ __('site.website') }}"
                                       value="{{ old('website') }}">
                            </div>

                            <button type="submit"
                                    class="comment-submit-btn flex-c-m stext-101 cl0 size-125 bg3 bor2 hov-btn3 p-lr-15 trans-04">
                                {{ __('site.post_comment') }}
                            </button>
                        </form>
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
                            @forelse($categories as $category)
                                <li class="blog-category-item">
                                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                                       class="blog-category-link">
                                        <span>{{ $category->name }}</span>
                                        <span>({{ $category->blogs_count }})</span>
                                    </a>
                                </li>
                            @empty
                                <li class="blog-category-item">
                                    <span class="blog-category-empty">
                                        {{ __('site.no_categories_found') }}
                                    </span>
                                </li>
                            @endforelse
                        </ul>
                    </div>

                </aside>
            </div>

        </div>
    </div>
</section>

@endsection
