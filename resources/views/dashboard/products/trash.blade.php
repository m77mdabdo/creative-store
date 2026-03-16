@extends('dashboard.app.layout')

@section('title', 'Trashed Products')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Trashed Products</h1>

            <a href="{{ route('dashboard.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Back to Products
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Deleted At</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->name ?? '-' }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->deleted_at?->format('Y-m-d h:i A') }}</td>
                                    <td>
                                        @if($canEdit)
                                            <form action="{{ route('dashboard.products.restore', $product->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($canDelete)
                                            <form action="{{ route('dashboard.products.force-delete', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">Trash is empty.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="card-footer clearfix">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
