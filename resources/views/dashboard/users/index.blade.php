@extends('dashboard.app.layout')

@section('title', 'Users')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <h1>{{ __('Users') }}</h1>
            </div>
            <div class="d-flex" style="gap:10px;">
                <a href="{{ route('dashboard.users.trash') }}" class="btn btn-outline-danger">
                    <i class="fas fa-trash"></i> {{ __('Trash') }}
                </a>
                <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Add User') }}
                </a>
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
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_users'] }}</h3>
                            <p>{{ __('Total Users') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-dark">
                        <div class="inner">
                            <h3>{{ $stats['owners'] }}</h3>
                            <p>{{ __('Owners') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-crown"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $stats['admins'] }}</h3>
                            <p>{{ __('Admins') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-shield"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['employees'] }}</h3>
                            <p>{{ __('Employees') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-tie"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['customers'] }}</h3>
                            <p>{{ __('Customers') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-user"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $stats['verified_users'] }}</h3>
                            <p>{{ __('Verified') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Filters') }}</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.users.index') }}">
                        <div class="row">
                            <div class="col-md-2 mb-2">
                                <input
                                    type="text"
                                    name="q"
                                    class="form-control"
                                    placeholder="{{ __('Search') }}"
                                    value="{{ $filters['q'] ?? '' }}"
                                >
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="role" class="form-control">
                                    <option value="">{{ __('All Roles') }}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" @selected(($filters['role'] ?? '') === $role->name)>
                                            {{ status_label($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="type" class="form-control">
                                    <option value="">{{ __('All Types') }}</option>
                                    @foreach(['owner','admin','employee','customer'] as $type)
                                        <option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>
                                            {{ status_label($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="email_verified" class="form-control">
                                    <option value="">{{ __('Email Verification') }}</option>
                                    <option value="1" @selected(($filters['email_verified'] ?? '') === '1')>{{ __('Verified') }}</option>
                                    <option value="0" @selected(($filters['email_verified'] ?? '') === '0')>{{ __('Unverified') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="is_active" class="form-control">
                                    <option value="">{{ __('Account Status') }}</option>
                                    <option value="1" @selected(($filters['is_active'] ?? '') === '1')>{{ __('Active') }}</option>
                                    <option value="0" @selected(($filters['is_active'] ?? '') === '0')>{{ __('Inactive') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>{{ __('Latest') }}</option>
                                    <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>{{ __('Oldest') }}</option>
                                    <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>{{ __('Name ASC') }}</option>
                                    <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>{{ __('Name DESC') }}</option>
                                    <option value="email_asc" @selected(($filters['sort'] ?? '') === 'email_asc')>{{ __('Email ASC') }}</option>
                                    <option value="email_desc" @selected(($filters['sort'] ?? '') === 'email_desc')>{{ __('Email DESC') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-2 mb-2">
                                <select name="has_phone" class="form-control">
                                    <option value="">{{ __('Phone') }}</option>
                                    <option value="1" @selected(($filters['has_phone'] ?? '') === '1')>{{ __('Has Phone') }}</option>
                                    <option value="0" @selected(($filters['has_phone'] ?? '') === '0')>{{ __('No Phone') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="per_page" class="form-control">
                                    @foreach([15, 25, 50, 100] as $size)
                                        <option value="{{ $size }}" @selected((int)($filters['per_page'] ?? 15) === $size)>
                                            {{ $size }} / page
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <button type="submit" class="btn btn-primary btn-block">{{ __('Filter') }}</button>
                            </div>

                            <div class="col-md-2 mb-2">
                                <a href="{{ route('dashboard.users.index') }}" class="btn btn-light btn-block">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.users.bulk-action') }}" id="bulkActionForm">
                @csrf
            </form>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h3 class="card-title mb-2 mb-md-0">{{ __('Users List') }}</h3>

                    <div class="d-flex flex-wrap" style="gap:10px;">
                        <select name="action" class="form-control" style="width: 180px;" form="bulkActionForm">
                            <option value="">{{ __('Bulk Action') }}</option>
                            <option value="verify">{{ __('Verify') }}</option>
                            <option value="unverify">{{ __('Unverify') }}</option>
                            <option value="delete">{{ __('Move to Trash') }}</option>
                        </select>

                        <button
                            type="submit"
                            class="btn btn-dark"
                            form="bulkActionForm"
                            onclick="return confirmBulkAction()"
                        >
                            {{ __('Apply') }}
                        </button>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="check-all"></th>
                                <th>#</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Roles') }}</th>
                                <th>{{ __('Verified') }}</th>
                                <th>{{ __('Active') }}</th>
                                <th>{{ __('Direct Permissions') }}</th>
                                <th>{{ __('Addresses') }}</th>
                                <th>{{ __('Orders') }}</th>
                                <th>{{ __('Reviews') }}</th>
                                <th>{{ __('Carts') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="ids[]"
                                            value="{{ $user->id }}"
                                            form="bulkActionForm"
                                            class="user-checkbox"
                                        >
                                    </td>

                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?: '-' }}</td>

                                    <td>
                                        @forelse($user->roles as $role)
                                            <span class="badge badge-info">{{ status_label($role->name) }}</span>
                                        @empty
                                            <span class="text-muted">{{ __('No role') }}</span>
                                        @endforelse
                                    </td>

                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge badge-success">{{ __('Verified') }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ __('Unverified') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($user->is_active)
                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            $allowCount = $user->permissions->filter(fn($permission) => ! $permission->pivot->is_denied)->count();
                                            $denyCount = $user->permissions->filter(fn($permission) => $permission->pivot->is_denied)->count();
                                        @endphp

                                        @if($allowCount > 0)
                                            <span class="badge badge-success">Allow: {{ $allowCount }}</span>
                                        @endif

                                        @if($denyCount > 0)
                                            <span class="badge badge-danger">Deny: {{ $denyCount }}</span>
                                        @endif

                                        @if($allowCount === 0 && $denyCount === 0)
                                            <span class="text-muted">{{ __('None') }}</span>
                                        @endif
                                    </td>

                                    <td>{{ $user->addresses_count }}</td>
                                    <td>{{ $user->orders_count }}</td>
                                    <td>{{ $user->reviews_count }}</td>
                                    <td>{{ $user->carts_count }}</td>
                                    <td>{{ $user->created_at?->format('Y-m-d') }}</td>

                                    <td>
                                        <a href="{{ route('dashboard.users.show', $user) }}" class="btn btn-sm btn-info">
                                            {{ __('View') }}
                                        </a>

                                        <a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-sm btn-primary">
                                            {{ __('Edit') }}
                                        </a>

                                        @php
                                            $authUser = auth()->user();
                                            $canDelete = false;

                                            if ($authUser) {
                                                if ($authUser->id !== $user->id) {
                                                    if ($authUser->hasRole('owner')) {
                                                        $canDelete = true;
                                                    } elseif (! $user->hasRole('owner') && ! $user->hasRole('admin')) {
                                                        $canDelete = true;
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if($canDelete)
                                            <form action="{{ route('dashboard.users.destroy', $user) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm(@js(__('Move this user to trash?')))"
                                                >
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-center py-4">{{ __('No users found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('check-all')?.addEventListener('change', function () {
        document.querySelectorAll('.user-checkbox').forEach((checkbox) => {
            checkbox.checked = this.checked;
        });
    });

    function confirmBulkAction() {
        const action = document.querySelector('select[name="action"][form="bulkActionForm"]')?.value;
        const checked = document.querySelectorAll('.user-checkbox:checked').length;

        if (!action) {
            alert('Please select a bulk action first.');
            return false;
        }

        if (checked === 0) {
            alert('Please select at least one user.');
            return false;
        }

        if (action === 'delete') {
            return confirm('Move selected users to trash?');
        }

        return true;
    }
</script>
@endpush
