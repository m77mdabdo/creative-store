@extends('dashboard.app.layout')

@section('title', 'Contact Messages')

@section('content')
@php
    $filters = $filters ?? [
        'q' => '',
        'status' => '',
        'sort' => 'latest',
        'per_page' => 15,
    ];

    $stats = $stats ?? [
        'total' => 0,
        'unread' => 0,
        'read' => 0,
    ];
@endphp

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1>{{ __('Contact Messages') }}</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total'] }}</h3>
                            <p>{{ __('Total Messages') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stats['unread'] }}</h3>
                            <p>{{ __('Unread Messages') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['read'] }}</h3>
                            <p>{{ __('Read Messages') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('dashboard.contact-messages.index') }}">
                        <div class="row">
                            <div class="col-md-5 mb-2">
                                <input
                                    type="text"
                                    name="q"
                                    class="form-control"
                                    placeholder="{{ __('Search by name, email, phone, subject...') }}"
                                    value="{{ $filters['q'] ?? '' }}"
                                >
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('Status: All') }}</option>
                                    <option value="unread" @selected(($filters['status'] ?? '') === 'unread')>{{ __('Unread') }}</option>
                                    <option value="read" @selected(($filters['status'] ?? '') === 'read')>{{ __('Read') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>{{ __('Latest') }}</option>
                                    <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>{{ __('Oldest') }}</option>
                                    <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>{{ __('Name A-Z') }}</option>
                                    <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>{{ __('Name Z-A') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="per_page" class="form-control">
                                    @foreach([10,15,25,50,100] as $size)
                                        <option value="{{ $size }}" @selected((int) ($filters['per_page'] ?? 15) === $size)>{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1 mb-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <form action="{{ route('dashboard.contact-messages.bulk-action') }}" method="POST">
                    @csrf

                    <div class="card-body border-bottom">
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <div class="mr-2">
                                <select name="action" class="form-control">
                                    <option value="">{{ __('Bulk Action') }}</option>
                                    <option value="read">{{ __('Mark as Read') }}</option>
                                    <option value="unread">{{ __('Mark as Unread') }}</option>
                                    <option value="delete">{{ __('Delete') }}</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-dark" onclick="return confirm(@js(__('Apply selected action?')))">
                                {{ __('Apply') }}
                            </button>
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="check-all">
                                    </th>
                                    <th>#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th class="text-center" style="width: 220px;">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $message)
                                    <tr class="{{ $message->is_read ? '' : 'font-weight-bold' }}">
                                        <td>
                                            <input type="checkbox" name="ids[]" value="{{ $message->id }}" class="row-check">
                                        </td>
                                        <td>{{ $message->id }}</td>
                                        <td>{{ $message->name }}</td>
                                        <td>{{ $message->email }}</td>
                                        <td>{{ $message->phone ?: '-' }}</td>
                                        <td>{{ $message->subject ?: __('No Subject') }}</td>
                                        <td>
                                            @if($message->is_read)
                                                <span class="badge badge-success">{{ __('Read') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Unread') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $message->created_at?->translatedFormat('Y-m-d h:i A') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('dashboard.contact-messages.show', $message) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($message->is_read)
                                                    <form action="{{ route('dashboard.contact-messages.mark-unread', $message) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('Mark as Unread') }}">
                                                            <i class="fas fa-envelope"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('dashboard.contact-messages.mark-read', $message) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" title="{{ __('Mark as Read') }}">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('dashboard.contact-messages.destroy', $message) }}"
                                                      method="POST"
                                                      style="display:inline-block;"
                                                      onsubmit="return confirm(@js(__('Are you sure you want to delete this message?')));">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">{{ __('No messages found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                @if($messages->hasPages())
                    <div class="card-footer clearfix">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('check-all');
    const rowChecks = document.querySelectorAll('.row-check');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            rowChecks.forEach(function (checkbox) {
                checkbox.checked = checkAll.checked;
            });
        });
    }
});
</script>
@endpush
