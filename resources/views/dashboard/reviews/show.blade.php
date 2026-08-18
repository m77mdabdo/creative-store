@extends('dashboard.app.layout')

@section('title', __('Review Details'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1>{{ __('Review Details') }}</h1>

            <div>
                <a href="{{ route('dashboard.reviews.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
                </a>

                @if($canEdit)
                    <form action="{{ route('dashboard.reviews.toggle-status', $review) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-power-off mr-1"></i>
                            {{ $review->is_approved ? __('Unapprove') : __('Approve') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Review Content') }}</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ __('Title:') }}</strong> {{ $review->title ?: '-' }}</p>
                            <p><strong>{{ __('Rating:') }}</strong> <span class="badge badge-warning">{{ $review->rating }}/5</span></p>
                            <p>
                                <strong>{{ __('Status:') }}</strong>
                                @if($review->is_approved)
                                    <span class="badge badge-success">{{ __('Approved') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Pending') }}</span>
                                @endif
                            </p>
                            <p><strong>{{ __('Comment:') }}</strong></p>
                            <div class="border rounded p-3 bg-light">
                                {{ $review->comment ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Customer Info') }}</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ __('Name:') }}</strong> {{ $review->user?->name ?? __('Guest / Deleted User') }}</p>
                            <p><strong>{{ __('Email:') }}</strong> {{ $review->user?->email ?? '-' }}</p>
                            <p><strong>{{ __('Date:') }}</strong> {{ $review->created_at?->translatedFormat('Y-m-d h:i A') }}</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Product Info') }}</h3>
                        </div>
                        <div class="card-body">
                            @if($review->product?->primaryImage)
                                <div class="mb-3">
                                    <img src="{{ media_url($review->product->primaryImage->path) }}" class="img-fluid rounded border" alt="">
                                </div>
                            @endif

                            <p><strong>{{ __('Product:') }}</strong> {{ $review->product?->name ?? '-' }}</p>
                            <p><strong>{{ __('SKU:') }}</strong> {{ $review->product?->sku ?? '-' }}</p>

                            @if($review->product)
                                <a href="{{ route('dashboard.products.show', $review->product) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye mr-1"></i> {{ __('View Product') }}
                                </a>

                                <a href="{{ route('dashboard.products.reviews.index', $review->product) }}" class="btn btn-dark btn-sm">
                                    <i class="fas fa-comments mr-1"></i> {{ __('Product Reviews') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($canDelete)
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('dashboard.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm(@js(__('Delete this review?')));">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-block">
                                        <i class="fas fa-trash mr-1"></i> {{ __('Delete Review') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
