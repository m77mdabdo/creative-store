@extends('dashboard.app.layout')

@section('title', 'Product Reviews')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-2">Product Reviews</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filter Reviews</h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.reviews.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="Title, comment, customer, product...">
                            </div>

                            <div class="col-md-3">
                                <label>Product</label>
                                <select name="product_id" class="form-control">
                                    <option value="">All</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ (string) $filters['product_id'] === (string) $product->id ? 'selected' : '' }}>
                                            {{ $product->name ?? $product->sku }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Rating</label>
                                <select name="rating" class="form-control">
                                    <option value="">All</option>
                                    @for($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" {{ (string) $filters['rating'] === (string) $i ? 'selected' : '' }}>
                                            {{ $i }} Star
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="approved" {{ $filters['status'] === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-info btn-block" type="submit">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($canEdit && $reviews->count())
                <div class="card card-outline card-secondary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2">Bulk Actions</h3>

                        <form id="bulkReviewsForm" action="{{ route('dashboard.reviews.bulk-action') }}" method="POST" class="d-flex align-items-center">
                            @csrf
                            <select name="action" class="form-control mr-2" required>
                                <option value="">Select action</option>
                                <option value="approve">Approve</option>
                                <option value="unapprove">Unapprove</option>
                                <option value="delete">Delete</option>
                            </select>

                            <button type="submit" class="btn btn-warning">Apply</button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                @if($canEdit)
                                    <th><input type="checkbox" id="check-all-reviews"></th>
                                @endif
                                <th>#</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Rating</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th width="220">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr>
                                    @if($canEdit)
                                        <td>
                                            <input type="checkbox" name="ids[]" value="{{ $review->id }}" class="review-checkbox" form="bulkReviewsForm">
                                        </td>
                                    @endif
                                    <td>{{ $review->id }}</td>
                                    <td>{{ $review->product?->name ?? $review->product?->sku ?? '-' }}</td>
                                    <td>{{ $review->user?->name ?? 'Guest / Deleted User' }}</td>
                                    <td>
                                        <span class="badge badge-warning">{{ $review->rating }}/5</span>
                                    </td>
                                    <td>{{ $review->title ?: '-' }}</td>
                                    <td>
                                        @if($review->is_approved)
                                            <span class="badge badge-success">Approved</span>
                                        @else
                                            <span class="badge badge-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $review->created_at?->format('Y-m-d h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.reviews.show', $review) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if($canEdit)
                                            <form action="{{ route('dashboard.reviews.toggle-status', $review) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($canDelete)
                                            <form action="{{ route('dashboard.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this review?');">
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
                                    <td colspan="{{ $canEdit ? 9 : 8 }}" class="text-center py-4">No reviews found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reviews->hasPages())
                    <div class="card-footer clearfix">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('check-all-reviews');
    const boxes = document.querySelectorAll('.review-checkbox');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            boxes.forEach(box => box.checked = checkAll.checked);
        });
    }
});
</script>
@endpush
@endsection
