@extends('dashboard.app.layout')

@section('title', 'Roles')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Roles') }}</h1>
            @if(auth()->user()->hasPermission('roles.create'))
                <a href="{{ route('dashboard.roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Add Role') }}
                </a>
            @endif
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

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_roles'] }}</h3>
                            <p>{{ __('Total Roles') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-tag"></i></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-dark">
                        <div class="inner">
                            <h3>{{ $stats['system_roles'] }}</h3>
                            <p>{{ __('System Roles') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-shield-alt"></i></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['custom_roles'] }}</h3>
                            <p>{{ __('Custom Roles') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-layer-group"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.roles.index') }}">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <input
                                    type="text"
                                    name="q"
                                    class="form-control"
                                    placeholder="{{ __('Search role...') }}"
                                    value="{{ $filters['q'] ?? '' }}"
                                >
                            </div>

                            <div class="col-md-3 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="name_asc" @selected(($filters['sort'] ?? 'name_asc') === 'name_asc')>{{ __('Name ASC') }}</option>
                                    <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>{{ __('Name DESC') }}</option>
                                    <option value="users_count_desc" @selected(($filters['sort'] ?? '') === 'users_count_desc')>{{ __('Users Count DESC') }}</option>
                                    <option value="users_count_asc" @selected(($filters['sort'] ?? '') === 'users_count_asc')>{{ __('Users Count ASC') }}</option>
                                    <option value="latest" @selected(($filters['sort'] ?? '') === 'latest')>{{ __('Latest') }}</option>
                                    <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>{{ __('Oldest') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="per_page" class="form-control">
                                    @foreach([15,25,50,100] as $size)
                                        <option value="{{ $size }}" @selected((int)($filters['per_page'] ?? 15) === $size)>
                                            {{ $size }} / page
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <button class="btn btn-primary btn-block">{{ __('Filter') }}</button>
                            </div>

                            <div class="col-md-2 mb-2">
                                <a href="{{ route('dashboard.roles.index') }}" class="btn btn-light btn-block">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Permissions') }}</th>
                                <th>{{ __('Users') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                @php
                                    $isSystem = in_array($role->name, ['owner', 'admin', 'employee', 'customer'], true);
                                @endphp

                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ status_label($role->name) }}</td>
                                    <td>{{ $role->permissions_count }}</td>
                                    <td>{{ $role->users_count }}</td>
                                    <td>
                                        @if($isSystem)
                                            <span class="badge badge-dark">{{ __('System') }}</span>
                                        @else
                                            <span class="badge badge-success">{{ __('Custom') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.roles.show', $role) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>

                                        @if(auth()->user()->hasPermission('roles.edit'))
                                            <a href="{{ route('dashboard.roles.edit', $role) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                        @endif

                                        @if(auth()->user()->hasPermission('roles.delete') && ! $isSystem)
                                            <form action="{{ route('dashboard.roles.destroy', $role) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm(@js(__('Delete this role?')))"
                                                >
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">{{ __('No roles found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
