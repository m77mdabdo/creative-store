@extends('dashboard.app.layout')

@section('title', 'Error Logs')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="mb-1">{{ __('Error Logs') }}</h1>
                    <p class="text-muted mb-0">{{ __('Track system errors, exceptions, and operational issues.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total'] }}</h3>
                            <p>{{ __('Total Logs') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-bug"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stats['critical'] }}</h3>
                            <p>{{ __('Critical Logs') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-radiation"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['unresolved'] }}</h3>
                            <p>{{ __('Unresolved') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['resolved'] }}</h3>
                            <p>{{ __('Resolved') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Filters') }}</h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.error-logs.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('Search') }}</label>
                                    <input
                                        type="text"
                                        name="q"
                                        class="form-control"
                                        value="{{ $filters['q'] }}"
                                        placeholder="{{ __('ID, message, level, service...') }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ __('Level') }}</label>
                                    <select name="level" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        @foreach($levels as $level)
                                            <option value="{{ $level }}" @selected($filters['level'] === $level)>
                                                {{ strtoupper($level) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ __('Service') }}</label>
                                    <select name="service" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service }}" @selected($filters['service'] === $service)>
                                                {{ $service }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ __('Resolved') }}</label>
                                    <select name="resolved" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="no" @selected($filters['resolved'] === 'no')>{{ __('Unresolved') }}</option>
                                        <option value="yes" @selected($filters['resolved'] === 'yes')>{{ __('Resolved') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ __('Date From') }}</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ __('Date To') }}</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group form-check mt-4 pt-2">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="search_trace"
                                        name="search_trace"
                                        value="1"
                                        @checked((string) $filters['search_trace'] === '1')
                                    >
                                    <label class="form-check-label" for="search_trace">{{ __('Search in trace') }}</label>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>{{ __('Per Page') }}</label>
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

                        <div class="d-flex flex-wrap">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-search mr-1"></i> {{ __('Apply Filters') }}
                            </button>

                            <a href="{{ route('dashboard.error-logs.index') }}" class="btn btn-default mr-2">
                                <i class="fas fa-undo mr-1"></i> {{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <form id="bulk-action-form" method="POST">
                @csrf
                @method('PATCH')

                <div class="card card-outline card-secondary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-0">{{ __('Error Logs') }}</h3>

                        <div class="d-flex flex-wrap">
                            <button type="button" class="btn btn-success btn-sm mr-2" onclick="submitBulkResolve()">
                                <i class="fas fa-check mr-1"></i> {{ __('Resolve Selected') }}
                            </button>

                            <button type="button" class="btn btn-danger btn-sm mr-2" onclick="submitBulkDelete()">
                                <i class="fas fa-trash mr-1"></i> {{ __('Delete Selected') }}
                            </button>

                            <form action="{{ route('dashboard.error-logs.clear-resolved') }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Are you sure you want to clear all resolved error logs?')));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-broom mr-1"></i> {{ __('Clear Resolved') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if($errorLogs->count())
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;">
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>#</th>
                                            <th>{{ __('Level') }}</th>
                                            <th>{{ __('Service') }}</th>
                                            <th>{{ __('Message') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Created At') }}</th>
                                            <th class="text-right">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($errorLogs as $errorLog)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="ids[]" value="{{ $errorLog->id }}" class="row-checkbox">
                                                </td>

                                                <td>{{ $errorLog->id }}</td>

                                                <td>
                                                    @php
                                                        $levelClass = match(strtolower((string) $errorLog->level)) {
                                                            'emergency', 'alert', 'critical' => 'badge badge-danger',
                                                            'error' => 'badge badge-warning',
                                                            'warning' => 'badge badge-info',
                                                            default => 'badge badge-secondary',
                                                        };
                                                    @endphp

                                                    <span class="{{ $levelClass }}">
                                                        {{ strtoupper($errorLog->level) }}
                                                    </span>
                                                </td>

                                                <td>{{ $errorLog->service ?: '—' }}</td>

                                                <td style="max-width: 420px;">
                                                    <div class="text-wrap">
                                                        {{ \Illuminate\Support\Str::limit($errorLog->message, 140) }}
                                                    </div>
                                                </td>

                                                <td>
                                                    @if($errorLog->resolved_at)
                                                        <span class="badge badge-success">{{ __('Resolved') }}</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ __('Open') }}</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <div>{{ optional($errorLog->created_at)->format('Y-m-d') }}</div>
                                                    <small class="text-muted">{{ optional($errorLog->created_at)->translatedFormat('h:i A') }}</small>
                                                </td>

                                                <td class="text-right">
                                                    <a href="{{ route('dashboard.error-logs.show', $errorLog) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    @if(!$errorLog->resolved_at)
                                                        <form action="{{ route('dashboard.error-logs.resolve', $errorLog) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-bug fa-2x mb-3 d-block"></i>
                                {{ __('No error logs found.') }}
                            </div>
                        @endif
                    </div>

                    @if($errorLogs->hasPages())
                        <div class="card-footer clearfix">
                            {{ $errorLogs->links() }}
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
        const selectAll = document.getElementById('select-all');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                document.querySelectorAll('.row-checkbox').forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });
            });
        }
    });

    function getCheckedIds() {
        return Array.from(document.querySelectorAll('.row-checkbox:checked'));
    }

    function submitBulkResolve() {
        const checked = getCheckedIds();

        if (!checked.length) {
            alert('Please select at least one error log.');
            return;
        }

        const form = document.getElementById('bulk-action-form');
        form.action = "{{ route('dashboard.error-logs.bulk-resolve') }}";
        form.querySelector('input[name="_method"]').value = 'PATCH';
        form.submit();
    }

    function submitBulkDelete() {
        const checked = getCheckedIds();

        if (!checked.length) {
            alert('Please select at least one error log.');
            return;
        }

        if (!confirm('Are you sure you want to delete selected error logs?')) {
            return;
        }

        const form = document.getElementById('bulk-action-form');
        form.action = "{{ route('dashboard.error-logs.bulk-delete') }}";
        form.querySelector('input[name="_method"]').value = 'DELETE';
        form.submit();
    }
</script>
@endpush
