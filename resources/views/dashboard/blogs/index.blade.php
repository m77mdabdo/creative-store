@extends('dashboard.app.layout')

@section('title', __('Blogs'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h1>{{ __('Blogs') }}</h1>

            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blogs.create'))
                <a href="{{ route('dashboard.blogs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Add Blog') }}
                </a>
            @endif
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.blogs.index') }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <input type="text" name="q" class="form-control" placeholder="{{ __('Search title, slug, author...') }}"
                                       value="{{ $filters['q'] }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="category_id" class="form-control">
                                    <option value="">{{ __('All categories') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected((string) $filters['category_id'] === (string) $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="featured" class="form-control">
                                    <option value="">{{ __('Featured: All') }}</option>
                                    <option value="1" @selected($filters['featured'] === '1')>{{ __('Featured') }}</option>
                                    <option value="0" @selected($filters['featured'] === '0')>{{ __('Not Featured') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('Status: All') }}</option>
                                    <option value="1" @selected($filters['status'] === '1')>{{ __('Active') }}</option>
                                    <option value="0" @selected($filters['status'] === '0')>{{ __('Inactive') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="newest" @selected($filters['sort'] === 'newest')>{{ __('Newest') }}</option>
                                    <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('Oldest') }}</option>
                                    <option value="title_asc" @selected($filters['sort'] === 'title_asc')>{{ __('Title A-Z') }}</option>
                                    <option value="title_desc" @selected($filters['sort'] === 'title_desc')>{{ __('Title Z-A') }}</option>
                                    <option value="most_viewed" @selected($filters['sort'] === 'most_viewed')>{{ __('Most Viewed') }}</option>
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <select name="per_page" class="form-control">
                                    @foreach([10,15,25,50,100] as $size)
                                        <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> {{ __('Filter') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>

                            <tr>
                                <th>#</th>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Author') }}</th>
                                <th>{{ __('Views') }}</th>
                                <th>{{ __('Comments') }}</th>
                                <th>{{ __('Featured') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Published At') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>

                        </thead>
                        <tbody>
                            @forelse($blogs as $blog)
                                <tr>
                                    <td>{{ $blog->id }}</td>
                                    <td>
                                        @if($blog->image)
                                           @php
    $img = $blog->image
        ? media_url($blog->image)
        : asset('cozastore/images/product-01.jpg');
@endphp

<img src="{{ $img }}"
     alt="{{ $blog->title }}"
     style="width: 60px; height: 45px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <span class="text-muted">{{ __('No image') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">
                                            {{-- <a href="{{ route('dashboard.blogs.show', $blog) }}">
                                                {{ $blog->title }}
                                            </a> --}}
                                        </div>
                                        <small><code>{{ $blog->slug }}</code></small>
                                    </td>
                                    <td>{{ $blog->category?->name ?? '-' }}</td>
                                    <td>{{ $blog->author ?? '-' }}</td>
                                    <td>{{ number_format((int) $blog->views) }}</td>
                                    <td><span class="badge badge-info">{{ $blog->comments_count }}</span></td>
                                    <td>
                                        <span class="badge {{ $blog->is_featured ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $blog->is_featured ? __('Yes') : __('No') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $blog->status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $blog->status ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td>{{ $blog->published_at?->translatedFormat('Y-m-d h:i A') ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blogs.view'))
                                                <a href="{{ route('dashboard.blogs.show', $blog) }}" class="btn btn-sm btn-info" title="{{ __('View in Dashboard') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif

                                            @if(Route::has('frontend.blog.show'))
                                                <a href="{{ route('frontend.blog.show', $blog->slug) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-success"
                                                   title="{{ __('Open Frontend') }}">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blogs.edit'))
                                                <a href="{{ route('dashboard.blogs.edit', $blog) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blogs.edit'))
                                                <form action="{{ route('dashboard.blogs.toggle-status', $blog) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('Toggle Status') }}">
                                                        <i class="fas fa-power-off"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blogs.edit'))
                                                <form action="{{ route('dashboard.blogs.toggle-featured', $blog) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-primary" title="{{ __('Toggle Featured') }}">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blogs.delete'))
                                                <form action="{{ route('dashboard.blogs.destroy', $blog) }}"
                                                      method="POST"
                                                      style="display:inline-block;"
                                                      onsubmit="return confirm(@js(__('Are you sure you want to delete this blog?')));">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">{{ __('No blogs found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($blogs->hasPages())
                    <div class="card-footer clearfix">
                        {{ $blogs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
