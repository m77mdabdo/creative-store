@extends('site.app.layout')

@section('title', __('site.order_details') . ' #' . $order->order_number)

@php
    $currency = $order->currency ?? 'SAR';

    $orderStatusMap = [
        'pending' => ['label' => __('site.status_pending'), 'class' => 'warning', 'row_class' => ''],
        'confirmed' => ['label' => __('site.status_confirmed'), 'class' => 'info', 'row_class' => ''],
        'processing' => ['label' => __('site.status_processing'), 'class' => 'info', 'row_class' => ''],
        'shipped' => ['label' => __('site.status_shipped'), 'class' => 'info', 'row_class' => ''],
        'delivered' => ['label' => __('site.status_delivered'), 'class' => 'success', 'row_class' => ''],
        'completed' => ['label' => __('site.status_completed'), 'class' => 'success', 'row_class' => ''],
        'cancelled' => ['label' => __('site.status_cancelled'), 'class' => 'danger', 'row_class' => 'danger-row'],
    ];

    $paymentStatusMap = [
        'pending' => ['label' => __('site.status_pending'), 'class' => 'secondary', 'row_class' => ''],
        'unpaid' => ['label' => __('site.status_unpaid'), 'class' => 'secondary', 'row_class' => ''],
        'cancelled' => ['label' => __('site.status_cancelled'), 'class' => 'secondary', 'row_class' => ''],
        'paid' => ['label' => __('site.status_paid'), 'class' => 'success', 'row_class' => ''],
        'failed' => ['label' => __('site.status_failed'), 'class' => 'danger', 'row_class' => 'danger-row'],
        'refund_pending' => ['label' => __('site.status_refund_pending'), 'class' => 'danger', 'row_class' => 'danger-row'],
        'partially_refunded' => ['label' => __('site.status_partially_refunded'), 'class' => 'warning', 'row_class' => ''],
        'refunded' => ['label' => __('site.status_refunded'), 'class' => 'dark', 'row_class' => ''],
    ];

    $shippingStatusMap = [
        'pending' => ['label' => __('site.status_pending'), 'class' => 'secondary', 'row_class' => ''],
        'processing' => ['label' => __('site.status_processing'), 'class' => 'info', 'row_class' => ''],
        'shipped' => ['label' => __('site.status_shipped'), 'class' => 'primary', 'row_class' => ''],
        'delivered' => ['label' => __('site.status_delivered'), 'class' => 'success', 'row_class' => ''],
        'cancelled' => ['label' => __('site.status_cancelled'), 'class' => 'danger', 'row_class' => 'danger-row'],
    ];

    $orderStatus = $orderStatusMap[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'secondary', 'row_class' => ''];
    $paymentStatus = $paymentStatusMap[$order->payment_status] ?? ['label' => ucfirst($order->payment_status), 'class' => 'secondary', 'row_class' => ''];
    $shippingStatus = $shippingStatusMap[$order->shipping_status] ?? ['label' => ucfirst($order->shipping_status), 'class' => 'secondary', 'row_class' => ''];

    $shipmentStatus = $shippingStatusMap[$order->shipment->status ?? 'pending'] ?? ['label' => ucfirst($order->shipment->status ?? 'pending'), 'class' => 'secondary', 'row_class' => ''];

    $canCancel = $order->status !== 'cancelled'
        && in_array($order->shipping_status, ['pending', 'processing', null], true)
        && $order->created_at
        && $order->created_at->diffInHours(now()) < 3;

    $cancelExpired = $order->created_at
        && $order->created_at->diffInHours(now()) >= 3
        && $order->status !== 'cancelled';

    $placedAt = $order->created_at?->translatedFormat('d M Y - h:i A');
@endphp

@section('content')
<section class="order-details-page bg0 p-t-80 p-b-90">
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success m-b-20">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger m-b-20">
                {{ session('error') }}
            </div>
        @endif

        <div class="page-header-box m-b-25">
            <div class="row align-items-center">
                <div class="col-lg-8 m-b-15">
                    <div class="page-mini-title">{{ __('site.order_details') }}</div>
                    <h2 class="page-main-title">{{ __('site.order_number') }} <bdi dir="ltr">#{{ $order->order_number }}</bdi></h2>
                    <div class="page-sub-title">
                        {{ __('site.placed_on') }} {{ $placedAt ?? '-' }}
                    </div>
                </div>

                <div class="col-lg-4 text-lg-right">
                    <a href="{{ route('shipping.orders.invoice', $order->id) }}" class="main-action-btn dark-btn">
                        <i class="fas fa-file-invoice m-r-8"></i>
                        {{ __('site.download_invoice') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">

                <div class="table-box m-b-24">
                    <div class="table-box-head">
                        <h4>{{ __('site.order_summary') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table details-grid-table mb-0">
                            <tbody>
                                <tr>
                                    <th>{{ __('site.order_number') }}</th>
                                    <td><bdi dir="ltr">{{ $order->order_number }}</bdi></td>
                                    <th>{{ __('site.placed_at') }}</th>
                                    <td>{{ $placedAt ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('site.payment_method') }}</th>
                                    <td>{{ status_label($order->payment_method) }}</td>
                                    <th>{{ __('site.shipping_method') }}</th>
                                    <td>{{ $order->shippingMethod?->label ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('site.currency') }}</th>
                                    <td>{{ strtoupper($currency) === 'SAR' ? __('SAR') : $currency }}</td>
                                    <th>{{ __('site.total') }}</th>
                                    <td>{{ money((float) $order->total, $currency) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-box m-b-24">
                    <div class="table-box-head">
                        <h4>{{ __('site.status_details') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table details-grid-table mb-0">
                            <tbody>
                                <tr class="{{ $orderStatus['row_class'] }}">
                                    <th>{{ __('site.order_status') }}</th>
                                    <td colspan="3">
                                        <span class="badge badge-{{ $orderStatus['class'] }} custom-badge">
                                            {{ $orderStatus['label'] }}
                                        </span>
                                    </td>
                                </tr>

                                <tr class="{{ $paymentStatus['row_class'] }}">
                                    <th>{{ __('site.payment_status') }}</th>
                                    <td colspan="3">
                                        <span class="badge badge-{{ $paymentStatus['class'] }} custom-badge">
                                            {{ $paymentStatus['label'] }}
                                        </span>
                                    </td>
                                </tr>

                                <tr class="{{ $shippingStatus['row_class'] }}">
                                    <th>{{ __('site.shipping_status') }}</th>
                                    <td colspan="3">
                                        <span class="badge badge-{{ $shippingStatus['class'] }} custom-badge">
                                            {{ $shippingStatus['label'] }}
                                        </span>
                                    </td>
                                </tr>

                                @if($order->status === 'cancelled')
                                    <tr class="danger-row">
                                        <th>{{ __('site.cancellation_reason') }}</th>
                                        <td colspan="3">{{ $order->cancellation_reason ?: __('site.order_was_cancelled') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-box m-b-24">
                    <div class="table-box-head">
                        <h4>{{ __('site.order_items') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table items-main-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 28%;">{{ __('site.product') }}</th>
                                    <th>{{ __('site.details') }}</th>
                                    <th class="text-center" style="width: 10%;">{{ __('site.quantity') }}</th>
                                    <th class="text-right" style="width: 16%;">{{ __('site.unit_price') }}</th>
                                    <th class="text-right" style="width: 18%;">{{ __('site.line_total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $item)
                                    @php
                                        $variantOptions = collect($item->variant_options ?? []);

                                        $displayOptions = $variantOptions->reject(function ($option) use ($item) {
                                            $slug = strtolower(trim((string) ($option['slug'] ?? '')));
                                            $name = strtolower(trim((string) ($option['name'] ?? '')));

                                            $isColor = in_array($slug, ['color', 'colors'], true)
                                                || in_array($name, ['color', 'colors', 'اللون', 'الألوان'], true);

                                            $isSize = in_array($slug, ['size', 'sizes'], true)
                                                || in_array($name, ['size', 'sizes', 'المقاس', 'الحجم'], true);

                                            if ($isColor && !empty($item->color)) {
                                                return true;
                                            }

                                            if ($isSize && !empty($item->size)) {
                                                return true;
                                            }

                                            return false;
                                        })->values();
                                    @endphp

                                    <tr>
                                        <td class="product-title-cell" data-label="{{ __('site.product') }}">
                                            <strong>{{ $item->product_name_snapshot }}</strong>
                                        </td>

                                        <td class="product-detail-cell" data-label="{{ __('site.details') }}">
                                            @if($item->sku_snapshot)
                                                <div><span>{{ __('site.sku') }}:</span> {{ $item->sku_snapshot }}</div>
                                            @endif

                                            @if($item->color)
                                                <div><span>{{ __('site.color') }}:</span> {{ $item->color }}</div>
                                            @endif

                                            @if($item->size)
                                                <div><span>{{ __('site.modal_size') }}:</span> {{ $item->size }}</div>
                                            @endif

                                            @if($displayOptions->count())
                                                @foreach($displayOptions as $option)
                                                    <div>
                                                        <span>{{ $option['name'] ?? __('site.option') }}:</span>
                                                        {{ $option['value'] ?? '-' }}
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>

                                        <td class="text-center" data-label="{{ __('site.quantity') }}">{{ (int) $item->quantity }}</td>
                                        <td class="text-right" data-label="{{ __('site.unit_price') }}">{{ money((float) $item->unit_price, $currency) }}</td>
                                        <td class="text-right total-strong" data-label="{{ __('site.line_total') }}">{{ money((float) $item->line_total, $currency) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="empty-row text-center">{{ __('site.no_items_in_order') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-box m-b-24">
                    <div class="table-box-head">
                        <h4>{{ __('site.shipment_details') }}</h4>
                    </div>

                    @if($order->shipment)
                        <div class="table-responsive">
                            <table class="table details-grid-table mb-0">
                                <tbody>
                                    <tr class="{{ $shipmentStatus['row_class'] }}">
                                        <th>{{ __('site.shipment_status') }}</th>
                                        <td colspan="3">
                                            <span class="badge badge-{{ $shipmentStatus['class'] }} custom-badge">
                                                {{ $shipmentStatus['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('site.carrier') }}</th>
                                        <td colspan="3">{{ $order->shipment->carrier ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('site.tracking_number') }}</th>
                                        <td colspan="3">{{ $order->shipment->tracking_number ?? __('site.not_available_yet') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('site.current_location') }}</th>
                                        <td colspan="3">{{ $order->shipment->current_location ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('site.carrier_status') }}</th>
                                        <td colspan="3">{{ $order->shipment->carrier_status ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if($order->shipment->tracking_number)
                            <div class="box-actions">
                                <a href="{{ route('tracking.search-form', ['tracking_number' => $order->shipment->tracking_number]) }}"
                                   class="main-action-btn light-btn">
                                    <i class="fas fa-map-marker-alt m-r-8"></i>
                                    {{ __('site.track_shipment') }}
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="empty-box-text">{{ __('site.no_shipment') }}</div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-sidebar">

                    <div class="table-box m-b-24">
                        <div class="table-box-head">
                            <h4>{{ __('site.shipping_address') }}</h4>
                        </div>

                        @if($order->addressSnapshot)
                            <div class="table-responsive">
                                <table class="table details-grid-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th>{{ __('site.full_name') }}</th>
                                            <td>{{ $order->addressSnapshot->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('site.phone') }}</th>
                                            <td>{{ $order->addressSnapshot->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('site.country_city') }}</th>
                                            <td>
                                                {{ $order->addressSnapshot->country }}
                                                -
                                                {{ $order->addressSnapshot->city }}
                                                @if($order->addressSnapshot->district)
                                                    - {{ $order->addressSnapshot->district }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('site.street') }}</th>
                                            <td>{{ $order->addressSnapshot->street }}</td>
                                        </tr>

                                        @if($order->addressSnapshot->building_no)
                                            <tr>
                                                <th>{{ __('site.building_no') }}</th>
                                                <td>{{ $order->addressSnapshot->building_no }}</td>
                                            </tr>
                                        @endif

                                        @if($order->addressSnapshot->apartment_no)
                                            <tr>
                                                <th>{{ __('site.apartment_no') }}</th>
                                                <td>{{ $order->addressSnapshot->apartment_no }}</td>
                                            </tr>
                                        @endif

                                        @if($order->addressSnapshot->postal_code)
                                            <tr>
                                                <th>{{ __('site.postal_code') }}</th>
                                                <td>{{ $order->addressSnapshot->postal_code }}</td>
                                            </tr>
                                        @endif

                                        @if($order->addressSnapshot->notes)
                                            <tr>
                                                <th>{{ __('site.notes') }}</th>
                                                <td>{{ $order->addressSnapshot->notes }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-box-text">{{ __('site.no_address_found') }}</div>
                        @endif
                    </div>

                    <div class="table-box m-b-24">
                        <div class="table-box-head">
                            <h4>{{ __('site.order_totals') }}</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table details-grid-table totals-table mb-0">
                                <tbody>
                                    <tr>
                                        <th>{{ __('site.subtotal') }}</th>
                                        <td class="text-right">{{ money((float) $order->subtotal, $currency) }}</td>
                                    </tr>

                                    @if((float) $order->discount > 0)
                                        <tr class="danger-row-soft">
                                            <th>{{ __('site.discount') }}</th>
                                            <td class="text-right">- {{ money((float) $order->discount, $currency) }}</td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <th>{{ __('site.shipping_fee') }}</th>
                                        <td class="text-right">{{ money((float) $order->shipping_fee, $currency) }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ __('site.vat') }}</th>
                                        <td class="text-right">{{ money((float) $order->tax_total, $currency) }}</td>
                                    </tr>

                                    <tr class="grand-total-row">
                                        <th>{{ __('site.total') }}</th>
                                        <td class="text-right">{{ money((float) $order->total, $currency) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-box">
                        <div class="table-box-head">
                            <h4>{{ __('site.actions') }}</h4>
                        </div>
                        <div class="actions-panel">
                            <a href="{{ route('shipping.orders.invoice', $order->id) }}" class="main-action-btn dark-btn w-100">
                                <i class="fas fa-file-invoice m-r-8"></i>
                                {{ __('site.download_invoice') }}
                            </a>

                            @if($canCancel)
                                <form action="{{ route('shipping.orders.cancel', $order->id) }}" method="POST" class="w-100 m-b-0">
                                    @csrf
                                    <button type="submit"
                                            class="main-action-btn danger-btn w-100"
                                            onclick="return confirm('{{ __('site.cancel_order_confirm') }}')">
                                        <i class="fas fa-times m-r-8"></i>
                                        {{ __('site.cancel_order') }}
                                    </button>
                                </form>
                            @endif

                            @if($cancelExpired)
                                <div class="cancel-note-box">
                                    {{ __('site.cancel_expired') }}
                                </div>
                            @endif

                            @if($order->status === 'cancelled')
                                <div class="cancel-note-box strong-red">
                                    {{ __('site.order_cancelled') }}
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@push('styles')
<link rel="stylesheet" href="{{ versioned_asset('css/site/site-shipping-orders-show.css') }}">
@endpush
