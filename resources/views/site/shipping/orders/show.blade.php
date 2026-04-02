@extends('site.app.layout')

@php
    $currency = $order->currency ?? 'SAR';

    $orderStatusMap = [
        'pending' => ['label' => __('site.status_pending'), 'class' => 'warning', 'row_class' => ''],
        'processing' => ['label' => __('site.status_processing'), 'class' => 'info', 'row_class' => ''],
        'completed' => ['label' => __('site.status_completed'), 'class' => 'success', 'row_class' => ''],
        'cancelled' => ['label' => __('site.status_cancelled'), 'class' => 'danger', 'row_class' => 'danger-row'],
    ];

    $paymentStatusMap = [
        'pending' => ['label' => __('site.status_pending'), 'class' => 'secondary', 'row_class' => ''],
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

    $placedAt = $order->created_at?->format('d M Y - h:i A');
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
                    <h2 class="page-main-title">{{ __('site.order_number') }} #{{ $order->order_number }}</h2>
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
                                    <td>{{ $order->order_number }}</td>
                                    <th>{{ __('site.placed_at') }}</th>
                                    <td>{{ $placedAt ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('site.payment_method') }}</th>
                                    <td>{{ strtoupper($order->payment_method ?? '-') }}</td>
                                    <th>{{ __('site.shipping_method') }}</th>
                                    <td>{{ optional($order->shippingMethod)->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('site.currency') }}</th>
                                    <td>{{ $currency }}</td>
                                    <th>{{ __('site.total') }}</th>
                                    <td>{{ number_format((float) $order->total, 2) }} {{ $currency }}</td>
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
                                        <td class="product-title-cell">
                                            <strong>{{ $item->product_name_snapshot }}</strong>
                                        </td>

                                        <td class="product-detail-cell">
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

                                        <td class="text-center">{{ (int) $item->quantity }}</td>
                                        <td class="text-right">{{ number_format((float) $item->unit_price, 2) }} {{ $currency }}</td>
                                        <td class="text-right total-strong">{{ number_format((float) $item->line_total, 2) }} {{ $currency }}</td>
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
                                        <td class="text-right">{{ number_format((float) $order->subtotal, 2) }} {{ $currency }}</td>
                                    </tr>

                                    @if((float) $order->discount > 0)
                                        <tr class="danger-row-soft">
                                            <th>{{ __('site.discount') }}</th>
                                            <td class="text-right">- {{ number_format((float) $order->discount, 2) }} {{ $currency }}</td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <th>{{ __('site.shipping_fee') }}</th>
                                        <td class="text-right">{{ number_format((float) $order->shipping_fee, 2) }} {{ $currency }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ __('site.vat') }}</th>
                                        <td class="text-right">{{ number_format((float) $order->tax_total, 2) }} {{ $currency }}</td>
                                    </tr>

                                    <tr class="grand-total-row">
                                        <th>{{ __('site.total') }}</th>
                                        <td class="text-right">{{ number_format((float) $order->total, 2) }} {{ $currency }}</td>
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
<style>
    .order-details-page {
        background: #f7f8fa;
    }

    .page-header-box,
    .table-box {
        background: #fff;
        border: 1px solid #dfe3e8;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .page-header-box {
        padding: 28px 30px;
    }

    .page-mini-title {
        font-size: 12px;
        font-weight: 700;
        color: #8b95a7;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 8px;
    }

    .page-main-title {
        font-size: 30px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.2;
        margin-bottom: 8px;
    }

    .page-sub-title {
        font-size: 15px;
        color: #6b7280;
    }

    .table-box {
        overflow: hidden;
    }

    .table-box-head {
        padding: 18px 22px;
        border-bottom: 1px solid #dfe3e8;
        background: #f8fafc;
    }

    .table-box-head h4 {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
        color: #1f2937;
    }

    .details-grid-table,
    .items-main-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .details-grid-table {
        border: 1px solid #bfc7d1;
    }

    .items-main-table {
        border: 1px solid #bfc7d1;
    }

    .details-grid-table th,
    .details-grid-table td,
    .items-main-table th,
    .items-main-table td {
        border: 1px solid #bfc7d1 !important;
        padding: 14px 16px;
        vertical-align: top;
        font-size: 14px;
    }

    .details-grid-table th {
        background: #f8fafc;
        color: #4b5563;
        font-weight: 700;
        white-space: nowrap;
        width: 24%;
    }

    .details-grid-table td {
        background: #fff;
        color: #111827;
        font-weight: 500;
    }

    .items-main-table thead th {
        background: #f1f5f9;
        color: #374151;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .03em;
        font-weight: 700;
    }

    .items-main-table tbody td {
        background: #fff;
        color: #111827;
    }

    .product-title-cell strong {
        font-size: 15px;
        color: #1f2937;
        font-weight: 700;
    }

    .product-detail-cell div {
        margin-bottom: 6px;
        color: #4b5563;
    }

    .product-detail-cell div:last-child {
        margin-bottom: 0;
    }

    .product-detail-cell span {
        font-weight: 700;
        color: #374151;
    }

    .total-strong {
        font-weight: 700;
        color: #111827;
    }

    .custom-badge {
        padding: 9px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
    }

    .danger-row th,
    .danger-row td {
        background: #fff1f2 !important;
        color: #b91c1c !important;
        font-weight: 700;
    }

    .danger-row-soft th,
    .danger-row-soft td {
        background: #fff5f5 !important;
        color: #c62828 !important;
        font-weight: 700;
    }

    .grand-total-row th,
    .grand-total-row td {
        background: #f8fafc !important;
        font-size: 17px;
        font-weight: 700;
        color: #111827;
    }

    .box-actions {
        padding: 18px 20px;
        border-top: 1px solid #dfe3e8;
        background: #fff;
    }

    .actions-panel {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .main-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        padding: 0 18px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none !important;
        border: 1px solid transparent;
        transition: .2s ease;
    }

    .dark-btn {
        background: #111827;
        color: #fff !important;
    }

    .dark-btn:hover {
        background: #0f172a;
        color: #fff !important;
    }

    .light-btn {
        background: #fff;
        color: #111827 !important;
        border-color: #d1d5db;
    }

    .light-btn:hover {
        background: #f8fafc;
        color: #111827 !important;
    }

    .danger-btn {
        background: #d32f2f;
        color: #fff;
        border: 0;
    }

    .danger-btn:hover {
        background: #b71c1c;
        color: #fff;
    }

    .cancel-note-box {
        background: #fff5f5;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 13px 14px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
    }

    .cancel-note-box.strong-red {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #991b1b;
    }

    .empty-box-text,
    .empty-row {
        padding: 22px !important;
        color: #6b7280;
        font-size: 14px;
        background: #fff !important;
    }

    .sticky-sidebar {
        position: sticky;
        top: 100px;
    }

    @media (max-width: 991.98px) {
        .sticky-sidebar {
            position: relative;
            top: auto;
        }

        .page-header-box {
            padding: 24px;
        }

        .page-main-title {
            font-size: 26px;
        }
    }

    @media (max-width: 767.98px) {
        .details-grid-table th,
        .details-grid-table td,
        .items-main-table th,
        .items-main-table td {
            padding: 12px 13px;
            font-size: 13px;
        }

        .main-action-btn {
            width: 100%;
        }
    }
</style>
@endpush
