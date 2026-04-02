@extends('dashboard.app.layout')

@section('title', 'Categories')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h1 class="mb-2">Categories</h1>

                <div class="mb-2">
                    @if($canViewTrash)
                        <a href="{{ route('dashboard.categories.trash') }}" class="btn btn-dark">
                            <i class="fas fa-trash mr-1"></i> Trash
                        </a>
                    @endif

                    @if($canCreate)
                        <a href="{{ route('dashboard.categories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Add Category
                        </a>
                    @endif
                </div>
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
                    <h3 class="card-title">Filter Categories</h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.categories.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Search</label>
                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    value="{{ $filters['search'] }}"
                                    placeholder="Name, slug, description..."
                                >
                            </div>

                            <div class="col-md-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Parent</label>
                                <select name="parent" class="form-control">
                                    <option value="">All</option>
                                    <option value="root" {{ $filters['parent'] === 'root' ? 'selected' : '' }}>Root Only</option>

                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ (string) $filters['parent'] === (string) $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name ?? 'No Name' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-info mr-2" type="submit">
                                    <i class="fas fa-search"></i> Filter
                                </button>

                                <a href="{{ route('dashboard.categories.index') }}" class="btn btn-secondary">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($canEdit && $categories->count())
                <div class="card card-outline card-secondary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2">Bulk Actions</h3>

                        <form id="bulkActionForm" action="{{ route('dashboard.categories.bulk-action') }}" method="POST" class="d-flex align-items-center">
                            @csrf

                            <select name="action" class="form-control mr-2" required>
                                <option value="">Select action</option>
                                <option value="activate">Activate</option>
                                <option value="deactivate">Deactivate</option>
                                <option value="delete">Move to Trash</option>
                            </select>

                            <button type="submit" class="btn btn-warning">
                                Apply
                            </button>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="check-all">
                                        </th>
                                        <th>#</th>
                                        <th>Name (AR)</th>
                                        <th>Name (EN)</th>
                                        <th>Slug</th>
                                        <th>Parent</th>
                                        <th>Children</th>
                                        <th>Products</th>
                                        <th>Sort</th>
                                        <th>Status</th>
                                        <th width="240">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($categories as $category)
                                        <tr>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    name="ids[]"
                                                    value="{{ $category->id }}"
                                                    class="row-checkbox"
                                                    form="bulkActionForm"
                                                >
                                            </td>
                                            <td>{{ $category->id }}</td>
                                            <td>{{ $category->translate('ar')?->name ?? '-' }}</td>
                                            <td>{{ $category->translate('en')?->name ?? '-' }}</td>
                                            <td>{{ $category->slug }}</td>
                                            <td>{{ $category->parent?->name ?? 'Root' }}</td>
                                            <td>{{ $category->children_count }}</td>
                                            <td>{{ $category->products_count }}</td>
                                            <td>{{ $category->sort_order }}</td>
                                            <td>
                                                @if($category->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('dashboard.categories.show', $category) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($canEdit)
                                                    <a href="{{ route('dashboard.categories.edit', $category) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('dashboard.categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($canDelete)
                                                    <form
                                                        action="{{ route('dashboard.categories.destroy', $category) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to move this category to trash?');"
                                                    >
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
                                            <td colspan="11" class="text-center py-4">No categories found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($categories->hasPages())
                        <div class="card-footer clearfix">
                            {{ $categories->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name (AR)</th>
                                    <th>Name (EN)</th>
                                    <th>Slug</th>
                                    <th>Parent</th>
                                    <th>Children</th>
                                    <th>Products</th>
                                    <th>Sort</th>
                                    <th>Status</th>
                                    <th width="220">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>{{ $category->translate('ar')?->name ?? '-' }}</td>
                                        <td>{{ $category->translate('en')?->name ?? '-' }}</td>
                                        <td>{{ $category->slug }}</td>
                                        <td>{{ $category->parent?->name ?? 'Root' }}</td>
                                        <td>{{ $category->children_count }}</td>
                                        <td>{{ $category->products_count }}</td>
                                        <td>{{ $category->sort_order }}</td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('dashboard.categories.show', $category) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">No categories found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($categories->hasPages())
                        <div class="card-footer clearfix">
                            {{ $categories->links() }}
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
        const checkAll = document.getElementById('check-all');
        const boxes = document.querySelectorAll('.row-checkbox');

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                boxes.forEach(box => {
                    box.checked = checkAll.checked;
                });
            });
        }
    });
</script>
@endpush
@endsection
