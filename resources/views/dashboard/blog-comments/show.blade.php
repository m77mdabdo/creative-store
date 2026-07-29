@extends('dashboard.app.layout')

@section('title', 'Comment Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Comment Details') }}</h1>
            <a href="{{ route('dashboard.blog-comments.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="mb-3">
                        <strong>{{ __('Name:') }}</strong> {{ $comment->name }}
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Email:') }}</strong> {{ $comment->email }}
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Website:') }}</strong>
                        @if($comment->website)
                            <a href="{{ $comment->website }}" target="_blank" rel="noopener noreferrer">
                                {{ $comment->website }}
                            </a>
                        @else
                            -
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Blog:') }}</strong>
                        @if($comment->blog)
                            <a href="{{ route('dashboard.blogs.show', $comment->blog) }}"
                               class="text-primary font-weight-bold">
                                {{ $comment->blog->title }}
                            </a>
                        @else
                            -
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Created At:') }}</strong> {{ $comment->created_at?->format('Y-m-d h:i A') }}
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Comment:') }}</strong>
                        <div class="mt-2 p-3 bg-light rounded" style="white-space: pre-line;">
                            {{ $comment->comment }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
