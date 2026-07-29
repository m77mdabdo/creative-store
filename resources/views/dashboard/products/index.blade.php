@extends('dashboard.app.layout')

@section('title', 'Products')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-2">{{ __('Products') }}</h1>

            <div class="mb-2">
                @if($canViewTrash)
                    <a href="{{ route('dashboard.products.trash') }}" class="btn btn-dark">
                        <i class="fas fa-trash mr-1"></i> {{ __('Trash') }}
                    </a>
                @endif

                @if($canCreate)
                    <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> {{ __('Add Product') }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Filter Products') }}</h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.products.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label>{{ __('Search') }}</label>
                                <input type="text" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="{{ __('Name, slug, sku...') }}">
                            </div>

                            <div class="col-md-2">
                                <label>{{ __('Category') }}</label>
                                <select name="category_id" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (string) $filters['category_id'] === (string) $category->id ? 'selected' : '' }}>
                                            {{ $category->name ?? __('No Name') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>{{ __('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    
                                    <option value="draft" {{ $filters['status'] === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                        <option value="archived" {{ $filters['status'] === 'archived' ? 'selected' : '' }}>{{ __('Archived') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>{{ __('Featured') }}</label>
                                <select name="featured" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="yes" {{ $filters['featured'] === 'yes' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                    <option value="no" {{ $filters['featured'] === 'no' ? 'selected' : '' }}>{{ __('No') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>{{ __('Has Variants') }}</label>
                                <select name="has_variants" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="yes" {{ $filters['has_variants'] === 'yes' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                    <option value="no" {{ $filters['has_variants'] === 'no' ? 'selected' : '' }}>{{ __('No') }}</option>
                                </select>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button class="btn btn-info mr-2" type="submit">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($products->count())
                <div class="card card-outline card-secondary">
                    @if($canEdit)
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <h3 class="card-title mb-2">{{ __('Bulk Actions') }}</h3>

                            <form id="bulkProductForm" action="{{ route('dashboard.products.bulk-action') }}" method="POST" class="d-flex align-items-center">
                                @csrf
                                <select name="action" class="form-control mr-2" required>
                                    <option value="">{{ __('Select action') }}</option>
                                    <option value="activate">{{ __('Activate') }}</option>
                                    <option value="deactivate">{{ __('Deactivate') }}</option>
                                    <option value="feature">{{ __('Feature') }}</option>
                                    <option value="unfeature">{{ __('Unfeature') }}</option>
                                    <option value="delete">{{ __('Move to Trash') }}</option>
                                </select>

                                <button type="submit" class="btn btn-warning">{{ __('Apply') }}</button>
                            </form>
                        </div>
                    @endif

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        @if($canEdit)
                                            <th><input type="checkbox" id="check-all-products"></th>
                                        @endif
                                        <th>#</th>
                                        <th>{{ __('Image') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('SKU') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Featured') }}</th>
                                        <th>{{ __('Variants') }}</th>
                                        <th>{{ __('Reviews') }}</th>
                                        <th width="280">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            @if($canEdit)
                                                <td>
                                                    <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="product-checkbox" form="bulkProductForm">
                                                </td>
                                            @endif
                                            <td>{{ $product->id }}</td>
                                            <td>
                                               @php
    $img = $product->primaryImage?->path
        ? media_url($product->primaryImage->path)
        : asset('cozastore/images/product-01.jpg');
@endphp

<img src="{{ $img }}" alt="" width="50" height="50" style="object-fit:cover;border-radius:6px;">
                                            </td>
                                            <td>{{ $product->name ?? '-' }}</td>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->category?->name ?? '-' }}</td>
                                            <td>{{ number_format((float) $product->base_price, 2) }} {{ $product->currency }}</td>
                                            <td>
                                                <span class="badge {{ $product->status === 'active' ? 'badge-success' : ($product->status === 'draft' ? 'badge-warning' : 'badge-secondary') }}">
                                                    {{ status_label($product->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($product->is_featured)
                                                    <span class="badge badge-info">{{ __('Featured') }}</span>
                                                @else
                                                    <span class="badge badge-light">{{ __('No') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $product->variants_count }}</td>
                                            <td>{{ $product->all_reviews_count }}</td>
                                            <td>
                                                <a href="{{ route('dashboard.products.show', $product) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('dashboard.products.reviews.index', $product) }}" class="btn btn-sm btn-light">
                                                    <i class="fas fa-comments"></i>
                                                </a>

                                                @if($canEdit)
                                                    <a href="{{ route('dashboard.products.edit', $product) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="{{ route('dashboard.products.variants.index', $product) }}" class="btn btn-sm btn-dark">
                                                        <i class="fas fa-layer-group"></i>
                                                    </a>

                                                    <a href="{{ route('dashboard.products.images.index', $product) }}" class="btn btn-sm btn-secondary">
                                                        <i class="fas fa-images"></i>
                                                    </a>

                                                    <form action="{{ route('dashboard.products.toggle-status', $product) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('dashboard.products.toggle-featured', $product) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($canDelete)
                                                    <form action="{{ route('dashboard.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Move product to trash?')));">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $canEdit ? '12' : '11' }}" class="text-center py-4">{{ __('No products found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($products->hasPages())
                        <div class="card-footer clearfix">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('check-all-products');
    const boxes = document.querySelectorAll('.product-checkbox');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            boxes.forEach(box => box.checked = checkAll.checked);
        });
    }
});
</script>
@endpush
@endsection
