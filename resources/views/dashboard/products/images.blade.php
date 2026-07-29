@extends('dashboard.app.layout')

@section('title', 'Product Images')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Images - {{ $product->name ?? $product->sku }}</h1>

            <a href="{{ route('dashboard.products.show', $product) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card card-outline card-primary">
                <div class="card-header"><h3 class="card-title">{{ __('Upload General Images') }}</h3></div>

                <div class="card-body">
                    <form action="{{ route('dashboard.products.images.store', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="images[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp">
                        <button type="submit" class="btn btn-primary mt-3">{{ __('Upload') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ __('General Images') }}</h3></div>

                <div class="card-body">
                    <div class="row">
                        @forelse($generalImages as $image)
                            <div class="col-md-2 mb-3">
                                <img src="{{ media_url($image->path) }}" class="img-fluid rounded border mb-2" alt="">
                                <div class="mb-2">
                                    <form action="{{ route('dashboard.products.images.set-primary', [$product, $image]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('dashboard.products.images.destroy', [$product, $image]) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Delete image?')));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <form action="{{ route('dashboard.products.images.sort', [$product, $image]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="sort_order" value="{{ $image->sort_order }}" class="form-control form-control-sm">
                                </form>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted mb-0">{{ __('No general images uploaded.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ __('Variant Color Images') }}</h3></div>
                <div class="card-body">
                    @forelse($product->variants as $variant)
                        <div class="border rounded p-3 mb-3">
                            <h5>
                                {{ $variant->values->pluck('value.value')->filter()->implode(' / ') ?: $variant->sku }}
                            </h5>

                            <form action="{{ route('dashboard.products.variants.images.store', [$product, $variant]) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                                @csrf
                                <input type="file" name="images[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp">
                                <button type="submit" class="btn btn-info btn-sm mt-2">{{ __('Upload Variant Images') }}</button>
                            </form>

                            <div class="row">
                                @forelse($variant->images as $image)
                                    <div class="col-md-2 mb-3">
                                        @php
    $img = $image->path
        ? media_url($image->path)
        : asset('cozastore/images/product-01.jpg');
@endphp

<img src="{{ $img }}" class="img-fluid rounded border" style="height:120px; object-fit:cover; width:100%;" alt="">

                                        <form action="{{ route('dashboard.products.variants.images.set-primary', [$product, $variant, $image]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('dashboard.products.variants.images.destroy', [$product, $variant, $image]) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Delete image?')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p class="text-muted">{{ __('No images for this variant yet.') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No variants available yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
