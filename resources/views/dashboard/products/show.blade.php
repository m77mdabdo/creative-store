@extends('dashboard.app.layout')

@section('title', 'Product Details')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
                <h1>{{ __('Product Details') }}</h1>

                <div class="d-flex flex-wrap">
                    <a href="{{ route('dashboard.products.index') }}" class="btn btn-secondary mr-2 mb-2">
                        <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
                    </a>

                    <a href="{{ route('dashboard.products.reviews.index', $product) }}" class="btn btn-outline-dark mr-2 mb-2">
                        <i class="fas fa-comments mr-1"></i> {{ __('Reviews') }}
                    </a>

                    @if($canEdit)
                        <a href="{{ route('dashboard.products.edit', $product) }}" class="btn btn-primary mr-2 mb-2">
                            <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
                        </a>

                        <a href="{{ route('dashboard.products.variants.index', $product) }}" class="btn btn-dark mr-2 mb-2">
                            <i class="fas fa-layer-group mr-1"></i> {{ __('Variants') }}
                        </a>

                        <a href="{{ route('dashboard.products.images.index', $product) }}" class="btn btn-info mb-2">
                            <i class="fas fa-images mr-1"></i> {{ __('Images') }}
                        </a>
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
                                <h3 class="card-title">{{ __('Product Info') }}</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>{{ __('ID:') }}</strong> {{ $product->id }}</p>
                                <p><strong>{{ __('Arabic Name:') }}</strong>
                                    {{ optional($product->translations->firstWhere('locale', 'ar'))->name ?? '-' }}</p>
                                <p><strong>{{ __('English Name:') }}</strong>
                                    {{ optional($product->translations->firstWhere('locale', 'en'))->name ?? '-' }}</p>
                                <p><strong>{{ __('SKU:') }}</strong> {{ $product->sku }}</p>
                                <p><strong>{{ __('Slug:') }}</strong> {{ $product->slug }}</p>
                                <p><strong>{{ __('Category:') }}</strong> {{ $product->category?->name ?? '-' }}</p>
                                <p><strong>{{ __('Price:') }}</strong> {{ number_format((float) $product->base_price, 2) }} {{ $product->currency }}</p>
                                <p><strong>{{ __('Status:') }}</strong> {{ status_label($product->status) }}</p>
                                <p><strong>{{ __('Has Variants:') }}</strong> {{ $product->has_variants ? __('Yes') : __('No') }}</p>
                                <p>
                                    <strong>{{ __('Attributes:') }}</strong>
                                    @if($product->attributes->count())
                                        {{ $product->attributes->pluck('name')->implode(' / ') }}
                                    @else
                                        -
                                    @endif
                                </p>
                                <p><strong>{{ __('Featured:') }}</strong> {{ $product->is_featured ? __('Yes') : __('No') }}</p>
                                <p><strong>{{ __('Tax Rate:') }}</strong> {{ $product->tax_rate }}</p>
                                <p><strong>{{ __('Weight:') }}</strong> {{ $product->weight }}</p>
                                <p><strong>{{ __('Meta Title:') }}</strong> {{ $product->meta_title ?: '-' }}</p>
                                <p><strong>{{ __('Meta Description:') }}</strong> {{ $product->meta_description ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Descriptions') }}</h3>
                            </div>
                            <div class="card-body">
                                <h5>{{ __('Arabic Description') }}</h5>
                                <p>{{ optional($product->translations->firstWhere('locale', 'ar'))->description ?? '-' }}</p>

                                <hr>

                                <h5>{{ __('English Description') }}</h5>
                                <p>{{ optional($product->translations->firstWhere('locale', 'en'))->description ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('General Images') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @forelse($product->images as $image)
                                        <div class="col-6 mb-3">
                                          @php
    $img = $image->path
        ? media_url($image->path)
        : asset('cozastore/images/product-01.jpg');
@endphp

<img src="{{ $img }}" class="img-fluid rounded border" style="height:120px; object-fit:cover; width:100%;" alt="">
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted mb-0">{{ __('No general images.') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Counts') }}</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>{{ __('Variants:') }}</strong> {{ $product->variants_count }}</p>
                                <p><strong>{{ __('Images:') }}</strong> {{ $product->images_count }}</p>
                                <p><strong>{{ __('Reviews:') }}</strong> {{ $product->all_reviews_count }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
