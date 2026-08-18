@extends('dashboard.app.layout')

@section('title', __('Error Log Details'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="mb-1">Error Log #{{ $errorLog->id }}</h1>
                    <p class="text-muted mb-0">{{ __('Detailed error log information.') }}</p>
                </div>

                <div class="d-flex align-items-center flex-wrap">
                    <a href="{{ route('dashboard.error-logs.index') }}" class="btn btn-default mr-2 mb-2">
                        <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
                    </a>

                    @if(!$errorLog->resolved_at)
                        <form action="{{ route('dashboard.error-logs.resolve', $errorLog) }}" method="POST" class="d-inline mb-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check mr-1"></i> {{ __('Mark as Resolved') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @php
                $level = strtolower((string) $errorLog->level);

                $levelClass = match($level) {
                    'emergency', 'alert', 'critical' => 'badge badge-danger',
                    'error' => 'badge badge-warning',
                    'warning' => 'badge badge-info',
                    default => 'badge badge-secondary',
                };

                $statusClass = $errorLog->resolved_at ? 'badge badge-success' : 'badge badge-danger';
                $statusText = $errorLog->resolved_at ? __('Resolved') : __('Open');

                $contextArray = is_array($errorLog->context) ? $errorLog->context : [];
                $contextJson = !empty($contextArray)
                    ? json_encode($contextArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null;
            @endphp

            <div class="row">
                <div class="col-lg-4">

                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Summary') }}</h3>
                        </div>

                        <div class="card-body">
                            <div class="mb-3">
                                <span class="{{ $levelClass }}">{{ strtoupper($errorLog->level) }}</span>
                                <span class="{{ $statusClass }} ml-2">{{ $statusText }}</span>
                            </div>

                            <table class="table table-sm table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 150px;">ID</th>
                                        <td>{{ $errorLog->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Service') }}</th>
                                        <td>{{ $errorLog->service ?: '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Created At') }}</th>
                                        <td>{{ optional($errorLog->created_at)->translatedFormat('Y-m-d h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Resolved At') }}</th>
                                        <td>{{ optional($errorLog->resolved_at)->translatedFormat('Y-m-d h:i A') ?: '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Updated At') }}</th>
                                        <td>{{ optional($errorLog->updated_at)->translatedFormat('Y-m-d h:i A') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Quick Context') }}</h3>
                        </div>

                        <div class="card-body p-0">
                            <table class="table table-sm table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 150px;">{{ __('Exception') }}</th>
                                        <td>{{ $contextArray['exception'] ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('File') }}</th>
                                        <td class="text-break">{{ $contextArray['file'] ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Line') }}</th>
                                        <td>{{ $contextArray['line'] ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>URL</th>
                                        <td class="text-break">{{ $contextArray['url'] ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Method') }}</th>
                                        <td>{{ $contextArray['method'] ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('User ID') }}</th>
                                        <td>{{ $contextArray['user_id'] ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>IP</th>
                                        <td>{{ $contextArray['ip'] ?? '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="col-lg-8">

                    <div class="card card-outline card-danger">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">{{ __('Message') }}</h3>
                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="copyBlock('error-message-block')">
                                <i class="fas fa-copy mr-1"></i> {{ __('Copy') }}
                            </button>
                        </div>

                        <div class="card-body">
                            <pre id="error-message-block" class="bg-light border rounded p-3 mb-0" style="white-space: pre-wrap; font-size: 13px;">{{ $errorLog->message }}</pre>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">{{ __('Context') }}</h3>
                            @if($contextJson)
                                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="copyBlock('error-context-block')">
                                    <i class="fas fa-copy mr-1"></i> {{ __('Copy') }}
                                </button>
                            @endif
                        </div>

                        <div class="card-body">
                            @if(!empty($contextArray))
                                <pre id="error-context-block" class="bg-light border rounded p-3 mb-0" style="white-space: pre-wrap; max-height: 320px; overflow:auto; font-size: 13px;">{{ $contextJson }}</pre>
                            @else
                                <p class="text-muted mb-0">{{ __('No context available.') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card card-outline card-dark">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">{{ __('Trace') }}</h3>
                            @if($errorLog->trace)
                                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="copyBlock('error-trace-block')">
                                    <i class="fas fa-copy mr-1"></i> {{ __('Copy') }}
                                </button>
                            @endif
                        </div>

                        <div class="card-body">
                            @if($errorLog->trace)
                                <pre id="error-trace-block" class="bg-light border rounded p-3 mb-0" style="white-space: pre-wrap; max-height: 600px; overflow:auto; font-size: 12px; line-height: 1.5;">{{ $errorLog->trace }}</pre>
                            @else
                                <p class="text-muted mb-0">{{ __('No trace available.') }}</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    function copyBlock(id) {
        const el = document.getElementById(id);
        if (!el) return;

        const text = el.innerText || el.textContent || '';

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function () {
                alert('Copied successfully');
            }).catch(function () {
                fallbackCopyText(text);
            });
        } else {
            fallbackCopyText(text);
        }
    }

    function fallbackCopyText(text) {
        const temp = document.createElement('textarea');
        temp.value = text;
        temp.style.position = 'fixed';
        temp.style.left = '-9999px';
        document.body.appendChild(temp);
        temp.focus();
        temp.select();

        try {
            document.execCommand('copy');
            alert('Copied successfully');
        } catch (e) {
            alert('Copy failed');
        }

        document.body.removeChild(temp);
    }
</script>
@endpush

