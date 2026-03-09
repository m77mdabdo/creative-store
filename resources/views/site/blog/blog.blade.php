@extends('site.app.layout')

@section('content')

    <section class="bg-img1 txt-center p-lr-15 p-tb-92"
        style="background-image: url('{{ asset('cozastore/images/bg-02.jpg') }}');">
        <h2 class="ltext-105 cl0 txt-center">
            Blog
        </h2>
    </section>

    <section class="bg0 p-t-62 p-b-60">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-lg-9 p-b-80">
                    <div class="p-r-45 p-r-0-lg">

                        @if(request('search') || request('category'))
                            <div class="p-b-25">
                                @if(request('search'))
                                    <h5 class="mtext-107 cl2 p-b-10">
                                        Search results for: "{{ request('search') }}"
                                    </h5>
                                @endif

                                @if(request('category'))
                                    <p class="stext-115 cl6">
                                        Filtered by category: <strong>{{ request('category') }}</strong>
                                    </p>
                                @endif

                                <div class="p-t-8">
                                    <a href="{{ route('blog.index') }}" class="stext-101 cl2 hov-cl1 trans-04">
                                        Clear Filters
                                    </a>
                                </div>
                            </div>
                        @endif

                        @forelse($blogs as $blog)
                            <div class="p-b-63">
                                <a href="{{ route('blog.show', $blog->slug) }}" class="hov-img0 how-pos5-parent">
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
                                </a>

                                <div class="p-t-32">
                                    <h4 class="p-b-15">
                                        <a href="{{ route('blog.show', $blog->slug) }}" class="ltext-108 cl2 hov-cl1 trans-04">
                                            {{ $blog->title }}
                                        </a>
                                    </h4>

                                    <p class="stext-117 cl6">
                                        {{ $blog->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($blog->content), 160) }}
                                    </p>

                                    <div class="flex-w flex-sb-m p-t-18">
                                        <span class="flex-w flex-m stext-111 cl2 p-r-30 m-tb-10">
                                            <span>
                                                <span class="cl4">By</span> {{ $blog->author ?? 'Admin' }}
                                                <span class="cl12 m-l-4 m-r-6">|</span>
                                            </span>

                                            <span>
                                                {{ $blog->category->name ?? 'Uncategorized' }}
                                                <span class="cl12 m-l-4 m-r-6">|</span>
                                            </span>

                                            <span>
                                                {{ $blog->views ?? 0 }} Views
                                            </span>
                                        </span>

                                        <a href="{{ route('blog.show', $blog->slug) }}"
                                            class="stext-101 cl2 hov-cl1 trans-04 m-tb-10">
                                            Continue Reading
                                            <i class="fa fa-long-arrow-right m-l-9"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning">
                                No blog posts found{{ request('search') ? ' for "' . request('search') . '"' : '' }}.
                            </div>
                        @endforelse

                        <div class="flex-l-m flex-w w-full p-t-10">
                            {{ $blogs->withQueryString()->links() }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3 p-b-80">
                    <div class="side-menu">

                        <!-- Search -->
                        <form action="{{ route('blog.index') }}" method="GET" class="bor17 of-hidden pos-relative">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif

                            <input class="stext-103 cl2 plh4 size-116 p-l-28 p-r-55" type="text" name="search"
                                value="{{ request('search') }}" placeholder="Search">

                            <button type="submit" class="flex-c-m size-122 ab-t-r fs-18 cl4 hov-cl1 trans-04">
                                <i class="zmdi zmdi-search"></i>
                            </button>
                        </form>

                        @if(request('search'))
                            <div class="p-t-10">
                                <a href="{{ route('blog.index', request('category') ? ['category' => request('category')] : []) }}"
                                    class="stext-101 cl2 hov-cl1 trans-04">
                                    Clear Search
                                </a>
                            </div>
                        @endif

                        <!-- Categories -->
                        <div class="p-t-55">
                            <h4 class="mtext-112 cl2 p-b-33">
                                Categories
                            </h4>

                            <ul>
                                <li class="bor18">
                                    <a href="{{ route('blog.index', request('search') ? ['search' => request('search')] : []) }}"
                                        class="dis-block stext-115 {{ request('category') ? 'cl6' : 'cl1' }} hov-cl1 trans-04 p-tb-8 p-lr-4">
                                        All Categories
                                    </a>
                                </li>

                                @foreach($categories as $category)
                                                            <li class="bor18">
                                                                <a href="{{ route('blog.index', array_filter([
                                        'category' => $category->slug,
                                        'search' => request('search'),
                                    ])) }}" class="dis-block stext-115 {{ request('category') == $category->slug ? 'cl1' : 'cl6' }} hov-cl1 trans-04 p-tb-8 p-lr-4">
                                                                    {{ $category->name }} ({{ $category->blogs_count }})
                                                                </a>
                                                            </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Featured -->
                        {{-- <div class="p-t-65">
                            <h4 class="mtext-112 cl2 p-b-33">
                                Featured Posts
                            </h4>

                            <ul>
                                @forelse($featuredBlogs as $item)
                                <li class="flex-w flex-t p-b-30">
                                    <a href="{{ route('blog.show', $item->slug) }}"
                                        class="wrao-pic-w size-214 hov-ovelay1 m-r-20">
                                        <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('cozastore/images/product-min-01.jpg') }}"
                                            alt="{{ $item->title }}">
                                    </a>

                                    <div class="size-215 flex-col-t p-t-8">
                                        <a href="{{ route('blog.show', $item->slug) }}"
                                            class="stext-116 cl8 hov-cl1 trans-04">
                                            {{ \Illuminate\Support\Str::limit($item->title, 50) }}
                                        </a>

                                        <span class="stext-116 cl6 p-t-20">
                                            {{ optional($item->published_at)->format('d M Y') ??
                                            $item->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                </li>
                                @empty
                                <li>
                                    <span class="stext-115 cl6">No featured posts.</span>
                                </li>
                                @endforelse
                            </ul>
                        </div> --}}

                        <!-- Latest -->
                        {{-- <div class="p-t-55">
                            <h4 class="mtext-112 cl2 p-b-20">
                                Latest Posts
                            </h4>

                            <ul>
                                @forelse($latestBlogs as $item)
                                <li class="p-b-7">
                                    <a href="{{ route('blog.show', $item->slug) }}"
                                        class="flex-w flex-sb-m stext-115 cl6 hov-cl1 trans-04 p-tb-2">
                                        <span>{{ \Illuminate\Support\Str::limit($item->title, 28) }}</span>
                                        <span>{{ optional($item->published_at)->format('M d') ??
                                            $item->created_at->format('M d') }}</span>
                                    </a>
                                </li>
                                @empty
                                <li>
                                    <span class="stext-115 cl6">No latest posts.</span>
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
