@extends('dashboard.app.layout')

@section('title', 'Trashed Categories')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Trashed Categories') }}</h1>

            <a href="{{ route('dashboard.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Categories') }}
            </a>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Trash') }}</h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Name (AR)') }}</th>
                                <th>{{ __('Name (EN)') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th>{{ __('Deleted At') }}</th>
                                <th width="180">{{ __('Actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->translate('ar')?->name ?? '-' }}</td>
                                    <td>{{ $category->translate('en')?->name ?? '-' }}</td>
                                    <td>{{ $category->slug }}</td>
                                    <td>{{ $category->deleted_at?->translatedFormat('Y-m-d h:i A') }}</td>
                                    <td>
                                        @if($canEdit)
                                            <form action="{{ route('dashboard.categories.restore', $category->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($canDelete)
                                            <form
                                                action="{{ route('dashboard.categories.force-delete', $category->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm(@js(__('Are you sure you want to permanently delete this category?')));"
                                            >
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
                                    <td colspan="6" class="text-center py-4">{{ __('Trash is empty.') }}</td>
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
