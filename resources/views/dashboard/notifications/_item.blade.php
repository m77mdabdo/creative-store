{{-- One notification row: used in the top-bar dropdown and the full page. --}}
<a href="{{ route('dashboard.notifications.open', $item->id) }}"
   class="cs-notif-item {{ $item->unread ? 'is-unread' : '' }} {{ $dropdown ?? false ? 'dropdown-item' : '' }}">
    <span class="cs-notif-icon bg-{{ $item->tone }}">
        <i class="fas {{ $item->icon }}" aria-hidden="true"></i>
    </span>
    <span class="cs-notif-body">
        <span class="cs-notif-title">{{ $item->title }}</span>
        @if ($item->message)
            <span class="cs-notif-message">{{ $item->message }}</span>
        @endif
        <span class="cs-notif-time" title="{{ $item->date }}">{{ $item->time }}</span>
    </span>
    @if ($item->unread)
        <span class="cs-notif-dot" aria-label="{{ __('notifications.unread') }}"></span>
    @endif
</a>
