@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Shipment #{{ $shipment->id }}</h1>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('dashboard.shipments.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="220">Shipment ID</th><td>{{ $shipment->id }}</td></tr>
                        <tr><th>Order</th><td>{{ $shipment->order?->order_number ?? '—' }}</td></tr>
                        <tr><th>Customer</th><td>{{ $shipment->order?->user?->name ?? 'Guest' }}</td></tr>
                        <tr><th>Shipping Method</th><td>{{ $shipment->method?->name ?? '—' }}</td></tr>
                        <tr><th>Carrier</th><td>{{ $shipment->carrier ?: '—' }}</td></tr>
                        <tr><th>Tracking Number</th><td>{{ $shipment->tracking_number ?: '—' }}</td></tr>
                        <tr><th>Status</th><td><span class="{{ $shipment->status_badge_class }}">{{ ucfirst(str_replace('_', ' ', $shipment->status)) }}</span></td></tr>
                        <tr><th>Current Location</th><td>{{ $shipment->current_location ?: '—' }}</td></tr>
                        <tr><th>Carrier Status</th><td>{{ $shipment->carrier_status ?: '—' }}</td></tr>
                        <tr><th>Shipped At</th><td>{{ $shipment->shipped_at?->format('Y-m-d h:i A') ?: '—' }}</td></tr>
                        <tr><th>Delivered At</th><td>{{ $shipment->delivered_at?->format('Y-m-d h:i A') ?: '—' }}</td></tr>
                        <tr><th>Notes</th><td>{{ $shipment->notes ?: '—' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Order Summary</h3>
                </div>
                <div class="card-body">
                    <p><strong>Order Number:</strong> {{ $shipment->order?->order_number ?? '—' }}</p>
                    <p><strong>Total:</strong> {{ $shipment->order?->total ?? '—' }}</p>
                    <p><strong>Order Status:</strong> {{ $shipment->order?->status ?? '—' }}</p>
                    <p><strong>Payment Status:</strong> {{ $shipment->order?->payment_status ?? '—' }}</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
