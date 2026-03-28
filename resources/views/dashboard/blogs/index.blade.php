@extends('dashboard.app.layout')

@section('title', 'Blogs')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h1>Blogs</h1>

            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blogs.create'))
                <a href="{{ route('dashboard.blogs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Blog
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
                                <input type="text" name="q" class="form-control" placeholder="Search title, slug, author..."
                                       value="{{ $filters['q'] }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="category_id" class="form-control">
                                    <option value="">All categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected((string) $filters['category_id'] === (string) $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="featured" class="form-control">
                                    <option value="">Featured: All</option>
                                    <option value="1" @selected($filters['featured'] === '1')>Featured</option>
                                    <option value="0" @selected($filters['featured'] === '0')>Not Featured</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">Status: All</option>
                                    <option value="1" @selected($filters['status'] === '1')>Active</option>
                                    <option value="0" @selected($filters['status'] === '0')>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
                                    <option value="oldest" @selected($filters['sort'] === 'oldest')>Oldest</option>
                                    <option value="title_asc" @selected($filters['sort'] === 'title_asc')>Title A-Z</option>
                                    <option value="title_desc" @selected($filters['sort'] === 'title_desc')>Title Z-A</option>
                                    <option value="most_viewed" @selected($filters['sort'] === 'most_viewed')>Most Viewed</option>
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
                                    <i class="fas fa-search"></i> Filter
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
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Views</th>
                                <th>Comments</th>
                                <th>Featured</th>
                                <th>Status</th>
                                <th>Published At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blogs as $blog)
                                <tr>
                                    <td>{{ $blog->id }}</td>
                                    <td>
                                        @if($blog->image)
                                            <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}"
                                                 style="width: 60px; height: 45px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $blog->title }}</div>
                                        <small><code>{{ $blog->slug }}</code></small>
                                    </td>
                                    <td>{{ $blog->category?->name ?? '-' }}</td>
                                    <td>{{ $blog->author ?? '-' }}</td>
                                    <td>{{ number_format((int) $blog->views) }}</td>
                                    <td><span class="badge badge-info">{{ $blog->comments_count }}</span></td>
                                    <td>
                                        <span class="badge {{ $blog->is_featured ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $blog->is_featured ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $blog->status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $blog->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $blog->published_at?->format('Y-m-d h:i A') ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blogs.view'))
                                                <a href="{{ route('dashboard.blogs.show', $blog) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
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
                                                    <button type="submit" class="btn btn-sm btn-secondary" title="Toggle Status">
                                                        <i class="fas fa-power-off"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blogs.edit'))
                                                <form action="{{ route('dashboard.blogs.toggle-featured', $blog) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-primary" title="Toggle Featured">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blogs.delete'))
                                                <form action="{{ route('dashboard.blogs.destroy', $blog) }}"
                                                      method="POST"
                                                      style="display:inline-block;"
                                                      onsubmit="return confirm('Are you sure you want to delete this blog?');">
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
                                    <td colspan="11" class="text-center text-muted py-4">No blogs found.</td>
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
