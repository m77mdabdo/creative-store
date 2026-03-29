@extends('dashboard.app.layout')

@section('title', 'Permission Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Permission Details</h1>
            <a href="{{ route('dashboard.permissions.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <h3>{{ $permission->name }}</h3>
                    <p><strong>Code:</strong> <code>{{ $permission->code }}</code></p>
                    <p><strong>Group:</strong> {{ $permission->group ?: 'general' }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Roles</h3></div>
                <div class="card-body">
                    @forelse($permission->roles as $role)
                        <span class="badge badge-info mb-1">{{ $role->name }}</span>
                    @empty
                        <p class="text-muted mb-0">No roles linked.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Users with Direct Permission</h3></div>
                <div class="card-body">
                    @forelse($permission->users as $user)
                        <div class="border rounded p-2 mb-2">
                            <strong>{{ $user->name }}</strong><br>
                            <small class="text-muted">{{ $user->email }}</small>
                            @if(isset($user->pivot) && $user->pivot->is_denied)
                                <span class="badge badge-danger">Denied</span>
                            @else
                                <span class="badge badge-success">Allowed</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No direct users linked.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
