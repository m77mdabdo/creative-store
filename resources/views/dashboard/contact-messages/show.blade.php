@extends('dashboard.app.layout')

@section('title', 'View Contact Message')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('View Contact Message') }}</h1>

            <div>
                <a href="{{ route('dashboard.contact-messages.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('Message Details') }}</h3>

                    <div>
                        @if($message->is_read)
                            <form action="{{ route('dashboard.contact-messages.mark-unread', $message) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-envelope"></i> {{ __('Mark Unread') }}
                                </button>
                            </form>
                        @else
                            <form action="{{ route('dashboard.contact-messages.mark-read', $message) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> {{ __('Mark Read') }}
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('dashboard.contact-messages.destroy', $message) }}" method="POST" class="d-inline-block" onsubmit="return confirm(@js(__('Are you sure you want to delete this message?')));">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>ID</strong>
                            <div>{{ $message->id }}</div>
                        </div>

                        <div class="col-md-4">
                            <strong>{{ __('Status') }}</strong>
                            <div>
                                @if($message->is_read)
                                    <span class="badge badge-success">{{ __('Read') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ __('Unread') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <strong>{{ __('Created At') }}</strong>
                            <div>{{ $message->created_at?->translatedFormat('Y-m-d h:i A') }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('Name') }}</strong>
                            <div>{{ $message->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <strong>{{ __('Email') }}</strong>
                            <div>{{ $message->email }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('Phone') }}</strong>
                            <div>{{ $message->phone ?: '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <strong>{{ __('Subject') }}</strong>
                            <div>{{ $message->subject ?: __('No Subject') }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Message') }}</strong>
                        <div class="mt-2 p-3 border rounded bg-light" style="white-space: pre-line;">
                            {{ $message->message }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
