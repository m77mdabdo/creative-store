@extends('dashboard.app.layout')

@section('title', 'Attribute Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1>Attribute - {{ $attribute->name }}</h1>

            <div>
                <a href="{{ route('dashboard.attributes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>

                @if($canEdit)
                    <a href="{{ route('dashboard.attributes.edit', $attribute) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>

                    <a href="{{ route('dashboard.attributes.values.create', $attribute) }}" class="btn btn-success">
                        <i class="fas fa-plus mr-1"></i> Add Value
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Attribute Info</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>ID:</strong> {{ $attribute->id }}</p>
                            <p><strong>Name:</strong> {{ $attribute->name }}</p>
                            <p><strong>Slug:</strong> {{ $attribute->slug }}</p>
                            <p>
                                <strong>Status:</strong>
                                @if($attribute->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </p>
                            <p><strong>Values Count:</strong> {{ $attribute->values_count }}</p>
                            <p><strong>Products Count:</strong> {{ $attribute->products_count }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Attribute Values</h3>

                            @if($canEdit)
                                <a href="{{ route('dashboard.attributes.values.create', $attribute) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus mr-1"></i> Add Value
                                </a>
                            @endif
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Value</th>
                                        <th>Slug</th>
                                        <th>Sort</th>
                                        <th>Status</th>
                                        <th width="220">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attribute->values as $value)
                                        <tr>
                                            <td>{{ $value->id }}</td>
                                            <td>{{ $value->value }}</td>
                                            <td>{{ $value->slug }}</td>
                                            <td>{{ $value->sort_order }}</td>
                                            <td>
                                                @if($value->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($canEdit)
                                                    <a href="{{ route('dashboard.attributes.values.edit', [$attribute, $value]) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('dashboard.attributes.values.toggle-status', [$attribute, $value]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($canDelete)
                                                    <form action="{{ route('dashboard.attributes.values.destroy', [$attribute, $value]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this value?');">
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
                                            <td colspan="6" class="text-center py-4">No values found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($attribute->products->count())
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Linked Products</h3>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    @foreach($attribute->products as $product)
                                        <li>{{ $product->name ?? $product->sku }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
