@extends('dashboard.app.layout')

@section('title', $blog->title)

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1>{{ $blog->title }}</h1>

            <div class="d-flex gap-2">
                <a href="{{ route('dashboard.blogs.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Back
                </a>

                @if(Route::has('frontend.blog.show'))
                    <a href="{{ route('frontend.blog.show', $blog->slug) }}" target="_blank" class="btn btn-success">
                        <i class="fas fa-external-link-alt"></i> Open Frontend
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-body">
                    @if($blog->image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" style="max-width: 100%; max-height: 320px; border-radius: 10px;">
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Category:</strong> {{ $blog->category?->name ?? '-' }}
                    </div>

                    <div class="mb-3">
                        <strong>Slug:</strong> <code>{{ $blog->slug }}</code>
                    </div>

                    <div class="mb-3">
                        <strong>Author:</strong> {{ $blog->author ?? '-' }}
                    </div>

                    <div class="mb-3">
                        <strong>Views:</strong> {{ number_format((int) $blog->views) }}
                    </div>

                    <div class="mb-3">
                        <strong>Featured:</strong>
                        <span class="badge {{ $blog->is_featured ? 'badge-success' : 'badge-secondary' }}">
                            {{ $blog->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge {{ $blog->status ? 'badge-success' : 'badge-danger' }}">
                            {{ $blog->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Published At:</strong>
                        {{ $blog->published_at?->format('Y-m-d h:i A') ?? '-' }}
                    </div>

                    @if($blog->excerpt)
                        <div class="mb-3">
                            <strong>Excerpt:</strong>
                            <div class="mt-2 p-3 bg-light rounded">{{ $blog->excerpt }}</div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Content:</strong>
                        <div class="mt-2 p-3 bg-light rounded" style="white-space: pre-line;">{{ $blog->content }}</div>
                    </div>

                    <hr>

                    <h5>Comments ({{ $blog->comments->count() }})</h5>

                    @forelse($blog->comments as $comment)
                        <div class="border rounded p-3 mb-3">
                            <div><strong>{{ $comment->name }}</strong> — {{ $comment->email }}</div>
                            @if($comment->website)
                                <div class="text-muted">{{ $comment->website }}</div>
                            @endif
                            <div class="mt-2">{{ $comment->comment }}</div>
                        </div>
                    @empty
                        <div class="text-muted">No comments found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
