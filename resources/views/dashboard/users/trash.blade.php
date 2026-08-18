@extends('dashboard.app.layout')

@section('title', __('Users Trash'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Users Trash') }}</h1>
            <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
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
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Roles') }}</th>
                            <th>{{ __('Deleted At') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @forelse($user->roles as $role)
                                        <span class="badge badge-info">{{ status_label($role->name) }}</span>
                                    @empty
                                        <span class="text-muted">{{ __('No role') }}</span>
                                    @endforelse
                                </td>
                                <td>{{ $user->deleted_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <form action="{{ route('dashboard.users.restore', $user->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success">{{ __('Restore') }}</button>
                                    </form>

                                    <form action="{{ route('dashboard.users.force-delete', $user->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm(@js(__('Delete permanently?')))">
                                            {{ __('Delete Permanently') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">{{ __('Trash is empty.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
