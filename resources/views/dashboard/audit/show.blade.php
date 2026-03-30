@extends('dashboard.app.layout')

@section('title', 'Audit Log Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="mb-1">Audit Log #{{ $auditLog->id }}</h1>
                    <p class="text-muted mb-0">Detailed audit log information.</p>
                </div>

                <div>
                    <a href="{{ route('dashboard.audit.index') }}" class="btn btn-default mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>

                    <a href="{{ route('dashboard.audit.print', ['q' => $auditLog->id]) }}" target="_blank" class="btn btn-dark">
                        <i class="fas fa-print mr-1"></i> Print
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-5">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Main Information</h3>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 180px;">ID</th>
                                        <td>{{ $auditLog->id }}</td>
                                    </tr>

                                    <tr>
                                        <th>User</th>
                                        <td>
                                            @if($auditLog->user)
                                                <div class="font-weight-bold">{{ $auditLog->user->name }}</div>
                                                <small class="text-muted">{{ $auditLog->user->email }}</small>
                                            @else
                                                <span class="text-muted">System / Unknown</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Action</th>
                                        <td>
                                            <span class="badge badge-info">{{ $auditLog->action }}</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Auditable Type</th>
                                        <td>
                                            {{ class_basename($auditLog->auditable_type) }}
                                            <br>
                                            <small class="text-muted">{{ $auditLog->auditable_type }}</small>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Auditable ID</th>
                                        <td>{{ $auditLog->auditable_id }}</td>
                                    </tr>

                                    <tr>
                                        <th>IP Address</th>
                                        <td>{{ $auditLog->ip_address ?: '—' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ optional($auditLog->created_at)->format('Y-m-d h:i A') }}</td>
                                    </tr>

                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ optional($auditLog->updated_at)->format('Y-m-d h:i A') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Changes Overview</h3>
                        </div>

                        <div class="card-body p-0">
                            @if(count($oldValuesRows) || count($newValuesRows))
                                @php
                                    $allKeys = collect($oldValuesRows)->pluck('key')
                                        ->merge(collect($newValuesRows)->pluck('key'))
                                        ->unique()
                                        ->values();

                                    $oldMap = collect($oldValuesRows)->keyBy('key');
                                    $newMap = collect($newValuesRows)->keyBy('key');
                                @endphp

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">Field</th>
                                                <th style="width: 35%;">Old Value</th>
                                                <th style="width: 35%;">New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($allKeys as $key)
                                                <tr>
                                                    <td><code>{{ $key }}</code></td>
                                                    <td class="text-danger">
                                                        {{ $oldMap[$key]['value'] ?? '—' }}
                                                    </td>
                                                    <td class="text-success">
                                                        {{ $newMap[$key]['value'] ?? '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-3 text-muted">
                                    No value changes recorded.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Raw Old Values</h3>
                        </div>

                        <div class="card-body">
                            @if(!empty($auditLog->old_values))
                                <pre class="bg-light p-3 rounded mb-0" style="white-space: pre-wrap; max-height: 300px; overflow:auto;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                            @else
                                <p class="text-muted mb-0">No old values recorded.</p>
                            @endif
                        </div>
                    </div>

                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">Raw New Values</h3>
                        </div>

                        <div class="card-body">
                            @if(!empty($auditLog->new_values))
                                <pre class="bg-light p-3 rounded mb-0" style="white-space: pre-wrap; max-height: 300px; overflow:auto;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                            @else
                                <p class="text-muted mb-0">No new values recorded.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
