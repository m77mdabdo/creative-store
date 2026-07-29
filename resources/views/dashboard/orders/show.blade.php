@extends('dashboard.app.layout')

@php
    $currency = $order->currency ?? __('SAR');

    $orderStatusMap = [
        'pending' => ['label' => 'Pending', 'class' => 'warning'],
        'processing' => ['label' => 'Processing', 'class' => 'info'],
        'completed' => ['label' => 'Completed', 'class' => 'success'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'danger'],
    ];

    $paymentStatusMap = [
        'pending' => ['label' => 'Pending', 'class' => 'secondary'],
        'paid' => ['label' => 'Paid', 'class' => 'success'],
        'failed' => ['label' => 'Failed', 'class' => 'danger'],
        'refund_pending' => ['label' => 'Refund Pending', 'class' => 'warning'],
        'partially_refunded' => ['label' => 'Partially Refunded', 'class' => 'info'],
        'refunded' => ['label' => 'Refunded', 'class' => 'dark'],
    ];

    $shippingStatusMap = [
        'pending' => ['label' => 'Pending', 'class' => 'secondary'],
        'processing' => ['label' => 'Processing', 'class' => 'info'],
        'shipped' => ['label' => 'Shipped', 'class' => 'primary'],
        'delivered' => ['label' => 'Delivered', 'class' => 'success'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'danger'],
    ];

    $returnStatusMap = [
        'pending' => ['label' => 'Pending', 'class' => 'warning'],
        'approved' => ['label' => 'Approved', 'class' => 'info'],
        'completed' => ['label' => 'Completed', 'class' => 'success'],
        'rejected' => ['label' => 'Rejected', 'class' => 'danger'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'secondary'],
    ];

    $refundStatusMap = [
        'pending' => ['label' => 'Pending', 'class' => 'warning'],
        'processed' => ['label' => 'Processed', 'class' => 'success'],
        'failed' => ['label' => 'Failed', 'class' => 'danger'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'secondary'],
    ];

    $orderStatus = $orderStatusMap[$order->status] ?? ['label' => status_label($order->status), 'class' => 'secondary'];
    $paymentStatus = $paymentStatusMap[$order->payment_status] ?? ['label' => status_label($order->payment_status), 'class' => 'secondary'];
    $shippingStatus = $shippingStatusMap[$order->shipping_status] ?? ['label' => status_label($order->shipping_status), 'class' => 'secondary'];

    $placedAt = $order->placed_at?->format('d M Y, h:i A') ?? $order->created_at?->format('d M Y, h:i A') ?? '-';
@endphp

@section('content')
    <div class="content-wrapper order-show-page">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h1 class="mb-1">Order #{{ $order->order_number }}</h1>
                        <div class="text-muted">
                            Placed at {{ $placedAt }}
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('dashboard.orders.index') }}" class="btn btn-outline-secondary mr-2 mb-2">
                            <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Orders') }}
                        </a>

                        <a href="{{ route('dashboard.orders.invoice', $order) }}" target="_blank"
                           class="btn btn-outline-dark mb-2">
                            <i class="fas fa-file-invoice mr-1"></i> {{ __('Invoice') }}
                        </a>
                    </div>
                </div>

                <ol class="breadcrumb float-sm-right mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.home') }}">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.orders.index') }}">{{ __('Orders') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                </ol>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">

                @if(session('success'))
                    <div class="alert alert-success alert-modern">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-modern">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-modern">
                        <div class="font-weight-bold mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            {{ __('Please review the following:') }}
                        </div>
                        <ul class="mb-0 pl-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Top Summary Cards --}}
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="card summary-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="summary-label">{{ __('Order Status') }}</div>
                                <div class="summary-value">
                                    <span class="badge badge-{{ $orderStatus['class'] }} badge-pill px-3 py-2">
                                        {{ $orderStatus['label'] }}
                                    </span>
                                </div>
                                <div class="summary-meta">{{ __('Current lifecycle state') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card summary-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="summary-label">{{ __('Payment Status') }}</div>
                                <div class="summary-value">
                                    <span class="badge badge-{{ $paymentStatus['class'] }} badge-pill px-3 py-2">
                                        {{ $paymentStatus['label'] }}
                                    </span>
                                </div>
                                <div class="summary-meta">{{ status_label($order->payment_method) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="card summary-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="summary-label">{{ __('Shipping Status') }}</div>
                                <div class="summary-value">
                                    <span class="badge badge-{{ $shippingStatus['class'] }} badge-pill px-3 py-2">
                                        {{ $shippingStatus['label'] }}
                                    </span>
                                </div>
                                <div class="summary-meta">{{ $order->shippingMethod?->label ?? __('No shipping method') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- LEFT SIDE --}}
                    <div class="col-lg-8">

                        {{-- Order Summary --}}
                        <div class="card card-outline card-primary shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-receipt mr-2 text-primary"></i>{{ __('Order Summary') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row info-grid">
                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">{{ __('Order Number') }}</div>
                                            <div class="info-text">{{ $order->order_number }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">{{ __('Placed At') }}</div>
                                            <div class="info-text">{{ $placedAt }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">{{ __('Currency') }}</div>
                                            <div class="info-text">{{ $currency }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">{{ __('Payment Method') }}</div>
                                            <div class="info-text">{{ status_label($order->payment_method) }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">{{ __('Shipping Method') }}</div>
                                            <div class="info-text">{{ $order->shippingMethod?->label ?? '-' }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">{{ __('Coupon') }}</div>
                                            <div class="info-text">{{ $order->coupon->code ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                @if($order->estimated_delivery_from || $order->estimated_delivery_to)
                                    <div class="soft-section mt-2">
                                        <div class="font-weight-bold mb-1">{{ __('Estimated Delivery') }}</div>
                                        <div class="text-muted">
                                            {{ $order->estimated_delivery_from?->format('d M Y') ?: '-' }}
                                            —
                                            {{ $order->estimated_delivery_to?->format('d M Y') ?: '-' }}
                                        </div>
                                    </div>
                                @endif

                                @if($order->cancelled_at)
                                    <div class="soft-section danger-soft mt-3">
                                        <div class="font-weight-bold text-danger mb-1">
                                            <i class="fas fa-ban mr-1"></i> {{ __('Cancellation Details') }}
                                        </div>
                                        <div><strong>{{ __('Cancelled At:') }}</strong> {{ $order->cancelled_at?->format('d M Y, h:i A') }}</div>
                                        <div><strong>{{ __('Reason:') }}</strong> {{ $order->cancellation_reason ?: '-' }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($canEdit ?? false)
                            {{-- Quick Management --}}
                            <div class="card card-outline card-warning shadow-sm">
                                <div class="card-header border-0">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-edit mr-2 text-warning"></i>{{ __('Update Order Statuses') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('dashboard.orders.update-statuses', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">{{ __('Order Status') }}</label>
                                                <select name="status" class="form-control custom-control-modern">
                                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">{{ __('Payment Status') }}</label>
                                                <select name="payment_status" class="form-control custom-control-modern">
                                                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                                                    <option value="refund_pending" {{ $order->payment_status === 'refund_pending' ? 'selected' : '' }}>{{ __('Refund Pending') }}</option>
                                                    <option value="partially_refunded" {{ $order->payment_status === 'partially_refunded' ? 'selected' : '' }}>{{ __('Partially Refunded') }}</option>
                                                    <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">{{ __('Shipping Status') }}</label>
                                                <select name="shipping_status" class="form-control custom-control-modern">
                                                    <option value="pending" {{ $order->shipping_status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                    <option value="processing" {{ $order->shipping_status === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                                    <option value="shipped" {{ $order->shipping_status === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                                                    <option value="delivered" {{ $order->shipping_status === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                                    <option value="cancelled" {{ $order->shipping_status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                                </select>
                                            </div>

                                            <div class="col-md-12 form-group mb-0">
                                                <label class="font-weight-semibold">{{ __('Admin Note') }}</label>
                                                <textarea name="note" rows="3" class="form-control custom-control-modern"
                                                          placeholder="{{ __('Optional internal note...') }}"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-warning px-4">
                                                <i class="fas fa-save mr-1"></i> {{ __('Update Statuses') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Shipment Management --}}
                            <div class="card card-outline card-info shadow-sm">
                                <div class="card-header border-0">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-truck mr-2 text-info"></i>{{ __('Shipment Management') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('dashboard.orders.shipment.upsert', $order) }}" method="POST">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">{{ __('Carrier') }}</label>
                                                <input type="text" name="carrier" class="form-control custom-control-modern"
                                                       value="{{ old('carrier', $order->shipment->carrier ?? '') }}"
                                                       placeholder="{{ __('e.g. Aramex / SMSA / DHL') }}">
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">{{ __('Tracking Number') }}</label>
                                                <input type="text" name="tracking_number" class="form-control custom-control-modern"
                                                       value="{{ old('tracking_number', $order->shipment->tracking_number ?? '') }}"
                                                       placeholder="{{ __('Tracking number') }}">
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">{{ __('Shipment Status') }}</label>
                                                @php
                                                    $shipmentStatus = old('status', $order->shipment->status ?? $order->shipping_status ?? 'pending');
                                                @endphp
                                                <select name="status" class="form-control custom-control-modern">
                                                    <option value="pending" {{ $shipmentStatus === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                    <option value="processing" {{ $shipmentStatus === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                                    <option value="shipped" {{ $shipmentStatus === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                                                    <option value="delivered" {{ $shipmentStatus === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                                    <option value="cancelled" {{ $shipmentStatus === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">{{ __('Shipped At') }}</label>
                                                <input type="datetime-local" name="shipped_at" class="form-control custom-control-modern"
                                                       value="{{ old('shipped_at', optional($order->shipment?->shipped_at)->format('Y-m-d\TH:i')) }}">
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">{{ __('Delivered At') }}</label>
                                                <input type="datetime-local" name="delivered_at" class="form-control custom-control-modern"
                                                       value="{{ old('delivered_at', optional($order->shipment?->delivered_at)->format('Y-m-d\TH:i')) }}">
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">{{ __('Current Location') }}</label>
                                                <input type="text" name="current_location" class="form-control custom-control-modern"
                                                       value="{{ old('current_location', $order->shipment->current_location ?? '') }}"
                                                       placeholder="{{ __('Current shipment location') }}">
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">{{ __('Carrier Status') }}</label>
                                                <input type="text" name="carrier_status" class="form-control custom-control-modern"
                                                       value="{{ old('carrier_status', $order->shipment->carrier_status ?? '') }}"
                                                       placeholder="{{ __('Carrier status text') }}">
                                            </div>

                                            <div class="col-md-12 form-group mb-0">
                                                <label class="font-weight-semibold">{{ __('Shipment Notes') }}</label>
                                                <textarea name="notes" rows="3" class="form-control custom-control-modern"
                                                          placeholder="{{ __('Optional shipment note...') }}">{{ old('notes', $order->shipment->notes ?? '') }}</textarea>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-info px-4">
                                                <i class="fas fa-truck mr-1"></i> {{ __('Save Shipment') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Cancel Order --}}
                            @if($order->status !== 'cancelled')
                                <div class="card card-outline card-danger shadow-sm">
                                    <div class="card-header border-0">
                                        <h3 class="card-title font-weight-bold">
                                            <i class="fas fa-times-circle mr-2 text-danger"></i>{{ __('Cancel Order') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('dashboard.orders.cancel', $order) }}" method="POST">
                                            @csrf

                                            <div class="form-group">
                                                <label class="font-weight-semibold">{{ __('Cancellation Reason') }}</label>
                                                <textarea name="reason" class="form-control custom-control-modern" rows="3"
                                                          placeholder="{{ __('Write a clear reason for cancellation...') }}">{{ old('reason') }}</textarea>
                                            </div>

                                            <div class="form-group form-check mb-3">
                                                <input type="checkbox"
                                                       name="refund_if_paid"
                                                       value="1"
                                                       class="form-check-input"
                                                       id="refund_if_paid"
                                                       {{ old('refund_if_paid', '1') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="refund_if_paid">
                                                    {{ __('Refund payment if the order is already paid') }}
                                                </label>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label class="font-weight-semibold">{{ __('Refund Amount (optional)') }}</label>
                                                <input type="number"
                                                       step="0.01"
                                                       min="0.01"
                                                       name="refund_amount"
                                                       value="{{ old('refund_amount') }}"
                                                       class="form-control custom-control-modern"
                                                       placeholder="{{ __('Leave empty for full refund') }}">
                                            </div>

                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-danger px-4"
                                                        onclick="return confirm(@js(__('Are you sure you want to cancel this order?')))">
                                                    <i class="fas fa-ban mr-1"></i> {{ __('Cancel Order') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Order Items --}}
                        <div class="card shadow-sm">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-box-open mr-2 text-dark"></i>{{ __('Order Items') }}
                                </h3>
                                <span class="badge badge-light">{{ $order->items->count() }} item(s)</span>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover align-middle mb-0 order-items-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Product') }}</th>
                                            <th>{{ __('SKU') }}</th>
                                            <th class="text-center">{{ __('Qty') }}</th>
                                            <th class="text-right">{{ __('Unit Price') }}</th>
                                            <th class="text-right">{{ __('Tax') }}</th>
                                            <th class="text-right">{{ __('Line Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="font-weight-bold">{{ $item->product_name_snapshot }}</div>

                                                    @if($item->product)
                                                        <small class="text-muted d-block">Product ID: {{ $item->product->id }}</small>
                                                    @endif

                                                    @if($item->color)
                                                        <small class="text-muted d-block">Color: {{ $item->color }}</small>
                                                    @endif

                                                    @if($item->size)
                                                        <small class="text-muted d-block">Size: {{ $item->size }}</small>
                                                    @endif

                                                    @if(!empty($item->variant_options))
                                                        @foreach($item->variant_options as $option)
                                                            <small class="text-muted d-block">
                                                                {{ $option['name'] ?? __('Option') }}: {{ $option['value'] ?? '-' }}
                                                            </small>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td>{{ $item->sku_snapshot ?: '-' }}</td>
                                                <td class="text-center">{{ (int) $item->quantity }}</td>
                                                <td class="text-right">{{ number_format((float) $item->unit_price, 2) }} {{ $currency }}</td>
                                                <td class="text-right">{{ number_format((float) $item->tax_amount, 2) }} {{ $currency }}</td>
                                                <td class="text-right font-weight-bold">{{ number_format((float) $item->line_total, 2) }} {{ $currency }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">{{ __('No items found.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer bg-white">
                                <div class="row">
                                    <div class="col-md-5 ml-auto">
                                        <table class="table table-sm table-borderless mb-0 totals-table">
                                            <tr>
                                                <th>{{ __('Subtotal') }}</th>
                                                <td class="text-right">{{ number_format((float) $order->subtotal, 2) }} {{ $currency }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Discount') }}</th>
                                                <td class="text-right text-danger">- {{ number_format((float) $order->discount, 2) }} {{ $currency }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Shipping') }}</th>
                                                <td class="text-right">{{ number_format((float) $order->shipping_fee, 2) }} {{ $currency }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('VAT') }}</th>
                                                <td class="text-right">{{ number_format((float) $order->tax_total, 2) }} {{ $currency }}</td>
                                            </tr>
                                            <tr class="total-row">
                                                <th>{{ __('Total') }}</th>
                                                <td class="text-right font-weight-bold">{{ number_format((float) $order->total, 2) }} {{ $currency }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status Timeline --}}
                        <div class="card shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-history mr-2 text-secondary"></i>{{ __('Status Timeline') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                @forelse($order->statusLogs as $log)
                                    <div class="timeline-item">
                                        <div class="timeline-dot"></div>
                                        <div class="timeline-content">
                                            <div class="font-weight-bold">
                                                {{ $log->from_status ? status_label($log->from_status) : __('N/A') }}
                                                <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                {{ $log->to_status ? status_label($log->to_status) : __('N/A') }}
                                            </div>
                                            <div class="text-muted small mt-1">
                                                By: {{ $log->changer->name ?? __('System') }}
                                                • {{ $log->created_at?->format('d M Y, h:i A') }}
                                            </div>
                                            @if($log->note)
                                                <div class="mt-2">{{ $log->note }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">{{ __('No status logs found.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT SIDE --}}
                    <div class="col-lg-4">

                        {{-- Customer --}}
                        <div class="card card-outline card-info shadow-sm sticky-card">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-user mr-2 text-info"></i>{{ __('Customer') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                @if($order->user)
                                    <div class="data-line"><span>{{ __('Name') }}</span><strong>{{ $order->user->name }}</strong></div>
                                    <div class="data-line"><span>{{ __('Email') }}</span><strong>{{ $order->user->email }}</strong></div>
                                    <div class="data-line"><span>{{ __('Phone') }}</span><strong>{{ $order->user->phone ?? '-' }}</strong></div>
                                    <div class="data-line"><span>{{ __('User ID') }}</span><strong>#{{ $order->user->id }}</strong></div>
                                @else
                                    <div class="data-line"><span>{{ __('Guest Email') }}</span><strong>{{ $order->guest_email ?? '-' }}</strong></div>
                                    <div class="data-line"><span>{{ __('Guest Phone') }}</span><strong>{{ $order->guest_phone ?? '-' }}</strong></div>
                                @endif
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="card card-outline card-secondary shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-map-marker-alt mr-2 text-secondary"></i>{{ __('Shipping Address') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                @if($order->addressSnapshot)
                                    <div class="address-block">
                                        <div class="font-weight-bold">{{ $order->addressSnapshot->full_name }}</div>
                                        <div>{{ $order->addressSnapshot->phone }}</div>
                                        <div>{{ $order->addressSnapshot->street }}</div>
                                        <div>{{ $order->addressSnapshot->district }}</div>
                                        <div>{{ $order->addressSnapshot->city }}, {{ $order->addressSnapshot->country }}</div>

                                        @if($order->addressSnapshot->postal_code)
                                            <div>Postal Code: {{ $order->addressSnapshot->postal_code }}</div>
                                        @endif

                                        @if($order->addressSnapshot->building_no)
                                            <div>Building No: {{ $order->addressSnapshot->building_no }}</div>
                                        @endif

                                        @if($order->addressSnapshot->apartment_no)
                                            <div>Apartment No: {{ $order->addressSnapshot->apartment_no }}</div>
                                        @endif

                                        @if($order->addressSnapshot->notes)
                                            <div class="soft-section mt-3">
                                                <strong>{{ __('Notes:') }}</strong>
                                                <div class="text-muted">{{ $order->addressSnapshot->notes }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-muted mb-0">{{ __('No address snapshot found.') }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Payments --}}
                        <div class="card card-outline card-success shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-credit-card mr-2 text-success"></i>{{ __('Payments') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                @forelse($order->payments as $payment)
                                    @php
                                        $paymentBadge = $paymentStatusMap[$payment->status] ?? ['label' => status_label($payment->status), 'class' => 'secondary'];
                                    @endphp

                                    <div class="mini-panel">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <strong>{{ strtoupper($payment->gateway) }}</strong>
                                            <span class="badge badge-{{ $paymentBadge['class'] }}">
                                                {{ $paymentBadge['label'] }}
                                            </span>
                                        </div>

                                        <div class="small text-muted mb-1">{{ __('Amount') }}</div>
                                        <div class="font-weight-bold mb-2">
                                            {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}
                                        </div>

                                        <div><strong>{{ __('Transaction ID:') }}</strong> {{ $payment->transaction_id ?: '-' }}</div>
                                        <div><strong>{{ __('Paid At:') }}</strong> {{ $payment->paid_at?->format('d M Y, h:i A') ?: '-' }}</div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">{{ __('No payments found.') }}</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Shipment --}}
                        <div class="card card-outline card-warning shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-shipping-fast mr-2 text-warning"></i>{{ __('Shipment') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                @if($order->shipment)
                                    @php
                                        $shipmentBadge = $shippingStatusMap[$order->shipment->status ?? 'pending'] ?? ['label' => status_label($order->shipment->status ?? 'pending'), 'class' => 'secondary'];
                                    @endphp

                                    <div class="mb-2">
                                        <span class="badge badge-{{ $shipmentBadge['class'] }} badge-pill px-3 py-2">
                                            {{ $shipmentBadge['label'] }}
                                        </span>
                                    </div>

                                    <div class="data-line"><span>{{ __('Carrier') }}</span><strong>{{ $order->shipment->carrier ?: '-' }}</strong></div>
                                    <div class="data-line"><span>{{ __('Tracking No.') }}</span><strong>{{ $order->shipment->tracking_number ?: '-' }}</strong></div>
                                    <div class="data-line"><span>{{ __('Location') }}</span><strong>{{ $order->shipment->current_location ?: '-' }}</strong></div>
                                    <div class="data-line"><span>{{ __('Carrier Status') }}</span><strong>{{ $order->shipment->carrier_status ?: '-' }}</strong></div>
                                    <div class="data-line"><span>{{ __('Shipped At') }}</span><strong>{{ $order->shipment->shipped_at?->format('d M Y, h:i A') ?: '-' }}</strong></div>
                                    <div class="data-line"><span>{{ __('Delivered At') }}</span><strong>{{ $order->shipment->delivered_at?->format('d M Y, h:i A') ?: '-' }}</strong></div>

                                    @if($order->shipment->notes)
                                        <div class="soft-section mt-3">
                                            <strong>{{ __('Notes:') }}</strong>
                                            <div class="text-muted">{{ $order->shipment->notes }}</div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">{{ __('No shipment record found.') }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Returns / Refunds --}}
                        <div class="card card-outline card-danger shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-undo-alt mr-2 text-danger"></i>{{ __('Returns / Refunds') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                @forelse($order->returnRequests as $return)
                                    @php
                                        $returnBadge = $returnStatusMap[$return->status] ?? ['label' => status_label($return->status), 'class' => 'secondary'];
                                    @endphp

                                    <div class="mini-panel">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <strong>Return #{{ $return->id }}</strong>
                                            <span class="badge badge-{{ $returnBadge['class'] }}">
                                                {{ $returnBadge['label'] }}
                                            </span>
                                        </div>

                                        <div><strong>{{ __('Reason:') }}</strong> {{ $return->reason ?: '-' }}</div>
                                        <div><strong>{{ __('Requested At:') }}</strong> {{ $return->requested_at?->format('d M Y, h:i A') ?: '-' }}</div>
                                        <div><strong>{{ __('Processed At:') }}</strong> {{ $return->processed_at?->format('d M Y, h:i A') ?: '-' }}</div>

                                        @if($return->refunds && $return->refunds->count())
                                            <hr>
                                            @foreach($return->refunds as $refund)
                                                @php
                                                    $refundBadge = $refundStatusMap[$refund->status] ?? ['label' => status_label($refund->status), 'class' => 'secondary'];
                                                @endphp
                                                <div class="refund-row">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>Refund #{{ $refund->id }}</span>
                                                        <span class="badge badge-{{ $refundBadge['class'] }}">
                                                            {{ $refundBadge['label'] }}
                                                        </span>
                                                    </div>
                                                    <div class="text-muted small mt-1">
                                                        {{ number_format((float) $refund->amount, 2) }} {{ $currency }}
                                                        @if($refund->transaction_id)
                                                            • {{ $refund->transaction_id }}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">{{ __('No returns found.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .order-show-page .gap-2 {
            gap: .5rem;
        }

        .order-show-page .alert-modern {
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,.05);
            border: 0;
        }

        .order-show-page .summary-card {
            border-radius: 16px;
        }

        .order-show-page .summary-card .card-body {
            padding: 1.25rem 1.25rem;
        }

        .order-show-page .summary-label {
            font-size: .85rem;
            color: #6c757d;
            margin-bottom: .45rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .order-show-page .summary-value {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: .4rem;
        }

        .order-show-page .summary-meta {
            color: #6c757d;
            font-size: .9rem;
        }

        .order-show-page .card {
            border-radius: 16px;
            overflow: hidden;
        }

        .order-show-page .card-header {
            background: #fff;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .order-show-page .info-box-lite {
            background: #f8f9fb;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: .9rem 1rem;
            height: 100%;
        }

        .order-show-page .info-title {
            color: #6c757d;
            font-size: .82rem;
            font-weight: 600;
            margin-bottom: .35rem;
        }

        .order-show-page .info-text {
            font-size: .96rem;
            font-weight: 600;
            color: #212529;
            word-break: break-word;
        }

        .order-show-page .soft-section {
            background: #f8f9fb;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: .9rem 1rem;
        }

        .order-show-page .danger-soft {
            background: #fff5f5;
            border-color: #ffd6d6;
        }

        .order-show-page .custom-control-modern {
            min-height: 44px;
            border-radius: 12px;
            border-color: #dfe5ec;
            box-shadow: none !important;
        }

        .order-show-page textarea.custom-control-modern {
            min-height: auto;
        }

        .order-show-page .order-items-table thead th {
            background: #f8f9fb;
            border-top: 0;
            font-size: .84rem;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: .03em;
        }

        .order-show-page .order-items-table td,
        .order-show-page .order-items-table th {
            vertical-align: top;
            padding: .95rem .85rem;
        }

        .order-show-page .totals-table tr th,
        .order-show-page .totals-table tr td {
            padding: .45rem .25rem;
        }

        .order-show-page .totals-table .total-row th,
        .order-show-page .totals-table .total-row td {
            font-size: 1rem;
            border-top: 2px solid #e9ecef;
            padding-top: .75rem;
        }

        .order-show-page .timeline-item {
            position: relative;
            padding-left: 2rem;
            padding-bottom: 1.25rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-show-page .timeline-item:last-child {
            border-bottom: 0;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .order-show-page .timeline-dot {
            position: absolute;
            top: .35rem;
            left: .2rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
            box-shadow: 0 0 0 4px rgba(0,123,255,.12);
        }

        .order-show-page .timeline-content {
            background: #fafbfc;
            border: 1px solid #eef1f5;
            border-radius: 12px;
            padding: .9rem 1rem;
        }

        .order-show-page .data-line {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: .55rem 0;
            border-bottom: 1px dashed #eceff3;
        }

        .order-show-page .data-line:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .order-show-page .data-line span {
            color: #6c757d;
            font-size: .9rem;
        }

        .order-show-page .data-line strong {
            text-align: right;
            word-break: break-word;
        }

        .order-show-page .mini-panel {
            background: #fafbfc;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: .9rem 1rem;
            margin-bottom: .85rem;
        }

        .order-show-page .mini-panel:last-child {
            margin-bottom: 0;
        }

        .order-show-page .refund-row {
            background: #fff;
            border: 1px solid #eef1f4;
            border-radius: 10px;
            padding: .65rem .75rem;
            margin-top: .6rem;
        }

        .order-show-page .address-block {
            line-height: 1.8;
        }

        .order-show-page .sticky-card {
            position: relative;
        }

        @media (min-width: 992px) {
            .order-show-page .sticky-card {
                position: sticky;
                top: 18px;
                z-index: 2;
            }
        }
    </style>
@endpush
