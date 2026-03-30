@extends('dashboard.app.layout')

@section('title', 'Audit Logs')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="mb-1">Audit Logs</h1>
                    <p class="text-muted mb-0">Track system activities, changes, and user actions.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.audit.export', request()->query()) }}" class="btn btn-success mr-2">
                        <i class="fas fa-file-csv mr-1"></i> Export CSV
                    </a>

                    <a href="{{ route('dashboard.audit.print', request()->query()) }}" target="_blank" class="btn btn-dark">
                        <i class="fas fa-print mr-1"></i> Print
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filters</h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.audit.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Search</label>
                                    <input
                                        type="text"
                                        name="q"
                                        value="{{ $filters['q'] }}"
                                        class="form-control"
                                        placeholder="ID, action, user, email, type, IP..."
                                    >
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>User</label>
                                    <select name="user_id" class="form-control">
                                        <option value="">All</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" @selected((string) $filters['user_id'] === (string) $user->id)>
                                                {{ $user->name }}{{ $user->email ? ' (' . $user->email . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Action</label>
                                    <select name="action" class="form-control">
                                        <option value="">All</option>
                                        @foreach($actions as $action)
                                            <option value="{{ $action }}" @selected($filters['action'] === $action)>
                                                {{ $action }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Auditable Type</label>
                                    <select name="auditable_type" class="form-control">
                                        <option value="">All</option>
                                        @foreach($auditableTypes as $auditableType)
                                            <option value="{{ $auditableType }}" @selected($filters['auditable_type'] === $auditableType)>
                                                {{ class_basename($auditableType) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Auditable ID</label>
                                    <input
                                        type="number"
                                        name="auditable_id"
                                        value="{{ $filters['auditable_id'] }}"
                                        class="form-control"
                                        placeholder="Auditable ID"
                                    >
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>IP Address</label>
                                    <input
                                        type="text"
                                        name="ip_address"
                                        value="{{ $filters['ip_address'] }}"
                                        class="form-control"
                                        placeholder="IP Address"
                                    >
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input
                                        type="date"
                                        name="date_from"
                                        value="{{ $filters['date_from'] }}"
                                        class="form-control"
                                    >
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input
                                        type="date"
                                        name="date_to"
                                        value="{{ $filters['date_to'] }}"
                                        class="form-control"
                                    >
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Per Page</label>
                                    <select name="per_page" class="form-control">
                                        @foreach([10, 20, 30, 50, 100] as $perPage)
                                            <option value="{{ $perPage }}" @selected((int) $filters['per_page'] === $perPage)>
                                                {{ $perPage }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-search mr-1"></i> Apply Filters
                            </button>

                            <a href="{{ route('dashboard.audit.index') }}" class="btn btn-default">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Audit Logs</h3>
                </div>

                <div class="card-body p-0">
                    @if($auditLogs->count())
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Auditable Type</th>
                                        <th>Auditable ID</th>
                                        <th>IP Address</th>
                                        <th>Changes</th>
                                        <th>Created At</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($auditLogs as $auditLog)
                                        <tr>
                                            <td>{{ $auditLog->id }}</td>

                                            <td>
                                                @if($auditLog->user)
                                                    <div class="font-weight-bold">{{ $auditLog->user->name }}</div>
                                                    <small class="text-muted">{{ $auditLog->user->email }}</small>
                                                @else
                                                    <span class="text-muted">System / Unknown</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="badge badge-info">{{ $auditLog->action }}</span>
                                            </td>

                                            <td>
                                                {{ class_basename($auditLog->auditable_type) }}
                                                <br>
                                                <small class="text-muted">{{ $auditLog->auditable_type }}</small>
                                            </td>

                                            <td>{{ $auditLog->auditable_id }}</td>

                                            <td>{{ $auditLog->ip_address ?: '—' }}</td>

                                            <td>
                                                @php
                                                    $oldCount = is_array($auditLog->old_values) ? count($auditLog->old_values) : 0;
                                                    $newCount = is_array($auditLog->new_values) ? count($auditLog->new_values) : 0;
                                                    $changesCount = max($oldCount, $newCount);
                                                @endphp

                                                @if($changesCount > 0)
                                                    <span class="badge badge-warning">{{ $changesCount }} field(s)</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div>{{ optional($auditLog->created_at)->format('Y-m-d') }}</div>
                                                <small class="text-muted">{{ optional($auditLog->created_at)->format('h:i A') }}</small>
                                            </td>

                                            <td class="text-right">
                                                <a href="{{ route('dashboard.audit.show', $auditLog) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-history fa-2x mb-3 d-block"></i>
                            No audit logs found.
                        </div>
                    @endif
                </div>

                @if($auditLogs->hasPages())
                    <div class="card-footer clearfix">
                        {{ $auditLogs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
