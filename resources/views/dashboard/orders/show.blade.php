@extends('dashboard.app.layout')

@php
    $currency = $order->currency ?? 'SAR';

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

    $orderStatus = $orderStatusMap[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'secondary'];
    $paymentStatus = $paymentStatusMap[$order->payment_status] ?? ['label' => ucfirst($order->payment_status), 'class' => 'secondary'];
    $shippingStatus = $shippingStatusMap[$order->shipping_status] ?? ['label' => ucfirst($order->shipping_status), 'class' => 'secondary'];

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
                            <i class="fas fa-arrow-left mr-1"></i> Back to Orders
                        </a>

                        <a href="{{ route('dashboard.orders.invoice', $order) }}" target="_blank"
                           class="btn btn-outline-dark mb-2">
                            <i class="fas fa-file-invoice mr-1"></i> Invoice
                        </a>
                    </div>
                </div>

                <ol class="breadcrumb float-sm-right mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.home') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.orders.index') }}">Orders</a>
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
                            Please review the following:
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
                                <div class="summary-label">Order Status</div>
                                <div class="summary-value">
                                    <span class="badge badge-{{ $orderStatus['class'] }} badge-pill px-3 py-2">
                                        {{ $orderStatus['label'] }}
                                    </span>
                                </div>
                                <div class="summary-meta">Current lifecycle state</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card summary-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="summary-label">Payment Status</div>
                                <div class="summary-value">
                                    <span class="badge badge-{{ $paymentStatus['class'] }} badge-pill px-3 py-2">
                                        {{ $paymentStatus['label'] }}
                                    </span>
                                </div>
                                <div class="summary-meta">{{ strtoupper($order->payment_method ?? '-') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="card summary-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="summary-label">Shipping Status</div>
                                <div class="summary-value">
                                    <span class="badge badge-{{ $shippingStatus['class'] }} badge-pill px-3 py-2">
                                        {{ $shippingStatus['label'] }}
                                    </span>
                                </div>
                                <div class="summary-meta">{{ $order->shippingMethod->name ?? 'No shipping method' }}</div>
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
                                    <i class="fas fa-receipt mr-2 text-primary"></i>Order Summary
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row info-grid">
                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">Order Number</div>
                                            <div class="info-text">{{ $order->order_number }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">Placed At</div>
                                            <div class="info-text">{{ $placedAt }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">Currency</div>
                                            <div class="info-text">{{ $currency }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">Payment Method</div>
                                            <div class="info-text">{{ strtoupper($order->payment_method ?? '-') }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">Shipping Method</div>
                                            <div class="info-text">{{ $order->shippingMethod->name ?? '-' }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-box-lite">
                                            <div class="info-title">Coupon</div>
                                            <div class="info-text">{{ $order->coupon->code ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                @if($order->estimated_delivery_from || $order->estimated_delivery_to)
                                    <div class="soft-section mt-2">
                                        <div class="font-weight-bold mb-1">Estimated Delivery</div>
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
                                            <i class="fas fa-ban mr-1"></i> Cancellation Details
                                        </div>
                                        <div><strong>Cancelled At:</strong> {{ $order->cancelled_at?->format('d M Y, h:i A') }}</div>
                                        <div><strong>Reason:</strong> {{ $order->cancellation_reason ?: '-' }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($canEdit ?? false)
                            {{-- Quick Management --}}
                            <div class="card card-outline card-warning shadow-sm">
                                <div class="card-header border-0">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-edit mr-2 text-warning"></i>Update Order Statuses
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('dashboard.orders.update-statuses', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">Order Status</label>
                                                <select name="status" class="form-control custom-control-modern">
                                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">Payment Status</label>
                                                <select name="payment_status" class="form-control custom-control-modern">
                                                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                                    <option value="refund_pending" {{ $order->payment_status === 'refund_pending' ? 'selected' : '' }}>Refund Pending</option>
                                                    <option value="partially_refunded" {{ $order->payment_status === 'partially_refunded' ? 'selected' : '' }}>Partially Refunded</option>
                                                    <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">Shipping Status</label>
                                                <select name="shipping_status" class="form-control custom-control-modern">
                                                    <option value="pending" {{ $order->shipping_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="processing" {{ $order->shipping_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                    <option value="shipped" {{ $order->shipping_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                    <option value="delivered" {{ $order->shipping_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                    <option value="cancelled" {{ $order->shipping_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </div>

                                            <div class="col-md-12 form-group mb-0">
                                                <label class="font-weight-semibold">Admin Note</label>
                                                <textarea name="note" rows="3" class="form-control custom-control-modern"
                                                          placeholder="Optional internal note..."></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-warning px-4">
                                                <i class="fas fa-save mr-1"></i> Update Statuses
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Shipment Management --}}
                            <div class="card card-outline card-info shadow-sm">
                                <div class="card-header border-0">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-truck mr-2 text-info"></i>Shipment Management
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('dashboard.orders.shipment.upsert', $order) }}" method="POST">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Carrier</label>
                                                <input type="text" name="carrier" class="form-control custom-control-modern"
                                                       value="{{ old('carrier', $order->shipment->carrier ?? '') }}"
                                                       placeholder="e.g. Aramex / SMSA / DHL">
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Tracking Number</label>
                                                <input type="text" name="tracking_number" class="form-control custom-control-modern"
                                                       value="{{ old('tracking_number', $order->shipment->tracking_number ?? '') }}"
                                                       placeholder="Tracking number">
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">Shipment Status</label>
                                                @php
                                                    $shipmentStatus = old('status', $order->shipment->status ?? $order->shipping_status ?? 'pending');
                                                @endphp
                                                <select name="status" class="form-control custom-control-modern">
                                                    <option value="pending" {{ $shipmentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="processing" {{ $shipmentStatus === 'processing' ? 'selected' : '' }}>Processing</option>
                                                    <option value="shipped" {{ $shipmentStatus === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                    <option value="delivered" {{ $shipmentStatus === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                    <option value="cancelled" {{ $shipmentStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">Shipped At</label>
                                                <input type="datetime-local" name="shipped_at" class="form-control custom-control-modern"
                                                       value="{{ old('shipped_at', optional($order->shipment?->shipped_at)->format('Y-m-d\TH:i')) }}">
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-semibold">Delivered At</label>
                                                <input type="datetime-local" name="delivered_at" class="form-control custom-control-modern"
                                                       value="{{ old('delivered_at', optional($order->shipment?->delivered_at)->format('Y-m-d\TH:i')) }}">
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Current Location</label>
                                                <input type="text" name="current_location" class="form-control custom-control-modern"
                                                       value="{{ old('current_location', $order->shipment->current_location ?? '') }}"
                                                       placeholder="Current shipment location">
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-semibold">Carrier Status</label>
                                                <input type="text" name="carrier_status" class="form-control custom-control-modern"
                                                       value="{{ old('carrier_status', $order->shipment->carrier_status ?? '') }}"
                                                       placeholder="Carrier status text">
                                            </div>

                                            <div class="col-md-12 form-group mb-0">
                                                <label class="font-weight-semibold">Shipment Notes</label>
                                                <textarea name="notes" rows="3" class="form-control custom-control-modern"
                                                          placeholder="Optional shipment note...">{{ old('notes', $order->shipment->notes ?? '') }}</textarea>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-info px-4">
                                                <i class="fas fa-truck mr-1"></i> Save Shipment
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
                                            <i class="fas fa-times-circle mr-2 text-danger"></i>Cancel Order
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('dashboard.orders.cancel', $order) }}" method="POST">
                                            @csrf

                                            <div class="form-group">
                                                <label class="font-weight-semibold">Cancellation Reason</label>
                                                <textarea name="reason" class="form-control custom-control-modern" rows="3"
                                                          placeholder="Write a clear reason for cancellation...">{{ old('reason') }}</textarea>
                                            </div>

                                            <div class="form-group form-check mb-3">
                                                <input type="checkbox"
                                                       name="refund_if_paid"
                                                       value="1"
                                                       class="form-check-input"
                                                       id="refund_if_paid"
                                                       {{ old('refund_if_paid', '1') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="refund_if_paid">
                                                    Refund payment if the order is already paid
                                                </label>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label class="font-weight-semibold">Refund Amount (optional)</label>
                                                <input type="number"
                                                       step="0.01"
                                                       min="0.01"
                                                       name="refund_amount"
                                                       value="{{ old('refund_amount') }}"
                                                       class="form-control custom-control-modern"
                                                       placeholder="Leave empty for full refund">
                                            </div>

                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-danger px-4"
                                                        onclick="return confirm('Are you sure you want to cancel this order?')">
                                                    <i class="fas fa-ban mr-1"></i> Cancel Order
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
                                    <i class="fas fa-box-open mr-2 text-dark"></i>Order Items
                                </h3>
                                <span class="badge badge-light">{{ $order->items->count() }} item(s)</span>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover align-middle mb-0 order-items-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-right">Unit Price</th>
                                            <th class="text-right">Tax</th>
                                            <th class="text-right">Line Total</th>
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
                                                                {{ $option['name'] ?? 'Option' }}: {{ $option['value'] ?? '-' }}
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
                                                <td colspan="6" class="text-center text-muted py-4">No items found.</td>
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
                                                <th>Subtotal</th>
                                                <td class="text-right">{{ number_format((float) $order->subtotal, 2) }} {{ $currency }}</td>
                                            </tr>
                                            <tr>
                                                <th>Discount</th>
                                                <td class="text-right text-danger">- {{ number_format((float) $order->discount, 2) }} {{ $currency }}</td>
                                            </tr>
                                            <tr>
                                                <th>Shipping</th>
                                                <td class="text-right">{{ number_format((float) $order->shipping_fee, 2) }} {{ $currency }}</td>
                                            </tr>
                                            <tr>
                                                <th>VAT</th>
                                                <td class="text-right">{{ number_format((float) $order->tax_total, 2) }} {{ $currency }}</td>
                                            </tr>
                                            <tr class="total-row">
                                                <th>Total</th>
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
                                    <i class="fas fa-history mr-2 text-secondary"></i>Status Timeline
                                </h3>
                            </div>
                            <div class="card-body">
                                @forelse($order->statusLogs as $log)
                                    <div class="timeline-item">
                                        <div class="timeline-dot"></div>
                                        <div class="timeline-content">
                                            <div class="font-weight-bold">
                                                {{ $log->from_status ?: 'N/A' }}
                                                <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                {{ $log->to_status ?: 'N/A' }}
                                            </div>
                                            <div class="text-muted small mt-1">
                                                By: {{ $log->changer->name ?? 'System' }}
                                                • {{ $log->created_at?->format('d M Y, h:i A') }}
                                            </div>
                                            @if($log->note)
                                                <div class="mt-2">{{ $log->note }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No status logs found.</p>
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
                                    <i class="fas fa-user mr-2 text-info"></i>Customer
                                </h3>
                            </div>
                            <div class="card-body">
                                @if($order->user)
                                    <div class="data-line"><span>Name</span><strong>{{ $order->user->name }}</strong></div>
                                    <div class="data-line"><span>Email</span><strong>{{ $order->user->email }}</strong></div>
                                    <div class="data-line"><span>Phone</span><strong>{{ $order->user->phone ?? '-' }}</strong></div>
                                    <div class="data-line"><span>User ID</span><strong>#{{ $order->user->id }}</strong></div>
                                @else
                                    <div class="data-line"><span>Guest Email</span><strong>{{ $order->guest_email ?? '-' }}</strong></div>
                                    <div class="data-line"><span>Guest Phone</span><strong>{{ $order->guest_phone ?? '-' }}</strong></div>
                                @endif
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="card card-outline card-secondary shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-map-marker-alt mr-2 text-secondary"></i>Shipping Address
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
                                                <strong>Notes:</strong>
                                                <div class="text-muted">{{ $order->addressSnapshot->notes }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No address snapshot found.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Payments --}}
                        <div class="card card-outline card-success shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-credit-card mr-2 text-success"></i>Payments
                                </h3>
                            </div>
                            <div class="card-body">
                                @forelse($order->payments as $payment)
                                    @php
                                        $paymentBadge = $paymentStatusMap[$payment->status] ?? ['label' => ucfirst($payment->status), 'class' => 'secondary'];
                                    @endphp

                                    <div class="mini-panel">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <strong>{{ strtoupper($payment->gateway) }}</strong>
                                            <span class="badge badge-{{ $paymentBadge['class'] }}">
                                                {{ $paymentBadge['label'] }}
                                            </span>
                                        </div>

                                        <div class="small text-muted mb-1">Amount</div>
                                        <div class="font-weight-bold mb-2">
                                            {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}
                                        </div>

                                        <div><strong>Transaction ID:</strong> {{ $payment->transaction_id ?: '-' }}</div>
                                        <div><strong>Paid At:</strong> {{ $payment->paid_at?->format('d M Y, h:i A') ?: '-' }}</div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No payments found.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Shipment --}}
                        <div class="card card-outline card-warning shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-shipping-fast mr-2 text-warning"></i>Shipment
                                </h3>
                            </div>
                            <div class="card-body">
                                @if($order->shipment)
                                    @php
                                        $shipmentBadge = $shippingStatusMap[$order->shipment->status ?? 'pending'] ?? ['label' => ucfirst($order->shipment->status ?? 'pending'), 'class' => 'secondary'];
                                    @endphp

                                    <div class="mb-2">
                                        <span class="badge badge-{{ $shipmentBadge['class'] }} badge-pill px-3 py-2">
                                            {{ $shipmentBadge['label'] }}
                                        </span>
                                    </div>

                                    <div class="data-line"><span>Carrier</span><strong>{{ $order->shipment->carrier ?: '-' }}</strong></div>
                                    <div class="data-line"><span>Tracking No.</span><strong>{{ $order->shipment->tracking_number ?: '-' }}</strong></div>
                                    <div class="data-line"><span>Location</span><strong>{{ $order->shipment->current_location ?: '-' }}</strong></div>
                                    <div class="data-line"><span>Carrier Status</span><strong>{{ $order->shipment->carrier_status ?: '-' }}</strong></div>
                                    <div class="data-line"><span>Shipped At</span><strong>{{ $order->shipment->shipped_at?->format('d M Y, h:i A') ?: '-' }}</strong></div>
                                    <div class="data-line"><span>Delivered At</span><strong>{{ $order->shipment->delivered_at?->format('d M Y, h:i A') ?: '-' }}</strong></div>

                                    @if($order->shipment->notes)
                                        <div class="soft-section mt-3">
                                            <strong>Notes:</strong>
                                            <div class="text-muted">{{ $order->shipment->notes }}</div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">No shipment record found.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Returns / Refunds --}}
                        <div class="card card-outline card-danger shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-undo-alt mr-2 text-danger"></i>Returns / Refunds
                                </h3>
                            </div>
                            <div class="card-body">
                                @forelse($order->returnRequests as $return)
                                    @php
                                        $returnBadge = $returnStatusMap[$return->status] ?? ['label' => ucfirst($return->status), 'class' => 'secondary'];
                                    @endphp

                                    <div class="mini-panel">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <strong>Return #{{ $return->id }}</strong>
                                            <span class="badge badge-{{ $returnBadge['class'] }}">
                                                {{ $returnBadge['label'] }}
                                            </span>
                                        </div>

                                        <div><strong>Reason:</strong> {{ $return->reason ?: '-' }}</div>
                                        <div><strong>Requested At:</strong> {{ $return->requested_at?->format('d M Y, h:i A') ?: '-' }}</div>
                                        <div><strong>Processed At:</strong> {{ $return->processed_at?->format('d M Y, h:i A') ?: '-' }}</div>

                                        @if($return->refunds && $return->refunds->count())
                                            <hr>
                                            @foreach($return->refunds as $refund)
                                                @php
                                                    $refundBadge = $refundStatusMap[$refund->status] ?? ['label' => ucfirst($refund->status), 'class' => 'secondary'];
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
                                    <p class="text-muted mb-0">No returns found.</p>
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
