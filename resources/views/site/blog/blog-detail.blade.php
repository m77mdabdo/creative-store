@extends('site.app.layout')

@section('content')

<style>
    .blog-details-page {
        padding-top: 34px;
        padding-bottom: 90px;
        background: #fff;
    }

    .blog-details-breadcrumb {
        margin-bottom: 20px;
    }

    .blog-details-breadcrumb .bread-crumb {
        padding-top: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .blog-main-card {
        background: #fff;
        border: 1px solid #efefef;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(0,0,0,.05);
        overflow: hidden;
    }

    .blog-cover-wrap {
        position: relative;
        background: #f7f7f7;
    }

    .blog-cover-frame {
        width: 100%;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: #f5f5f5;
    }

    .blog-cover-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .blog-date-badge {
        position: absolute;
        left: 24px;
        bottom: 24px;
        width: 82px;
        min-height: 88px;
        border-radius: 18px;
        background: rgba(255,255,255,.94);
        backdrop-filter: blur(8px);
        box-shadow: 0 10px 24px rgba(0,0,0,.08);
        z-index: 3;
    }

    .blog-details-body {
        padding: 28px 28px 30px;
    }

    .blog-meta-row {
        display: flex;
        align-items: center;
        gap: 8px 10px;
        flex-wrap: wrap;
        color: #666;
        font-size: 14px;
        margin-bottom: 18px;
    }

    .blog-meta-sep {
        color: #cfcfcf;
    }

    .blog-details-title {
        font-size: 36px;
        line-height: 1.35;
        font-weight: 800;
        color: #222;
        margin-bottom: 18px;
    }

    .blog-excerpt-box {
        border: 1px solid #ececec;
        background: #fafafa;
        border-radius: 18px;
        padding: 18px 20px;
        margin-bottom: 22px;
    }

    .blog-excerpt-box p {
        margin: 0;
        font-size: 16px;
        line-height: 1.9;
        color: #555;
    }

    .blog-content-box {
        font-size: 16px;
        line-height: 2;
        color: #555;
    }

    .blog-content-box p:last-child {
        margin-bottom: 0;
    }

    .blog-content-box img {
        max-width: 100%;
        height: auto;
        border-radius: 18px;
        display: block;
        margin: 18px auto;
    }

    .blog-section-card {
        margin-top: 32px;
        border: 1px solid #efefef;
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(0,0,0,.04);
        padding: 24px 22px;
    }

    .blog-section-title {
        font-size: 24px;
        line-height: 1.3;
        font-weight: 800;
        color: #222;
        margin-bottom: 22px;
    }

    .related-post-card {
        border: 1px solid #efefef;
        border-radius: 18px;
        background: #fff;
        overflow: hidden;
        height: 100%;
        box-shadow: 0 10px 25px rgba(0,0,0,.04);
        transition: all .25s ease;
    }

    .related-post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 35px rgba(0,0,0,.08);
    }

    .related-post-image {
        width: 100%;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: #f5f5f5;
    }

    .related-post-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .related-post-body {
        padding: 16px;
    }

    .related-post-title {
        display: block;
        font-size: 16px;
        line-height: 1.7;
        font-weight: 700;
        color: #222;
        text-decoration: none;
        margin-bottom: 8px;
        transition: all .25s ease;
    }

    .related-post-title:hover {
        color: #717fe0;
    }

    .related-post-date {
        font-size: 14px;
        color: #777;
    }

    .comment-card {
        border: 1px solid #ececec;
        border-radius: 18px;
        background: #fafafa;
        padding: 18px 18px 16px;
        margin-bottom: 16px;
    }

    .comment-card:last-child {
        margin-bottom: 0;
    }

    .comment-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .comment-author {
        font-size: 16px;
        font-weight: 700;
        color: #222;
    }

    .comment-date {
        font-size: 13px;
        color: #888;
    }

    .comment-text {
        font-size: 15px;
        line-height: 1.85;
        color: #555;
        margin-bottom: 0;
    }

    .comment-link {
        margin-top: 10px;
        display: inline-block;
        font-size: 14px;
        color: #717fe0;
        text-decoration: none;
    }

    .comment-form-card {
        border: 1px solid #efefef;
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(0,0,0,.04);
        padding: 24px 22px;
    }

    .comment-form-card textarea,
    .comment-form-card input {
        border-radius: 16px;
        border: 1px solid #e2e2e2;
        background: #fff;
    }

    .comment-form-card textarea {
        min-height: 140px;
        resize: vertical;
    }

    .comment-submit-btn {
        min-height: 48px;
        border-radius: 999px !important;
        padding-left: 26px !important;
        padding-right: 26px !important;
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

    .blog-category-empty {
        display: block;
        min-height: 48px;
        padding: 12px 16px;
        border-radius: 14px;
        background: #fafafa;
        color: #777;
    }

    html[dir="rtl"] .blog-details-page,
    html[dir="rtl"] .blog-main-card,
    html[dir="rtl"] .blog-section-card,
    html[dir="rtl"] .comment-form-card,
    html[dir="rtl"] .blog-sidebar-card,
    html[dir="rtl"] .blog-excerpt-box,
    html[dir="rtl"] .comment-card {
        text-align: right;
    }

    html[dir="rtl"] .blog-date-badge {
        left: auto;
        right: 24px;
    }

    html[dir="rtl"] .blog-meta-row,
    html[dir="rtl"] .comment-head,
    html[dir="rtl"] .blog-category-link {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .blog-search-input {
        text-align: right;
        padding: 0 20px 0 58px;
    }

    html[dir="rtl"] .blog-search-btn {
        right: auto;
        left: 0;
    }

    @media (max-width: 991px) {
        .blog-sidebar {
            position: static;
            top: auto;
            margin-top: 28px;
        }

        .blog-details-title {
            font-size: 30px;
        }
    }

    @media (max-width: 767px) {
        .blog-details-page {
            padding-top: 20px;
            padding-bottom: 70px;
        }

        .blog-details-body,
        .blog-section-card,
        .comment-form-card,
        .blog-sidebar-card {
            padding: 18px 16px;
        }

        .blog-details-title {
            font-size: 24px;
        }

        .blog-date-badge {
            width: 70px;
            min-height: 74px;
            border-radius: 16px;
        }
    }
</style>

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
                    {{ $blog->title }}
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
                                <img src="{{ $blog->image ? asset('storage/' . $blog->image) : asset('cozastore/images/blog-04.jpg') }}"
                                     alt="{{ $blog->title }}">
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
                                {{ $blog->title }}
                            </h1>

                            @if($blog->excerpt)
                                <div class="blog-excerpt-box">
                                    <p>{{ $blog->excerpt }}</p>
                                </div>
                            @endif

                            <div class="blog-content-box">
                                {!! $blog->content !!}
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
                                                    <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('cozastore/images/blog-05.jpg') }}"
                                                         alt="{{ $item->title }}">
                                                </a>
                                            </div>

                                            <div class="related-post-body">
                                                <a href="{{ route('blog.show', $item->slug) }}" class="related-post-title">
                                                    {{ \Illuminate\Support\Str::limit($item->title, 45) }}
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
