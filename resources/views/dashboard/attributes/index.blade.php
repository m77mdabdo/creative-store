@extends('dashboard.app.layout')

@section('title', 'Attributes')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-2">{{ __('Attributes') }}</h1>

            @if($canCreate)
                <a href="{{ route('dashboard.attributes.create') }}" class="btn btn-primary mb-2">
                    <i class="fas fa-plus mr-1"></i> {{ __('Add Attribute') }}
                </a>
            @endif
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Filter Attributes') }}</h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.attributes.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label>{{ __('Search') }}</label>
                                <input type="text" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="{{ __('Name or slug') }}">
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-info" type="submit">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Values') }}</th>
                                <th>{{ __('Products') }}</th>
                                <th width="260">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attributes as $attribute)
                                <tr>
                                    <td>{{ $attribute->id }}</td>
                                    <td>{{ $attribute->name }}</td>
                                    <td>{{ $attribute->slug }}</td>
                                    <td>
                                        @if($attribute->is_active)
                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $attribute->values_count }}</td>
                                    <td>{{ $attribute->products_count }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.attributes.show', $attribute) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if($canEdit)
                                            <a href="{{ route('dashboard.attributes.edit', $attribute) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('dashboard.attributes.toggle-status', $attribute) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($canDelete)
                                            <form action="{{ route('dashboard.attributes.destroy', $attribute) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Delete this attribute?')));">
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
                                    <td colspan="7" class="text-center py-4">{{ __('No attributes found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($attributes->hasPages())
                    <div class="card-footer clearfix">
                        {{ $attributes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
