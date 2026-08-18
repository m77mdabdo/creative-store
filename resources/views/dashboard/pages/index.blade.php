@extends('dashboard.app.layout')

@section('title', __('Pages'))

@section('content')
@php
    $filters = $filters ?? [
        'q' => '',
        'status' => '',
        'sort' => 'newest',
        'per_page' => 15,
    ];
@endphp

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1>{{ __('Pages') }}</h1>

            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('pages.create'))
                <a href="{{ route('dashboard.pages.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Add Page') }}
                </a>
            @endif
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.pages.index') }}">
                        <div class="row">
                            <div class="col-md-5 mb-2">
                                <input
                                    type="text"
                                    name="q"
                                    class="form-control"
                                    placeholder="{{ __('Search by slug or title...') }}"
                                    value="{{ $filters['q'] ?? '' }}"
                                >
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('Status: All') }}</option>
                                    <option value="1" @selected(($filters['status'] ?? '') === '1')>{{ __('Active') }}</option>
                                    <option value="0" @selected(($filters['status'] ?? '') === '0')>{{ __('Inactive') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>{{ __('Newest') }}</option>
                                    <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>{{ __('Oldest') }}</option>
                                    <option value="slug_asc" @selected(($filters['sort'] ?? '') === 'slug_asc')>{{ __('Slug A-Z') }}</option>
                                    <option value="slug_desc" @selected(($filters['sort'] ?? '') === 'slug_desc')>{{ __('Slug Z-A') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="per_page" class="form-control">
                                    @foreach([10,15,25,50,100] as $size)
                                        <option value="{{ $size }}" @selected((int) ($filters['per_page'] ?? 15) === $size)>{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Slug') }}</th>
                                <th>{{ __('Arabic Title') }}</th>
                                <th>{{ __('English Title') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created At') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                                @php
                                    $ar = $page->translations->firstWhere('locale', 'ar');
                                    $en = $page->translations->firstWhere('locale', 'en');
                                @endphp

                                <tr>
                                    <td>{{ $page->id }}</td>
                                    <td><code>{{ $page->slug }}</code></td>
                                    <td>{{ $ar?->title ?? '-' }}</td>
                                    <td>{{ $en?->title ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $page->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $page->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td>{{ $page->created_at?->translatedFormat('Y-m-d h:i A') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('pages.view'))
                                                <a href="{{ route('dashboard.pages.show', $page) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif

                                            @if(Route::has('frontend.pages.show'))
                                                <a href="{{ route('frontend.pages.show', $page->slug) }}" target="_blank" class="btn btn-sm btn-success" title="{{ __('Open Frontend') }}">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('pages.edit'))
                                                <a href="{{ route('dashboard.pages.edit', $page) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('pages.edit'))
                                                <form action="{{ route('dashboard.pages.toggle-status', $page) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('Toggle Status') }}">
                                                        <i class="fas fa-power-off"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('pages.delete'))
                                                <form action="{{ route('dashboard.pages.destroy', $page) }}"
                                                      method="POST"
                                                      style="display:inline-block;"
                                                      onsubmit="return confirm(@js(__('Are you sure you want to delete this page?')));">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">{{ __('No pages found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pages->hasPages())
                    <div class="card-footer clearfix">
                        {{ $pages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
