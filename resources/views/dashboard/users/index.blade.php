@extends('dashboard.app.layout')

@section('title', 'Users')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <h1>Users</h1>
            </div>
            <div class="d-flex" style="gap:10px;">
                <a href="{{ route('dashboard.users.trash') }}" class="btn btn-outline-danger">
                    <i class="fas fa-trash"></i> Trash
                </a>
                <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add User
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
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_users'] }}</h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['verified_users'] }}</h3>
                            <p>Verified Users</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['unverified_users'] }}</h3>
                            <p>Unverified Users</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $stats['users_with_phone'] }}</h3>
                            <p>Users With Phone</p>
                        </div>
                        <div class="icon"><i class="fas fa-phone"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filters</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.users.index') }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <input
                                    type="text"
                                    name="q"
                                    class="form-control"
                                    placeholder="Search by name, email, phone"
                                    value="{{ $filters['q'] ?? '' }}"
                                >
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="role" class="form-control">
                                    <option value="">All Roles</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="email_verified" class="form-control">
                                    <option value="">Email Verification</option>
                                    <option value="1" @selected(($filters['email_verified'] ?? '') === '1')>Verified</option>
                                    <option value="0" @selected(($filters['email_verified'] ?? '') === '0')>Unverified</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="has_phone" class="form-control">
                                    <option value="">Phone</option>
                                    <option value="1" @selected(($filters['has_phone'] ?? '') === '1')>Has Phone</option>
                                    <option value="0" @selected(($filters['has_phone'] ?? '') === '0')>No Phone</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Latest</option>
                                    <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Oldest</option>
                                    <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>Name ASC</option>
                                    <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>Name DESC</option>
                                    <option value="email_asc" @selected(($filters['sort'] ?? '') === 'email_asc')>Email ASC</option>
                                    <option value="email_desc" @selected(($filters['sort'] ?? '') === 'email_desc')>Email DESC</option>
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Bulk action form مستقل تمامًا --}}
            <form method="POST" action="{{ route('dashboard.users.bulk-action') }}" id="bulkActionForm">
                @csrf
            </form>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h3 class="card-title mb-2 mb-md-0">Users List</h3>

                    <div class="d-flex flex-wrap" style="gap:10px;">
                        <select name="action" class="form-control" style="width: 180px;" form="bulkActionForm">
                            <option value="">Bulk Action</option>
                            <option value="verify">Verify</option>
                            <option value="unverify">Unverify</option>
                            <option value="delete">Move to Trash</option>
                        </select>

                        <button
                            type="submit"
                            class="btn btn-dark"
                            form="bulkActionForm"
                            onclick="return confirmBulkAction()"
                        >
                            Apply
                        </button>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="check-all"></th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Roles</th>
                                <th>Verified</th>
                                <th>Addresses</th>
                                <th>Orders</th>
                                <th>Reviews</th>
                                <th>Carts</th>
                                <th>Created</th>
                                <th>Actions</th>
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
                                            <span class="badge badge-info">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-muted">No role</span>
                                        @endforelse
                                    </td>

                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge badge-success">Verified</span>
                                        @else
                                            <span class="badge badge-warning">Unverified</span>
                                        @endif
                                    </td>

                                    <td>{{ $user->addresses_count }}</td>
                                    <td>{{ $user->orders_count }}</td>
                                    <td>{{ $user->reviews_count }}</td>
                                    <td>{{ $user->carts_count }}</td>
                                    <td>{{ $user->created_at?->format('Y-m-d') }}</td>

                                    <td>
                                        <a href="{{ route('dashboard.users.show', $user) }}" class="btn btn-sm btn-info">
                                            View
                                        </a>

                                        <a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-sm btn-primary">
                                            Edit
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
                                                    onclick="return confirm('Move this user to trash?')"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center py-4">No users found.</td>
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
