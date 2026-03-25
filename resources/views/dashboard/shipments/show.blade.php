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
                    <a href="{{ route('dashboard.shipments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                    <a href="{{ route('dashboard.shipments.edit', $shipment) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i> Edit Shipment
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-7">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Shipment Details</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <tr>
                                    <th width="220">Shipment ID</th>
                                    <td>{{ $shipment->id }}</td>
                                </tr>
                                <tr>
                                    <th>Order</th>
                                    <td>{{ $shipment->order?->order_number ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Customer</th>
                                    <td>{{ $shipment->order?->user?->name ?? 'Guest' }}</td>
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
                                    <th>Tracking Number</th>
                                    <td>
                                        @if($shipment->tracking_number)
                                            <code>{{ $shipment->tracking_number }}</code>
                                        @else
                                            —
                                        @endif
                                    </td>
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
                                    <td>{{ $shipment->delivered_at?->format('Y-m-d h:i A') ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td>{{ $shipment->notes ?: '—' }}</td>
                                </tr>
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
                                        <p><strong>Full Name:</strong> {{ $snapshot->full_name ?? '—' }}</p>
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
                                <p class="text-muted mb-0">No delivery address snapshot available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Add Tracking Update</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dashboard.shipments.tracking.store', $shipment) }}">
                                @csrf

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="">Select status</option>
                                        @foreach(\App\Models\Shipment::statuses() as $status)
                                            <option value="{{ $status }}" @selected(old('status') === $status)>
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Location</label>
                                    <input
                                        type="text"
                                        name="location"
                                        value="{{ old('location') }}"
                                        class="form-control @error('location') is-invalid @enderror"
                                        placeholder="Current location"
                                    >
                                    @error('location')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Carrier Status</label>
                                    <input
                                        type="text"
                                        name="carrier_status"
                                        value="{{ old('carrier_status') }}"
                                        class="form-control @error('carrier_status') is-invalid @enderror"
                                        placeholder="Carrier status"
                                    >
                                    @error('carrier_status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Note</label>
                                    <textarea
                                        name="note"
                                        class="form-control @error('note') is-invalid @enderror"
                                        rows="4"
                                        placeholder="Write note..."
                                    >{{ old('note') }}</textarea>
                                    @error('note')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Add Tracking
                                </button>
                            </form>
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
                                            <strong>
                                                {{ ucfirst(str_replace('_', ' ', $log->status ?? 'update')) }}
                                            </strong>

                                            @if($log->location || $log->carrier_status)
                                                <div class="text-muted mt-1">
                                                    {{ $log->location ?: 'Unknown location' }}
                                                    @if($log->carrier_status)
                                                        | {{ $log->carrier_status }}
                                                    @endif
                                                </div>
                                            @endif

                                            @if($log->note)
                                                <div class="mt-2">
                                                    {{ $log->note }}
                                                </div>
                                            @endif
                                        </div>

                                        <small class="text-muted ml-3">
                                            {{ $log->tracked_at?->format('Y-m-d H:i') ?: ($log->created_at?->format('Y-m-d H:i') ?? '—') }}
                                        </small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No tracking logs.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Add Delivery Attempt</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dashboard.shipments.delivery-attempts.store', $shipment) }}">
                                @csrf

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        @foreach(\App\Models\DeliveryAttempt::statuses() as $status)
                                            <option value="{{ $status }}" @selected(old('status') === $status)>
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Driver Name</label>
                                            <input type="text" name="driver_name" value="{{ old('driver_name') }}" class="form-control" placeholder="Driver name">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Driver Phone</label>
                                            <input type="text" name="driver_phone" value="{{ old('driver_phone') }}" class="form-control" placeholder="Driver phone">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Location</label>
                                    <input type="text" name="location" value="{{ old('location') }}" class="form-control" placeholder="Current location">
                                </div>

                                <div class="form-group">
                                    <label>Failure Reason</label>
                                    <input type="text" name="failure_reason" value="{{ old('failure_reason') }}" class="form-control" placeholder="Reason if failed">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Attempted At</label>
                                            <input type="datetime-local" name="attempted_at" value="{{ old('attempted_at') }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Next Attempt At</label>
                                            <input type="datetime-local" name="next_attempt_at" value="{{ old('next_attempt_at') }}" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" rows="3" class="form-control" placeholder="Write attempt notes...">{{ old('notes') }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Add Attempt
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Delivery Attempts Timeline</h3>
                        </div>
                        <div class="card-body">
                            @forelse($shipment->deliveryAttempts as $attempt)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div>
                                                <strong>Attempt #{{ $attempt->attempt_no }}</strong>
                                                <span class="{{ $attempt->status_badge_class }} ml-2">
                                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                                </span>
                                            </div>

                                            <div class="text-muted mt-2">
                                                <div><strong>Driver:</strong> {{ $attempt->driver_name ?: '—' }}</div>
                                                <div><strong>Phone:</strong> {{ $attempt->driver_phone ?: '—' }}</div>
                                                <div><strong>Location:</strong> {{ $attempt->location ?: '—' }}</div>
                                                <div><strong>Failure Reason:</strong> {{ $attempt->failure_reason ?: '—' }}</div>
                                            </div>

                                            @if($attempt->notes)
                                                <div class="mt-2">
                                                    {{ $attempt->notes }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="text-right text-muted ml-3">
                                            <div>
                                                <small>
                                                    Attempted:
                                                    {{ $attempt->attempted_at?->format('Y-m-d h:i A') ?: '—' }}
                                                </small>
                                            </div>
                                            <div>
                                                <small>
                                                    Next:
                                                    {{ $attempt->next_attempt_at?->format('Y-m-d h:i A') ?: '—' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No delivery attempts yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
