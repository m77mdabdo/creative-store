@extends('dashboard.app.layout')

@section('title', __('Role Details'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Role Details') }}</h1>
            <div>
                <a href="{{ route('dashboard.roles.edit', $role) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                <a href="{{ route('dashboard.roles.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-4">
                    <div class="card card-outline card-primary">
                        <div class="card-body">
                            <h3>{{ status_label($role->name) }}</h3>
                            <p class="mb-2">
                                <span class="badge badge-info">Users: {{ $role->users_count }}</span>
                                <span class="badge badge-success">Permissions: {{ $role->permissions_count }}</span>
                            </p>

                            @if(in_array($role->name, ['owner', 'admin', 'employee', 'customer'], true))
                                <span class="badge badge-dark">{{ __('System Role') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('Custom Role') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h3 class="card-title">{{ __('Assigned Users') }}</h3></div>
                        <div class="card-body">
                            @forelse($role->users as $user)
                                <div class="border rounded p-2 mb-2">
                                    <strong>{{ $user->name }}</strong><br>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('No users assigned.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    @foreach($permissionsGrouped as $group => $permissions)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title text-capitalize">{{ status_label($group) }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-6 mb-2">
                                            <div class="border rounded p-2">
                                                <strong>{{ __($permission->name) }}</strong><br>
                                                <small class="text-muted">{{ $permission->code }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
