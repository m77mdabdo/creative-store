@extends('site.app.layout') {{-- حسب مشروعك --}}

@section('content')
<div class="container py-5">

    <h1 class="mb-3">{{ $blog->title }}</h1>

    <div class="mb-3 text-muted">
        By {{ $blog->author }} | {{ $blog->published_at?->format('Y-m-d') }}
    </div>

    @if($blog->image)
        <div class="mb-4">
            <img src="{{ asset('storage/' . $blog->image) }}"
                 style="width:100%; max-height:400px; object-fit:cover;" loading="lazy" decoding="async">
        </div>
    @endif

    <div class="mb-4">
        {!! nl2br(e($blog->content)) !!}
    </div>

    <hr>

    <h4>Comments</h4>

    @forelse($blog->comments as $comment)
        <div class="border p-3 mb-3 rounded">
            <strong>{{ $comment->name }}</strong>
            <div class="text-muted">{{ $comment->email }}</div>
            <div class="mt-2">{{ $comment->comment }}</div>
        </div>
    @empty
        <p>No comments yet.</p>
    @endforelse

</div>
@endsection
