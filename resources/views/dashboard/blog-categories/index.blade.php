@extends('dashboard.app.layout')

@section('title', 'Blog Categories')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h1>{{ __('Blog Categories') }}</h1>

                @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blog-categories.create'))
                    <a href="{{ route('dashboard.blog-categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Add Category') }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.blog-categories.index') }}">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <input
                                    type="text"
                                    name="q"
                                    class="form-control"
                                    placeholder="{{ __('Search by name or slug...') }}"
                                    value="{{ $filters['q'] }}"
                                >
                            </div>

                            <div class="col-md-3">
                                <select name="sort" class="form-control">
                                    <option value="newest" {{ $filters['sort'] === 'newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                                    <option value="oldest" {{ $filters['sort'] === 'oldest' ? 'selected' : '' }}>{{ __('Oldest') }}</option>
                                    <option value="name_asc" {{ $filters['sort'] === 'name_asc' ? 'selected' : '' }}>{{ __('Name A-Z') }}</option>
                                    <option value="name_desc" {{ $filters['sort'] === 'name_desc' ? 'selected' : '' }}>{{ __('Name Z-A') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="per_page" class="form-control">
                                    @foreach([10,15,25,50,100] as $size)
                                        <option value="{{ $size }}" {{ (int) $filters['per_page'] === $size ? 'selected' : '' }}>
                                            {{ $size }} / page
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 d-flex gap-2">
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="fas fa-search"></i> {{ __('Filter') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th style="width: 80px;">#</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th>{{ __('Blogs Count') }}</th>
                                <th>{{ __('Created At') }}</th>
                                <th style="width: 180px;" class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        <code>{{ $category->slug }}</code>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $category->blogs_count }}</span>
                                    </td>
                                    <td>{{ $category->created_at?->translatedFormat('Y-m-d h:i A') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blog-categories.edit'))
                                                <a href="{{ route('dashboard.blog-categories.edit', $category) }}"
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('blog-categories.delete'))
                                                <form action="{{ route('dashboard.blog-categories.destroy', $category) }}"
                                                      method="POST"
                                                      onsubmit="return confirm(@js(__('Are you sure you want to delete this category?')));"
                                                      style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        {{ __('No blog categories found.') }}
                                    </td>
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
