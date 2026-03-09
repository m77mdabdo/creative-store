@extends('site.app.layout')

@section('content')

    <!-- breadcrumb -->
    <div class="container p-t-30">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">
                Home
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>

            <a href="{{ route('blog.index') }}" class="stext-109 cl8 hov-cl1 trans-04">
                Blog
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>

            <span class="stext-109 cl4">
                {{ $blog->title }}
            </span>
        </div>
    </div>

    <!-- Content page -->
    <section class="bg0 p-t-52 p-b-20">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-lg-9 p-b-80">
                    <div class="p-r-45 p-r-0-lg">

                        <!-- Blog image -->
                        <div class="wrap-pic-w how-pos5-parent">
                            <img src="{{ $blog->image ? asset('storage/' . $blog->image) : asset('cozastore/images/blog-04.jpg') }}"
                                 alt="{{ $blog->title }}">

                            <div class="flex-col-c-m size-123 bg9 how-pos5">
                                <span class="ltext-107 cl2 txt-center">
                                    {{ optional($blog->published_at)->format('d') ?? $blog->created_at->format('d') }}
                                </span>

                                <span class="stext-109 cl3 txt-center">
                                    {{ optional($blog->published_at)->format('M Y') ?? $blog->created_at->format('M Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- Blog content -->
                        <div class="p-t-32">
                            <span class="flex-w flex-m stext-111 cl2 p-b-19">
                                <span>
                                    <span class="cl4">By</span> {{ $blog->author ?? 'Admin' }}
                                    <span class="cl12 m-l-4 m-r-6">|</span>
                                </span>

                                <span>
                                    {{ optional($blog->published_at)->format('d M, Y') ?? $blog->created_at->format('d M, Y') }}
                                    <span class="cl12 m-l-4 m-r-6">|</span>
                                </span>

                                <span>
                                    {{ $blog->category->name ?? 'Uncategorized' }}
                                    <span class="cl12 m-l-4 m-r-6">|</span>
                                </span>

                                <span>
                                    {{ $blog->views }} Views
                                </span>
                            </span>

                            <h4 class="ltext-109 cl2 p-b-28">
                                {{ $blog->title }}
                            </h4>

                            @if($blog->excerpt)
                                <p class="stext-117 cl6 p-b-26">
                                    {{ $blog->excerpt }}
                                </p>
                            @endif

                            <div class="stext-117 cl6 p-b-26">
                                {!! $blog->content !!}
                            </div>
                        </div>

                        <!-- Related posts -->
                        @if($relatedBlogs->count())
                            <div class="p-t-40">
                                <h5 class="mtext-113 cl2 p-b-25">
                                    Related Posts
                                </h5>

                                <div class="row">
                                    @foreach($relatedBlogs as $item)
                                        <div class="col-sm-6 col-md-4 p-b-30">
                                            <div class="block2">
                                                <div class="block2-pic hov-img0">
                                                    <a href="{{ route('blog.show', $item->slug) }}">
                                                        <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('cozastore/images/blog-05.jpg') }}"
                                                             alt="{{ $item->title }}">
                                                    </a>
                                                </div>

                                                <div class="block2-txt p-t-14">
                                                    <a href="{{ route('blog.show', $item->slug) }}"
                                                       class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                                        {{ \Illuminate\Support\Str::limit($item->title, 45) }}
                                                    </a>

                                                    <span class="stext-105 cl3">
                                                        {{ optional($item->published_at)->format('d M Y') ?? $item->created_at->format('d M Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Comments list -->
                        <div class="p-t-50">
                            <h5 class="mtext-113 cl2 p-b-20">
                                Comments ({{ $blog->comments->count() }})
                            </h5>

                            @forelse($blog->comments as $comment)
                                <div class="bor16 p-lr-20 p-tb-20 m-b-20">
                                    <div class="p-b-8">
                                        <strong class="stext-111 cl2">{{ $comment->name }}</strong>
                                        <span class="stext-107 cl6 m-l-10">
                                            {{ $comment->created_at->format('d M Y') }}
                                        </span>
                                    </div>

                                    <p class="stext-117 cl6 m-b-0">
                                        {{ $comment->comment }}
                                    </p>

                                    @if($comment->website)
                                        <div class="p-t-8">
                                            <a href="{{ $comment->website }}" target="_blank" class="stext-107 cl2 hov-cl1 trans-04">
                                                {{ $comment->website }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="stext-107 cl6 p-b-20">
                                    No comments yet. Be the first to comment.
                                </p>
                            @endforelse
                        </div>

                        <!-- Comment form -->
                        <div class="p-t-40">
                            <h5 class="mtext-113 cl2 p-b-12">
                                Leave a Comment
                            </h5>

                            <p class="stext-107 cl6 p-b-25">
                                Your email address will not be published. Required fields are marked *
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
                                              placeholder="Comment..."
                                              required>{{ old('comment') }}</textarea>
                                </div>

                                <div class="bor19 size-218 m-b-20">
                                    <input class="stext-111 cl2 plh3 size-116 p-lr-18"
                                           type="text"
                                           name="name"
                                           placeholder="Name *"
                                           value="{{ old('name') }}"
                                           required>
                                </div>

                                <div class="bor19 size-218 m-b-20">
                                    <input class="stext-111 cl2 plh3 size-116 p-lr-18"
                                           type="email"
                                           name="email"
                                           placeholder="Email *"
                                           value="{{ old('email') }}"
                                           required>
                                </div>

                                <div class="bor19 size-218 m-b-30">
                                    <input class="stext-111 cl2 plh3 size-116 p-lr-18"
                                           type="text"
                                           name="website"
                                           placeholder="Website"
                                           value="{{ old('website') }}">
                                </div>

                                <button type="submit"
                                        class="flex-c-m stext-101 cl0 size-125 bg3 bor2 hov-btn3 p-lr-15 trans-04">
                                    Post Comment
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4 col-lg-3 p-b-80">
                    <div class="side-menu">

                        <!-- Search -->
                        <form action="{{ route('blog.index') }}" method="GET" class="bor17 of-hidden pos-relative">
                            <input class="stext-103 cl2 plh4 size-116 p-l-28 p-r-55"
                                   type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search">

                            <button type="submit" class="flex-c-m size-122 ab-t-r fs-18 cl4 hov-cl1 trans-04">
                                <i class="zmdi zmdi-search"></i>
                            </button>
                        </form>

                        <!-- Categories -->
                        <div class="p-t-55">
                            <h4 class="mtext-112 cl2 p-b-33">
                                Categories
                            </h4>

                            <ul>
                                @forelse($categories as $category)
                                    <li class="bor18">
                                        <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                                           class="dis-block stext-115 cl6 hov-cl1 trans-04 p-tb-8 p-lr-4">
                                            {{ $category->name }} ({{ $category->blogs_count }})
                                        </a>
                                    </li>
                                @empty
                                    <li class="bor18">
                                        <span class="dis-block stext-115 cl6 p-tb-8 p-lr-4">
                                            No categories found
                                        </span>
                                    </li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- Latest Posts -->
                        {{-- <div class="p-t-65">
                            <h4 class="mtext-112 cl2 p-b-33">
                                Latest Posts
                            </h4>

                            <ul>
                                @forelse($latestBlogs as $item)
                                    <li class="flex-w flex-t p-b-30">
                                        <a href="{{ route('blog.show', $item->slug) }}"
                                           class="wrao-pic-w size-214 hov-ovelay1 m-r-20">
                                            <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('cozastore/images/product-min-01.jpg') }}"
                                                 alt="{{ $item->title }}">
                                        </a>

                                        <div class="size-215 flex-col-t p-t-8">
                                            <a href="{{ route('blog.show', $item->slug) }}"
                                               class="stext-116 cl8 hov-cl1 trans-04">
                                                {{ \Illuminate\Support\Str::limit($item->title, 45) }}
                                            </a>

                                            <span class="stext-116 cl6 p-t-20">
                                                {{ optional($item->published_at)->format('d M Y') ?? $item->created_at->format('d M Y') }}
                                            </span>
                                        </div>
                                    </li>
                                @empty
                                    <li>
                                        <span class="stext-115 cl6">
                                            No recent posts
                                        </span>
                                    </li>
                                @endforelse
                            </ul>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
