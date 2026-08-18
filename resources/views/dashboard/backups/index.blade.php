@extends('dashboard.app.layout')

@section('title', __('System Backups'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('System Backups') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.home') }}">{{ __('Dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('System Backups') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($stats['total'] ?? 0) }}</h3>
                            <p>{{ __('Total Backups') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['success'] ?? 0) }}</h3>
                            <p>{{ __('Successful') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($stats['failed'] ?? 0) }}</h3>
                            <p>{{ __('Failed') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['scheduled'] ?? 0) }}</h3>
                            <p>{{ __('Scheduled') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Filters') }}</h3>

                    <div class="card-tools">
                        @if(auth()->check())
                            <form action="{{ route('dashboard.backups.create') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i>
                                    {{ __('Create Manual Backup') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.backups.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="q">{{ __('Search') }}</label>
                                    <input
                                        type="text"
                                        name="q"
                                        id="q"
                                        class="form-control"
                                        value="{{ $filters['q'] ?? '' }}"
                                        placeholder="{{ __('ID, file path, type, status, note...') }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="type">{{ __('Type') }}</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="manual" @selected(($filters['type'] ?? '') === 'manual')>{{ __('Manual') }}</option>
                                        <option value="scheduled" @selected(($filters['type'] ?? '') === 'scheduled')>{{ __('Scheduled') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="success" @selected(($filters['status'] ?? '') === 'success')>{{ __('Success') }}</option>
                                        <option value="failed" @selected(($filters['status'] ?? '') === 'failed')>{{ __('Failed') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_from">{{ __('Date From') }}</label>
                                    <input
                                        type="date"
                                        name="date_from"
                                        id="date_from"
                                        class="form-control"
                                        value="{{ $filters['date_from'] ?? '' }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_to">{{ __('Date To') }}</label>
                                    <input
                                        type="date"
                                        name="date_to"
                                        id="date_to"
                                        class="form-control"
                                        value="{{ $filters['date_to'] ?? '' }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="per_page">{{ __('Rows') }}</label>
                                    <select name="per_page" id="per_page" class="form-control">
                                        @foreach([10, 20, 30, 50, 100] as $size)
                                            <option value="{{ $size }}" @selected((int) ($filters['per_page'] ?? 20) === $size)>
                                                {{ $size }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center">
                            <button type="submit" class="btn btn-primary mr-2 mb-2">
                                <i class="fas fa-search mr-1"></i>
                                {{ __('Filter') }}
                            </button>

                            <a href="{{ route('dashboard.backups.index') }}" class="btn btn-secondary mb-2">
                                <i class="fas fa-undo mr-1"></i>
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.backups.bulk-delete') }}" id="bulkDeleteForm">
                @csrf
                @method('DELETE')

                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Backup History') }}</h3>

                        <div class="card-tools">
                            @if(auth()->check())
                                <button type="submit" class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>
                                    <i class="fas fa-trash mr-1"></i>
                                    {{ __('Delete Selected') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    @if(auth()->check())
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="checkAll">
                                        </th>
                                    @endif
                                    <th>ID</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('File Path') }}</th>
                                    <th>{{ __('Size') }}</th>
                                    <th>{{ __('Started At') }}</th>
                                    <th>{{ __('Finished At') }}</th>
                                    <th>{{ __('Note') }}</th>
                                    <th style="width: 220px;">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $backup)
                                    <tr>
                                        @if(auth()->check())
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    name="ids[]"
                                                    value="{{ $backup->id }}"
                                                    class="row-checkbox"
                                                >
                                            </td>
                                        @endif

                                        <td>#{{ $backup->id }}</td>

                                        <td>
                                            @if($backup->type === 'manual')
                                                <span class="badge badge-info">{{ __('Manual') }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ __('Scheduled') }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($backup->status === 'success')
                                                <span class="badge badge-success">{{ __('Success') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Failed') }}</span>
                                            @endif
                                        </td>

                                        <td style="max-width: 360px;">
                                            <div class="text-wrap">{{ $backup->file_path ?: '—' }}</div>
                                        </td>

                                        <td>{{ $backup->human_size }}</td>

                                        <td>
                                            @if($backup->started_at)
                                                {{ $backup->started_at->format('Y-m-d') }}
                                                <br>
                                                <small class="text-muted">{{ $backup->started_at->translatedFormat('h:i A') }}</small>
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td>
                                            @if($backup->finished_at)
                                                {{ $backup->finished_at->format('Y-m-d') }}
                                                <br>
                                                <small class="text-muted">{{ $backup->finished_at->translatedFormat('h:i A') }}</small>
                                            @else
                                                <span class="text-muted">{{ __('In Progress / Not finished') }}</span>
                                            @endif
                                        </td>

                                        <td style="max-width: 280px;">
                                            <div class="text-wrap">{{ $backup->note ?: '—' }}</div>
                                        </td>

                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dashboard.backups.show', $backup) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($backup->status === 'success' && $backup->file_exists)
                                                    <a href="{{ route('dashboard.backups.download', $backup) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif

                                                @if(auth()->check())
                                                    <form
                                                        action="{{ route('dashboard.backups.destroy', $backup) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm(@js(__('Are you sure you want to delete this backup record and file?')));"
                                                    >
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->check() ? 10 : 9 }}" class="text-center text-muted py-4">
                                            {{ __('No backup records found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($backups->hasPages())
                        <div class="card-footer clearfix">
                            <div class="float-right">
                                {{ $backups->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </form>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');

        function refreshBulkDeleteState() {
            if (!bulkDeleteBtn) return;

            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            bulkDeleteBtn.disabled = checkedCount === 0;
        }

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                rowCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = checkAll.checked;
                });

                refreshBulkDeleteState();
            });
        }

        rowCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                if (!this.checked && checkAll) {
                    checkAll.checked = false;
                }

                if (checkAll) {
                    const allChecked = rowCheckboxes.length > 0
                        && Array.from(rowCheckboxes).every(item => item.checked);

                    checkAll.checked = allChecked;
                }

                refreshBulkDeleteState();
            });
        });

        if (bulkDeleteForm) {
            bulkDeleteForm.addEventListener('submit', function (e) {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;

                if (checkedCount === 0) {
                    e.preventDefault();
                    return;
                }

                const confirmed = confirm('Are you sure you want to delete the selected backup records and files?');

                if (!confirmed) {
                    e.preventDefault();
                }
            });
        }

        refreshBulkDeleteState();
    });
</script>
@endpush
