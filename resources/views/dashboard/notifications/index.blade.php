@extends('dashboard.app.layout')

@section('title', __('notifications.title'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
            <h1>{{ __('notifications.title') }}</h1>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('dashboard.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-check-double"></i> {{ __('notifications.mark_all_read') }}
                    </button>
                </form>
            @endif
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link {{ $onlyUnread ? '' : 'active' }}" href="{{ route('dashboard.notifications.index') }}">
                                {{ __('notifications.all') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $onlyUnread ? 'active' : '' }}" href="{{ route('dashboard.notifications.index', ['filter' => 'unread']) }}">
                                {{ __('notifications.unread') }}
                                @if ($unreadCount > 0)
                                    <span class="badge badge-danger">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0">
                    @forelse ($items as $item)
                        @include('dashboard.notifications._item', ['item' => $item])
                    @empty
                        <div class="cs-notif-empty">
                            <i class="far fa-bell-slash" aria-hidden="true"></i>
                            <p>{{ __('notifications.empty') }}</p>
                        </div>
                    @endforelse
                </div>

                @if ($notifications->hasPages())
                    <div class="card-footer">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
