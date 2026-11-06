@extends('site.app.layout')

@php
    /** @var \App\Support\Tracking\OrderTracking $tracking */
    $order = $tracking->order;
    $shipment = $tracking->shipment;
    $promise = $tracking->promise();
    $address = $order->addressSnapshot;
    $events = $tracking->events();
    $steps = $tracking->steps();
    $cancelled = $tracking->isCancelled();
    $delivered = $tracking->isDelivered();
    $state = $cancelled ? 'cancelled' : ($delivered ? 'delivered' : 'active');
    $rtl = app()->getLocale() === 'ar';
    $dayFormat = $rtl ? 'l، j M' : 'l, j M';
    $methodName = $order->shippingMethod
        ? (\Illuminate\Support\Facades\Lang::has('shipping.methods.' . $order->shippingMethod->code)
            ? __('shipping.methods.' . $order->shippingMethod->code)
            : $order->shippingMethod->name)
        : null;
    $itemsCount = (int) $order->items->sum('quantity');
@endphp

@section('title', __('tracking.title') . ' · ' . $order->order_number)

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/site/tracking.css') }}">
@endpush

@section('content')
<section class="trk-page">
    <div class="container">

        {{-- Hero --}}
        <div class="trk-hero trk-hero--{{ $state }}">
            @if ($delivered)
                <div class="trk-confetti" aria-hidden="true">
                    @for ($i = 0; $i < 18; $i++)<span></span>@endfor
                </div>
            @endif

            <div class="trk-hero-main">
                <span class="trk-hero-badge">
                    <i class="fas {{ $cancelled ? 'fa-times' : $steps[$tracking->currentIndex()]->icon }}" aria-hidden="true"></i>
                </span>
                <div>
                    <p class="trk-hero-order">
                        {{ __('tracking.order') }} <strong dir="ltr">#{{ $order->order_number }}</strong>
                        <span class="trk-dot">·</span>
                        {{ __('tracking.placed_on', ['date' => ($order->placed_at ?? $order->created_at)?->translatedFormat('j M Y')]) }}
                    </p>
                    <h1 class="trk-hero-title">{{ $tracking->headline() }}</h1>
                    <p class="trk-hero-sub">{{ $tracking->subline() }}</p>
                </div>
            </div>

            @unless ($cancelled)
                <div class="trk-eta">
                    @if ($delivered)
                        <span class="trk-eta-label">{{ __('tracking.stages.' . ($tracking->isPickup ? 'pickup_delivered' : 'delivered')) }}</span>
                        <strong class="trk-eta-date">{{ $tracking->deliveredAt()?->translatedFormat($dayFormat) ?? '—' }}</strong>
                    @elseif ($promise)
                        <span class="trk-eta-label">{{ __('shipping.eta.estimated') }}</span>
                        <strong class="trk-eta-date">{{ $promise->label() }}</strong>
                        @if ($promise->isLate())
                            <span class="trk-eta-late">{{ __('shipping.eta.running_late') }}</span>
                        @endif
                    @endif
                </div>
            @endunless
        </div>

        {{-- Progress --}}
        @unless ($cancelled)
            <div class="trk-card trk-progress-card">
                <div class="trk-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                     aria-valuenow="{{ $tracking->progress() }}" aria-label="{{ __('tracking.progress') }}">
                    <span class="trk-bar-fill" style="--trk-progress: {{ $tracking->progress() }}%"></span>
                    @unless ($delivered)
                        <span class="trk-bar-truck" style="--trk-progress: {{ $tracking->progress() }}%" aria-hidden="true">
                            <i class="fas fa-truck"></i>
                        </span>
                    @endunless
                </div>

                <ol class="trk-steps">
                    @foreach ($steps as $step)
                        <li class="trk-step {{ $step->done ? 'is-done' : '' }} {{ $step->current ? 'is-current' : '' }}">
                            <span class="trk-step-icon">
                                <i class="fas {{ $step->done && ! $step->current ? 'fa-check' : $step->icon }}" aria-hidden="true"></i>
                            </span>
                            <span class="trk-step-label">{{ $step->label }}</span>
                            <span class="trk-step-time">
                                @if ($step->at)
                                    {{ $step->at->translatedFormat('j M') }}<br>{{ $step->at->translatedFormat('h:i A') }}
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endunless

        <div class="trk-grid">
            {{-- Updates feed --}}
            <div class="trk-card">
                <div class="trk-card-head">
                    <h2 class="trk-card-title"><i class="fas fa-stream" aria-hidden="true"></i> {{ __('tracking.updates') }}</h2>
                    @unless ($cancelled || $delivered)
                        <span class="trk-live"><span class="trk-live-dot"></span>{{ __('tracking.auto_refresh') }}</span>
                    @endunless
                </div>

                @if ($events->isEmpty())
                    <p class="trk-empty">{{ __('tracking.no_updates') }}</p>
                @else
                    <ul class="trk-feed">
                        @foreach ($events as $event)
                            <li class="trk-feed-item trk-tone-{{ $event->tone }} {{ $loop->first ? 'is-latest' : '' }}">
                                <span class="trk-feed-dot" aria-hidden="true"></span>
                                <div class="trk-feed-body">
                                    <strong>{{ $event->title }}</strong>
                                    @if ($event->location)
                                        <span class="trk-feed-place"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> {{ $event->location }}</span>
                                    @endif
                                </div>
                                <time class="trk-feed-time" datetime="{{ $event->at->toIso8601String() }}">
                                    {{ $event->at->translatedFormat('j M') }} · {{ $event->at->translatedFormat('h:i A') }}
                                </time>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="trk-side">
                {{-- Shipment --}}
                <div class="trk-card">
                    <h2 class="trk-card-title"><i class="fas fa-shipping-fast" aria-hidden="true"></i> {{ __('tracking.shipment') }}</h2>
                    <dl class="trk-facts">
                        @if ($methodName)
                            <div><dt>{{ __('tracking.method') }}</dt><dd>{{ $methodName }}</dd></div>
                        @endif
                        @if ($shipment?->carrier)
                            <div><dt>{{ __('tracking.carrier') }}</dt><dd>{{ \Illuminate\Support\Str::headline($shipment->carrier) }}</dd></div>
                        @endif
                        @if ($shipment?->tracking_number)
                            <div>
                                <dt>{{ __('tracking.tracking_no') }}</dt>
                                <dd class="trk-copy-wrap">
                                    <span dir="ltr" id="trk-number">{{ $shipment->tracking_number }}</span>
                                    <button type="button" class="trk-copy" data-copy="#trk-number"
                                            data-done="{{ __('tracking.copied') }}">{{ __('tracking.copy') }}</button>
                                </dd>
                            </div>
                        @endif
                        @if ($shipment?->current_location)
                            <div>
                                <dt>{{ __('tracking.current_location') }}</dt>
                                <dd>{{ \App\Support\Tracking\OrderTracking::translateCarrierText($shipment->current_location) }}</dd>
                            </div>
                        @endif
                        @if ($promise && ! $cancelled)
                            <div><dt>{{ __('shipping.eta.estimated') }}</dt><dd>{{ $promise->label() }}</dd></div>
                        @endif
                    </dl>
                </div>

                {{-- Address --}}
                <div class="trk-card">
                    <h2 class="trk-card-title">
                        <i class="fas {{ $tracking->isPickup ? 'fa-store' : 'fa-map-marker-alt' }}" aria-hidden="true"></i>
                        {{ $tracking->isPickup ? __('tracking.pickup_from') : __('tracking.deliver_to') }}
                    </h2>
                    @if ($tracking->isPickup)
                        <p class="trk-address">
                            <strong>{{ __('tracking.store_pickup') }}</strong><br>
                            {{ $rtl ? config('store.legal.address_ar') : config('store.legal.address_en') }}
                        </p>
                    @elseif ($address)
                        <p class="trk-address">
                            <strong>{{ $address->full_name }}</strong><br>
                            {{ collect([$address->street, $address->district, $address->city])->filter()->implode($rtl ? '، ' : ', ') }}
                        </p>
                    @endif
                </div>

                {{-- Items --}}
                <div class="trk-card">
                    <h2 class="trk-card-title">
                        <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                        {{ __('tracking.items') }}
                        <span class="trk-muted">({{ trans_choice('tracking.items_count', $itemsCount, ['count' => $itemsCount]) }})</span>
                    </h2>
                    <ul class="trk-items">
                        @foreach ($order->items->take(6) as $item)
                            <li>
                                <span class="trk-item-name">{{ $item->product_name_snapshot }}</span>
                                <span class="trk-muted">{{ __('tracking.qty', ['count' => $item->quantity]) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="trk-total">
                        <span>{{ __('tracking.total') }}</span>
                        <strong>{{ money($order->total, $order->currency) }}</strong>
                    </div>
                </div>

                <div class="trk-actions">
                    @auth
                        @if ($order->user_id === auth()->id())
                            <a href="{{ route('shipping.orders.show', $order->id) }}" class="trk-btn trk-btn--primary">{{ __('tracking.view_order') }}</a>
                        @endif
                    @endauth
                    <a href="{{ route('tracking.search-form') }}" class="trk-btn">{{ __('tracking.new_search') }}</a>
                </div>

                <p class="trk-help">
                    {{ __('tracking.help') }}
                    <a href="{{ route('contact.index') }}">{{ __('tracking.contact_us') }}</a>
                </p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    document.querySelectorAll('.trk-copy').forEach(function (button) {
        button.addEventListener('click', function () {
            var target = document.querySelector(button.dataset.copy);
            if (!target || !navigator.clipboard) return;
            navigator.clipboard.writeText(target.textContent.trim()).then(function () {
                var label = button.textContent;
                button.textContent = button.dataset.done;
                setTimeout(function () { button.textContent = label; }, 1600);
            });
        });
    });

    @unless ($cancelled || $delivered)
        // Follow the order while the page is open and reload when it moves on.
        var known = @json(['stage' => $tracking->currentStage(), 'status' => $order->status]);
        var url = @json(route('tracking.status', $order->order_number));
        var poll = function () {
            if (document.hidden) return;
            fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (response) { return response.ok ? response.json() : null; })
                .then(function (data) {
                    if (data && (data.stage !== known.stage || data.status !== known.status)) {
                        window.location.reload();
                    }
                })
                .catch(function () {});
        };
        setInterval(poll, 30000);
        document.addEventListener('visibilitychange', poll);
    @endunless
})();
</script>
@endpush
