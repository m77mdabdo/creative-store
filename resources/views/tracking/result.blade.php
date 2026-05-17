<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Result</title>
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/tokens.css') }}">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
    <div class="content-wrapper" style="min-height: 100vh;">
        <div class="content-header">
            <div class="container">
                <div class="row mb-2 pt-4">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tracking Result</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('tracking.search-form') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> New Search
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container">

                @if(! $shipment)
                    <div class="alert alert-warning">
                        No shipment found with the provided tracking details.
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-7">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Shipment Overview</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered mb-0">
                                        <tr>
                                            <th width="220">Order Number</th>
                                            <td>{{ $shipment->order?->order_number ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tracking Number</th>
                                            <td>{{ $shipment->tracking_number ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shipping Method</th>
                                            <td>{{ $shipment->method?->name ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Carrier</th>
                                            <td>{{ $shipment->carrier ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="{{ $shipment->status_badge_class }}">
                                                    {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Current Location</th>
                                            <td>{{ $shipment->current_location ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Carrier Status</th>
                                            <td>{{ $shipment->carrier_status ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shipped At</th>
                                            <td>{{ $shipment->shipped_at?->format('Y-m-d h:i A') ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Delivered At</th>
                                            <td>{{ $shipment->delivered_at?->format('Y-m-d h:i A') ?: 'Not delivered yet' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Delivery Address</h3>
                                </div>
                                <div class="card-body">
                                    @php
                                        $snapshot = $shipment->order?->addressSnapshot ?? null;
                                    @endphp

                                    @if($snapshot)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Name:</strong> {{ $snapshot->full_name ?? '—' }}</p>
                                                <p><strong>Phone:</strong> {{ $snapshot->phone ?? '—' }}</p>
                                                <p><strong>City:</strong> {{ $snapshot->city ?? '—' }}</p>
                                                <p><strong>District:</strong> {{ $snapshot->district ?? '—' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Address Line 1:</strong> {{ $snapshot->address_line1 ?? '—' }}</p>
                                                <p><strong>Address Line 2:</strong> {{ $snapshot->address_line2 ?? '—' }}</p>
                                                <p><strong>Postal Code:</strong> {{ $snapshot->postal_code ?? '—' }}</p>
                                                <p><strong>Country:</strong> {{ $snapshot->country ?? '—' }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">No delivery address available.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="card card-outline card-dark">
                                <div class="card-header">
                                    <h3 class="card-title">Tracking Timeline</h3>
                                </div>
                                <div class="card-body">
                                    @forelse($shipment->trackingLogs as $log)
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $log->status ?? 'update')) }}</strong>

                                                    @if($log->location || $log->carrier_status)
                                                        <div class="text-muted mt-1">
                                                            {{ $log->location ?: 'Unknown location' }}
                                                            @if($log->carrier_status)
                                                                | {{ $log->carrier_status }}
                                                            @endif
                                                        </div>
                                                    @endif

                                                    @if($log->note)
                                                        <div class="mt-2">{{ $log->note }}</div>
                                                    @endif
                                                </div>

                                                <small class="text-muted ml-3">
                                                    {{ $log->tracked_at?->format('Y-m-d H:i') ?: '—' }}
                                                </small>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">No tracking updates available.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Delivery Monitoring</h3>
                                </div>
                                <div class="card-body">
                                    @forelse($shipment->deliveryAttempts as $attempt)
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div>
                                                <strong>Attempt #{{ $attempt->attempt_no }}</strong>
                                                <span class="{{ $attempt->status_badge_class }} ml-2">
                                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                                </span>
                                            </div>

                                            <div class="mt-2 text-muted">
                                                <div><strong>Driver:</strong> {{ $attempt->driver_name ?: '—' }}</div>
                                                <div><strong>Location:</strong> {{ $attempt->location ?: '—' }}</div>
                                                <div><strong>Failure Reason:</strong> {{ $attempt->failure_reason ?: '—' }}</div>
                                            </div>

                                            @if($attempt->notes)
                                                <div class="mt-2">{{ $attempt->notes }}</div>
                                            @endif

                                            <div class="text-muted mt-2">
                                                <small>Attempted: {{ $attempt->attempted_at?->format('Y-m-d h:i A') ?: '—' }}</small><br>
                                                <small>Next Attempt: {{ $attempt->next_attempt_at?->format('Y-m-d h:i A') ?: '—' }}</small>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">No delivery attempt records available.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Delivery Result</h3>
                                </div>
                                <div class="card-body">
                                    @if($shipment->status === 'delivered')
                                        <div class="alert alert-success mb-0">
                                            Your shipment has been delivered successfully.
                                        </div>
                                    @elseif(in_array($shipment->status, ['pending', 'picked', 'in_transit']))
                                        <div class="alert alert-info mb-0">
                                            Your shipment is currently on the way.
                                        </div>
                                    @elseif($shipment->status === 'failed_return')
                                        <div class="alert alert-danger mb-0">
                                            Delivery was not completed successfully.
                                        </div>
                                    @else
                                        <div class="alert alert-secondary mb-0">
                                            Shipment status is currently unavailable.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('AdminLTE/dist/js/adminlte.min.js') }}"></script>
</body>
</html>
