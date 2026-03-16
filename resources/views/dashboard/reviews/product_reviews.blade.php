@extends('dashboard.app.layout')

@section('title', 'Product Reviews')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1>Reviews - {{ $product->name ?? $product->sku }}</h1>

            <div>
                <a href="{{ route('dashboard.products.show', $product) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>

                <a href="{{ route('dashboard.reviews.index') }}" class="btn btn-dark">
                    <i class="fas fa-comments mr-1"></i> All Reviews
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
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
                                    <td>{{ $review->id }}</td>
                                    <td>{{ $review->user?->name ?? 'Guest / Deleted User' }}</td>
                                    <td><span class="badge badge-warning">{{ $review->rating }}/5</span></td>
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
                                    <td colspan="7" class="text-center py-4">No reviews for this product.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
