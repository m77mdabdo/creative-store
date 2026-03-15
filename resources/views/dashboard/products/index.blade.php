@extends('dashboard.app.layout')

@section('title', 'Products')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-2">Products</h1>

            <div class="mb-2">
                @if($canViewTrash)
                    <a href="{{ route('dashboard.products.trash') }}" class="btn btn-dark">
                        <i class="fas fa-trash mr-1"></i> Trash
                    </a>
                @endif

                @if($canCreate)
                    <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Add Product
                    </a>
                @endif
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
                    <h3 class="card-title">Filter Products</h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.products.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="Name, slug, sku...">
                            </div>

                            <div class="col-md-2">
                                <label>Category</label>
                                <select name="category_id" class="form-control">
                                    <option value="">All</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (string) $filters['category_id'] === (string) $category->id ? 'selected' : '' }}>
                                            {{ $category->name ?? 'No Name' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="draft" {{ $filters['status'] === 'draft' ? 'selected' : '' }}>Draft</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Featured</label>
                                <select name="featured" class="form-control">
                                    <option value="">All</option>
                                    <option value="yes" {{ $filters['featured'] === 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ $filters['featured'] === 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Has Variants</label>
                                <select name="has_variants" class="form-control">
                                    <option value="">All</option>
                                    <option value="yes" {{ $filters['has_variants'] === 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ $filters['has_variants'] === 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button class="btn btn-info mr-2" type="submit">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($canEdit && $products->count())
                <div class="card card-outline card-secondary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2">Bulk Actions</h3>

                        <form id="bulkProductForm" action="{{ route('dashboard.products.bulk-action') }}" method="POST" class="d-flex align-items-center">
                            @csrf
                            <select name="action" class="form-control mr-2" required>
                                <option value="">Select action</option>
                                <option value="activate">Activate</option>
                                <option value="deactivate">Deactivate</option>
                                <option value="feature">Feature</option>
                                <option value="unfeature">Unfeature</option>
                                <option value="delete">Move to Trash</option>
                            </select>

                            <button type="submit" class="btn btn-warning">Apply</button>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="check-all-products"></th>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>SKU</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Featured</th>
                                        <th>Variants</th>
                                        <th>Reviews</th>
                                        <th width="280">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="product-checkbox" form="bulkProductForm">
                                            </td>
                                            <td>{{ $product->id }}</td>
                                            <td>
                                                @if($product->primaryImage)
                                                    <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="" width="50" height="50" style="object-fit:cover;border-radius:6px;">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $product->name ?? '-' }}</td>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->category?->name ?? '-' }}</td>
                                            <td>{{ number_format((float) $product->base_price, 2) }} {{ $product->currency }}</td>
                                            <td>
                                                <span class="badge {{ $product->status === 'active' ? 'badge-success' : ($product->status === 'draft' ? 'badge-warning' : 'badge-secondary') }}">
                                                    {{ ucfirst($product->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($product->is_featured)
                                                    <span class="badge badge-info">Featured</span>
                                                @else
                                                    <span class="badge badge-light">No</span>
                                                @endif
                                            </td>
                                            <td>{{ $product->variants_count }}</td>
                                            <td>{{ $product->all_reviews_count }}</td>
                                            <td>
                                                <a href="{{ route('dashboard.products.show', $product) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
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
                                                    <form action="{{ route('dashboard.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Move product to trash?');">
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
                                            <td colspan="12" class="text-center py-4">No products found.</td>
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
