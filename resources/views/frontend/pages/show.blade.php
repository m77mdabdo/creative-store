@extends('site.app.layout')

@section('title', $translation?->title ?? $page->slug)

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-light">← Back</a>
    </div>

    <h1 class="mb-4">{{ $translation?->title ?? $page->slug }}</h1>

    <div style="white-space: pre-line; line-height: 1.9;">
        {!! nl2br(e($translation?->content ?? '')) !!}
    </div>
</div>
@endsection


