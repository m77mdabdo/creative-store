@extends('dashboard.app.layout')

@section('title', 'Categories')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Categories</h1>
                @if(auth()->user()->hasPermission('categories.create') || auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin'))
                    <a href="{{ route('dashboard.categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Add Category
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
                    <h3 class="card-title">Filter Categories</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.categories.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control"
                                       value="{{ request('search') }}"
                                       placeholder="Name, slug, description...">
                            </div>

                            <div class="col-md-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Parent</label>
                                <select name="parent" class="form-control">
                                    <option value="">All</option>
                                    <option value="root" {{ request('parent') === 'root' ? 'selected' : '' }}>Root Only</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ request('parent') == $parent->id ? 'selected' : '' }}>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Categories List</h3>
                </div>

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
                                @php
                                    $ar = $category->translations->firstWhere('locale', 'ar');
                                    $en = $category->translations->firstWhere('locale', 'en');
                                @endphp
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $ar?->name ?? '-' }}</td>
                                    <td>{{ $en?->name ?? '-' }}</td>
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

                                        @if(auth()->user()->hasPermission('categories.edit') || auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin'))
                                            <a href="{{ route('dashboard.categories.edit', $category) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('dashboard.categories.toggle-status', $category) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if(auth()->user()->hasPermission('categories.delete') || auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin'))
                                            <form action="{{ route('dashboard.categories.destroy', $category) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this category?');">
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
        </div>
    </section>
</div>
@endsection
