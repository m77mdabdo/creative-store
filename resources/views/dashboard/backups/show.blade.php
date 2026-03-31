@extends('dashboard.app.layout')

@section('title', 'Backup Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Backup Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.home') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.backups.index') }}">System Backups</a>
                        </li>
                        <li class="breadcrumb-item active">Backup #{{ $systemBackup->id }}</li>
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
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Backup Information</h3>

                            <div class="card-tools">
                                <a href="{{ route('dashboard.backups.index') }}" class="btn btn-default btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i>
                                    Back
                                </a>

                                @if(auth()->user()?->hasPermission('backup.download') && $systemBackup->status === 'success' && $fileExists)
                                    <a href="{{ route('dashboard.backups.download', $systemBackup) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download mr-1"></i>
                                        Download
                                    </a>
                                @endif

                                @if(auth()->user()?->hasPermission('backup.delete'))
                                    <form
                                        action="{{ route('dashboard.backups.destroy', $systemBackup) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this backup record and file?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash mr-1"></i>
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 220px;">ID</th>
                                        <td>#{{ $systemBackup->id }}</td>
                                    </tr>

                                    <tr>
                                        <th>Type</th>
                                        <td>
                                            @if($systemBackup->type === 'manual')
                                                <span class="badge badge-info">Manual</span>
                                            @else
                                                <span class="badge badge-warning">Scheduled</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($systemBackup->status === 'success')
                                                <span class="badge badge-success">Success</span>
                                            @else
                                                <span class="badge badge-danger">Failed</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>File Path</th>
                                        <td>
                                            <div class="text-wrap">{{ $systemBackup->file_path ?: '—' }}</div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>File Exists</th>
                                        <td>
                                            @if($fileExists)
                                                <span class="badge badge-success">Yes</span>
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Size</th>
                                        <td>{{ $humanSize }}</td>
                                    </tr>

                                    <tr>
                                        <th>Started At</th>
                                        <td>
                                            @if($systemBackup->started_at)
                                                {{ $systemBackup->started_at->format('Y-m-d h:i:s A') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Finished At</th>
                                        <td>
                                            @if($systemBackup->finished_at)
                                                {{ $systemBackup->finished_at->format('Y-m-d h:i:s A') }}
                                            @else
                                                <span class="text-muted">Not finished</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Duration</th>
                                        <td>
                                            @if($systemBackup->started_at && $systemBackup->finished_at)
                                                {{ $systemBackup->started_at->diffForHumans($systemBackup->finished_at, true) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Note</th>
                                        <td>
                                            <div class="text-wrap">{{ $systemBackup->note ?: '—' }}</div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Created At</th>
                                        <td>
                                            @if($systemBackup->created_at)
                                                {{ $systemBackup->created_at->format('Y-m-d h:i:s A') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Updated At</th>
                                        <td>
                                            @if($systemBackup->updated_at)
                                                {{ $systemBackup->updated_at->format('Y-m-d h:i:s A') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>

                        <div class="card-body">
                            @if(auth()->user()?->hasPermission('backup.create'))
                                <a href="{{ route('dashboard.backups.create') }}" class="btn btn-primary btn-block mb-2">
                                    <i class="fas fa-plus mr-1"></i>
                                    Create New Manual Backup
                                </a>
                            @endif

                            @if(auth()->user()?->hasPermission('backup.download') && $systemBackup->status === 'success' && $fileExists)
                                <a href="{{ route('dashboard.backups.download', $systemBackup) }}" class="btn btn-success btn-block mb-2">
                                    <i class="fas fa-download mr-1"></i>
                                    Download This Backup
                                </a>
                            @endif

                            <a href="{{ route('dashboard.backups.index') }}" class="btn btn-default btn-block">
                                <i class="fas fa-list mr-1"></i>
                                Back to Backups List
                            </a>
                        </div>
                    </div>

                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Status Summary</h3>
                        </div>

                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Backup ID:</strong> #{{ $systemBackup->id }}
                            </p>

                            <p class="mb-2">
                                <strong>Type:</strong>
                                {{ ucfirst($systemBackup->type) }}
                            </p>

                            <p class="mb-2">
                                <strong>Status:</strong>
                                {{ ucfirst($systemBackup->status) }}
                            </p>

                            <p class="mb-2">
                                <strong>Readable Size:</strong>
                                {{ $humanSize }}
                            </p>

                            <p class="mb-0">
                                <strong>Download Ready:</strong>
                                {{ $fileExists ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
