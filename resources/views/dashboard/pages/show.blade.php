@extends('dashboard.app.layout')

@section('title', $page->slug)

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1>Page Details</h1>

            <div class="d-flex gap-2">
                <a href="{{ route('dashboard.pages.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Back
                </a>

                @if(Route::has('frontend.pages.show'))
                    <a href="{{ route('frontend.pages.show', $page->slug) }}" target="_blank" class="btn btn-success">
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
                    <div class="mb-3">
                        <strong>Slug:</strong> <code>{{ $page->slug }}</code>
                    </div>

                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge {{ $page->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $page->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <hr>

                    <h5>Arabic</h5>
                    <div class="mb-3">
                        <strong>Title:</strong> {{ $translationAr?->title ?? '-' }}
                    </div>
                    <div class="mb-4">
                        <strong>Content:</strong>
                        <div class="mt-2 p-3 bg-light rounded" style="white-space: pre-line;">
                            {{ $translationAr?->content ?? '-' }}
                        </div>
                    </div>

                    <hr>

                    <h5>English</h5>
                    <div class="mb-3">
                        <strong>Title:</strong> {{ $translationEn?->title ?? '-' }}
                    </div>
                    <div class="mb-3">
                        <strong>Content:</strong>
                        <div class="mt-2 p-3 bg-light rounded" style="white-space: pre-line;">
                            {{ $translationEn?->content ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
