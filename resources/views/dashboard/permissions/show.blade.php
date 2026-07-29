@extends('dashboard.app.layout')

@section('title', 'Permission Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Permission Details') }}</h1>
            <a href="{{ route('dashboard.permissions.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <h3>{{ __($permission->name) }}</h3>
                    <p><strong>{{ __('Code:') }}</strong> <code>{{ $permission->code }}</code></p>
                    <p><strong>{{ __('Group:') }}</strong> {{ $permission->group ?: 'general' }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ __('Roles') }}</h3></div>
                <div class="card-body">
                    @forelse($permission->roles as $role)
                        <span class="badge badge-info mb-1">{{ status_label($role->name) }}</span>
                    @empty
                        <p class="text-muted mb-0">{{ __('No roles linked.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ __('Users with Direct Permission') }}</h3></div>
                <div class="card-body">
                    @forelse($permission->users as $user)
                        <div class="border rounded p-2 mb-2">
                            <strong>{{ $user->name }}</strong><br>
                            <small class="text-muted">{{ $user->email }}</small>
                            @if(isset($user->pivot) && $user->pivot->is_denied)
                                <span class="badge badge-danger">{{ __('Denied') }}</span>
                            @else
                                <span class="badge badge-success">{{ __('Allowed') }}</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No direct users linked.') }}</p>
                    @endforelse
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
