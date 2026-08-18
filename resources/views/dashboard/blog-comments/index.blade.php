@extends('dashboard.app.layout')

@section('title', __('Blog Comments'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ __('Blog Comments') }}</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.blog-comments.index') }}">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <input type="text" name="q" class="form-control"
                                       placeholder="{{ __('Search name, email, website, comment...') }}"
                                       value="{{ $filters['q'] }}">
                            </div>

                            <div class="col-md-3 mb-2">
                                <select name="blog_id" class="form-control">
                                    <option value="">{{ __('All blogs') }}</option>
                                    @foreach($blogs as $blog)
                                        <option value="{{ $blog->id }}" @selected((string) $filters['blog_id'] === (string) $blog->id)>
                                            {{ $blog->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="newest" @selected($filters['sort'] === 'newest')>{{ __('Newest') }}</option>
                                    <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('Oldest') }}</option>
                                    <option value="name_asc" @selected($filters['sort'] === 'name_asc')>{{ __('Name A-Z') }}</option>
                                    <option value="name_desc" @selected($filters['sort'] === 'name_desc')>{{ __('Name Z-A') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="per_page" class="form-control">
                                    @foreach([10,15,25,50,100] as $size)
                                        <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i>
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
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Blog') }}</th>
                                <th>{{ __('Website') }}</th>
                                <th>{{ __('Comment') }}</th>
                                <th>{{ __('Created At') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comments as $comment)
                                <tr>
                                    <td>{{ $comment->id }}</td>
                                    <td>{{ $comment->name }}</td>
                                    <td>{{ $comment->email }}</td>
                                    <td>
                                        @if($comment->blog)
                                            <a href="{{ route('dashboard.blogs.show', $comment->blog) }}" class="font-weight-bold">
                                                {{ $comment->blog->title }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $comment->website ?? '-' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($comment->comment, 60) }}</td>
                                    <td>{{ $comment->created_at?->translatedFormat('Y-m-d h:i A') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blog-comments.view'))
                                                <a href="{{ route('dashboard.blog-comments.show', $comment) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blog-comments.delete'))
                                                <form action="{{ route('dashboard.blog-comments.destroy', $comment) }}"
                                                      method="POST"
                                                      style="display:inline-block;"
                                                      onsubmit="return confirm(@js(__('Are you sure you want to delete this comment?')));">
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
                                    <td colspan="8" class="text-center text-muted py-4">{{ __('No comments found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($comments->hasPages())
                    <div class="card-footer clearfix">
                        {{ $comments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
