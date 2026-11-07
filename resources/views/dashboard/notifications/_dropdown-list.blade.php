{{-- Rows inside the top-bar bell; also returned by the live feed. --}}
@forelse ($items as $item)
    @include('dashboard.notifications._item', ['item' => $item, 'dropdown' => true])
@empty
    <div class="cs-notif-empty">
        <i class="far fa-bell-slash" aria-hidden="true"></i>
        <p>{{ __('notifications.empty') }}</p>
    </div>
@endforelse
