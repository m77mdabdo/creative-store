@extends('dashboard.app.layout')

@section('title', 'Product Variants')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1>Variants - {{ $product->name ?? $product->sku }}</h1>

            <div>
                <a href="{{ route('dashboard.products.show', $product) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>

                <a href="{{ route('dashboard.products.images.index', $product) }}" class="btn btn-info">
                    <i class="fas fa-images mr-1"></i> Images
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Generate Variants</h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('dashboard.products.variants.generate', $product) }}" method="POST">
                        @csrf
                        <div class="row">
                            @foreach($attributes as $attribute)
                                <div class="col-md-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="attribute_ids[]" value="{{ $attribute->id }}" id="attr_{{ $attribute->id }}">
                                        <label class="form-check-label" for="attr_{{ $attribute->id }}">
                                            {{ $attribute->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-primary mt-2">Generate Missing Combinations</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Current Variants</h3></div>

                <div class="card-body">
                    @forelse($product->variants as $variant)
                        <div class="border rounded p-3 mb-3">
                            <form action="{{ route('dashboard.products.variants.update', [$product, $variant]) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-3">
                                        <label>SKU</label>
                                        <input type="text" name="sku" class="form-control" value="{{ $variant->sku }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label>Price</label>
                                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ $variant->price }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label>Stock</label>
                                        <input type="number" min="0" name="stock" class="form-control" value="{{ $variant->stock }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label>Weight</label>
                                        <input type="number" step="0.01" min="0" name="weight" class="form-control" value="{{ $variant->weight }}">
                                    </div>

                                    <div class="col-md-3 d-flex align-items-end">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="variant_active_{{ $variant->id }}" name="is_active" value="1" {{ $variant->is_active ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="variant_active_{{ $variant->id }}">Active</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    @foreach($variant->values as $idx => $value)
                                        <div class="col-md-6">
                                            <input type="hidden" name="values[{{ $idx }}][attribute_id]" value="{{ $value->attribute_id }}">
                                            <input type="hidden" name="values[{{ $idx }}][attribute_value_id]" value="{{ $value->attribute_value_id }}">

                                            <div class="border rounded p-2">
                                                <strong>{{ $value->attribute?->name ?? '-' }}:</strong>
                                                {{ $value->value?->value ?? '-' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                </div>
                            </form>

                            <div class="mt-2">
                                <form action="{{ route('dashboard.products.variants.toggle-status', [$product, $variant]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>

                                <form action="{{ route('dashboard.products.variants.destroy', [$product, $variant]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this variant?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <hr>

                            <form action="{{ route('dashboard.products.variants.images.store', [$product, $variant]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label>Upload images for this variant</label>
                                <input type="file" name="images[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp">
                                <button type="submit" class="btn btn-info btn-sm mt-2">Upload Variant Images</button>
                            </form>

                            @if($variant->images->count())
                                <div class="row mt-3">
                                    @foreach($variant->images as $image)
                                        <div class="col-md-2 mb-3">
                                           @php
    $img = $image->path
        ? Storage::url($image->path)
        : asset('cozastore/images/product-01.jpg');
@endphp

<img src="{{ $img }}" class="img-fluid rounded border" style="height:120px; object-fit:cover; width:100%;" alt="">
                                            <div>
                                                <form action="{{ route('dashboard.products.variants.images.set-primary', [$product, $variant, $image]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('dashboard.products.variants.images.destroy', [$product, $variant, $image]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete image?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No variants yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
