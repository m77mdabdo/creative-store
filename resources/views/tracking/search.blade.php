@extends('site.app.layout')

@section('title', __('tracking.title'))

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/site/tracking.css') }}">
@endpush

@php
    $byTracking = old('tracking_number', request('tracking_number')) !== null && old('order_number') === null;
@endphp

@section('content')
<section class="trk-page">
    <div class="container">
        <div class="trk-search">
            <div class="trk-search-art" aria-hidden="true">
                <span class="trk-search-road"></span>
                <span class="trk-search-truck"><i class="fas fa-truck"></i></span>
                <span class="trk-search-pin"><i class="fas fa-map-marker-alt"></i></span>
            </div>

            <h1 class="trk-search-title">{{ __('tracking.title') }}</h1>
            <p class="trk-search-sub">{{ __('tracking.subtitle') }}</p>

            <div class="trk-card trk-search-card">
                <div class="trk-tabs" role="tablist">
                    <button type="button" role="tab" class="trk-tab {{ $byTracking ? '' : 'is-active' }}" data-tab="order"
                            aria-selected="{{ $byTracking ? 'false' : 'true' }}">
                        <i class="fas fa-receipt" aria-hidden="true"></i> {{ __('tracking.by_order') }}
                    </button>
                    <button type="button" role="tab" class="trk-tab {{ $byTracking ? 'is-active' : '' }}" data-tab="tracking"
                            aria-selected="{{ $byTracking ? 'true' : 'false' }}">
                        <i class="fas fa-barcode" aria-hidden="true"></i> {{ __('tracking.by_tracking') }}
                    </button>
                </div>

                @error('tracking')
                    <div class="trk-alert" role="alert"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ $message }}</div>
                @enderror

                <form method="POST" action="{{ route('tracking.track') }}" class="trk-form" novalidate>
                    @csrf

                    <div class="trk-pane {{ $byTracking ? '' : 'is-active' }}" data-pane="order">
                        <label class="trk-label" for="trk-order">{{ __('tracking.order_number') }}</label>
                        <input id="trk-order" type="text" name="order_number" class="trk-input" dir="ltr"
                               value="{{ old('order_number') }}" placeholder="{{ __('tracking.order_number_placeholder') }}"
                               autocomplete="off" {{ $byTracking ? 'disabled' : '' }}>

                        <label class="trk-label" for="trk-phone">{{ __('tracking.phone') }}</label>
                        <input id="trk-phone" type="tel" name="phone" class="trk-input" dir="ltr" inputmode="tel"
                               value="{{ old('phone') }}" placeholder="{{ __('tracking.phone_placeholder') }}"
                               autocomplete="tel" {{ $byTracking ? 'disabled' : '' }}>
                    </div>

                    <div class="trk-pane {{ $byTracking ? 'is-active' : '' }}" data-pane="tracking">
                        <label class="trk-label" for="trk-number-input">{{ __('tracking.tracking_number') }}</label>
                        <input id="trk-number-input" type="text" name="tracking_number" class="trk-input" dir="ltr"
                               value="{{ old('tracking_number', request('tracking_number')) }}"
                               placeholder="{{ __('tracking.tracking_number_placeholder') }}"
                               autocomplete="off" {{ $byTracking ? '' : 'disabled' }}>
                    </div>

                    <button type="submit" class="trk-btn trk-btn--primary trk-btn--block">
                        <i class="fas fa-search-location" aria-hidden="true"></i> {{ __('tracking.submit') }}
                    </button>

                    <p class="trk-hint">{{ __('tracking.where_to_find') }}</p>
                </form>
            </div>

            @if ($recentOrders->isNotEmpty())
                <div class="trk-card trk-recent">
                    <h2 class="trk-card-title"><i class="fas fa-box" aria-hidden="true"></i> {{ __('tracking.your_orders') }}</h2>
                    <ul>
                        @foreach ($recentOrders as $recent)
                            <li>
                                <div>
                                    <strong dir="ltr">#{{ $recent->order_number }}</strong>
                                    <span class="trk-muted">{{ $recent->created_at?->translatedFormat('j M Y') }} · {{ status_label($recent->status) }}</span>
                                </div>
                                <a href="{{ route('tracking.show', $recent->order_number) }}" class="trk-btn trk-btn--sm">
                                    {{ __('tracking.track') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    var tabs = document.querySelectorAll('.trk-tab');
    var panes = document.querySelectorAll('.trk-pane');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) {
                t.classList.toggle('is-active', t === tab);
                t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
            });
            panes.forEach(function (pane) {
                var active = pane.dataset.pane === tab.dataset.tab;
                pane.classList.toggle('is-active', active);
                pane.querySelectorAll('input').forEach(function (input) { input.disabled = !active; });
                if (active) { var first = pane.querySelector('input'); if (first) first.focus(); }
            });
        });
    });
})();
</script>
@endpush
