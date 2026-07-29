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
                        <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
                    </a>
                    <a href="{{ route('dashboard.shipments.edit', $shipment) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i> {{ __('Edit Shipment') }}
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
                    <strong>{{ __('Please fix the following errors:') }}</strong>
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
                            <h3 class="card-title">{{ __('Shipment Details') }}</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <tr>
                                    <th width="220">{{ __('Shipment ID') }}</th>
                                    <td>{{ $shipment->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Order') }}</th>
                                    <td>{{ $shipment->order?->order_number ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Customer') }}</th>
                                    <td>{{ $shipment->order?->user?->name ?? __('Guest') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Shipping Method') }}</th>
                                    <td>{{ $shipment->method?->label ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Carrier') }}</th>
                                    <td>{{ $shipment->carrier ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Tracking Number') }}</th>
                                    <td>
                                        @if($shipment->tracking_number)
                                            <code>{{ $shipment->tracking_number }}</code>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td>
                                        <span class="{{ $shipment->status_badge_class }}">
                                            {{ status_label($shipment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Current Location') }}</th>
                                    <td>{{ $shipment->current_location ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Carrier Status') }}</th>
                                    <td>{{ $shipment->carrier_status ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Shipped At') }}</th>
                                    <td>{{ $shipment->shipped_at?->format('Y-m-d h:i A') ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Delivered At') }}</th>
                                    <td>{{ $shipment->delivered_at?->format('Y-m-d h:i A') ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Notes') }}</th>
                                    <td>{{ $shipment->notes ?: '—' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Order Summary') }}</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ __('Order Number:') }}</strong> {{ $shipment->order?->order_number ?? '—' }}</p>
                            <p><strong>{{ __('Total:') }}</strong> {{ $shipment->order?->total ?? '—' }}</p>
                            <p><strong>{{ __('Order Status:') }}</strong> {{ $shipment->order?->status ?? '—' }}</p>
                            <p><strong>{{ __('Payment Status:') }}</strong> {{ $shipment->order?->payment_status ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Delivery Address') }}</h3>
                        </div>
                        <div class="card-body">
                            @php
                                $snapshot = $shipment->order?->addressSnapshot ?? null;
                            @endphp

                            @if($snapshot)
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>{{ __('Full Name:') }}</strong> {{ $snapshot->full_name ?? '—' }}</p>
                                        <p><strong>{{ __('Phone:') }}</strong> {{ $snapshot->phone ?? '—' }}</p>
                                        <p><strong>{{ __('City:') }}</strong> {{ $snapshot->city ?? '—' }}</p>
                                        <p><strong>{{ __('District:') }}</strong> {{ $snapshot->district ?? '—' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>{{ __('Address Line 1:') }}</strong> {{ $snapshot->address_line1 ?? '—' }}</p>
                                        <p><strong>{{ __('Address Line 2:') }}</strong> {{ $snapshot->address_line2 ?? '—' }}</p>
                                        <p><strong>{{ __('Postal Code:') }}</strong> {{ $snapshot->postal_code ?? '—' }}</p>
                                        <p><strong>{{ __('Country:') }}</strong> {{ $snapshot->country ?? '—' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted mb-0">{{ __('No delivery address snapshot available.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Add Tracking Update') }}</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dashboard.shipments.tracking.store', $shipment) }}">
                                @csrf

                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="">{{ __('Select status') }}</option>
                                        @foreach(\App\Models\Shipment::statuses() as $status)
                                            <option value="{{ $status }}" @selected(old('status') === $status)>
                                                {{ status_label($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Location') }}</label>
                                    <input
                                        type="text"
                                        name="location"
                                        value="{{ old('location') }}"
                                        class="form-control @error('location') is-invalid @enderror"
                                        placeholder="{{ __('Current location') }}"
                                    >
                                    @error('location')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Carrier Status') }}</label>
                                    <input
                                        type="text"
                                        name="carrier_status"
                                        value="{{ old('carrier_status') }}"
                                        class="form-control @error('carrier_status') is-invalid @enderror"
                                        placeholder="{{ __('Carrier status') }}"
                                    >
                                    @error('carrier_status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Note') }}</label>
                                    <textarea
                                        name="note"
                                        class="form-control @error('note') is-invalid @enderror"
                                        rows="4"
                                        placeholder="{{ __('Write note...') }}"
                                    >{{ old('note') }}</textarea>
                                    @error('note')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> {{ __('Add Tracking') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card card-outline card-dark">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Tracking Timeline') }}</h3>
                        </div>

                        <div class="card-body">
                            @forelse($shipment->trackingLogs as $log)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>
                                                {{ status_label($log->status ?? 'update') }}
                                            </strong>

                                            @if($log->location || $log->carrier_status)
                                                <div class="text-muted mt-1">
                                                    {{ $log->location ?: __('Unknown location') }}
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
                                <p class="text-muted mb-0">{{ __('No tracking logs.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Add Delivery Attempt') }}</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dashboard.shipments.delivery-attempts.store', $shipment) }}">
                                @csrf

                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        @foreach(\App\Models\DeliveryAttempt::statuses() as $status)
                                            <option value="{{ $status }}" @selected(old('status') === $status)>
                                                {{ status_label($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Driver Name') }}</label>
                                            <input type="text" name="driver_name" value="{{ old('driver_name') }}" class="form-control" placeholder="{{ __('Driver name') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Driver Phone') }}</label>
                                            <input type="text" name="driver_phone" value="{{ old('driver_phone') }}" class="form-control" placeholder="{{ __('Driver phone') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Location') }}</label>
                                    <input type="text" name="location" value="{{ old('location') }}" class="form-control" placeholder="{{ __('Current location') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Failure Reason') }}</label>
                                    <input type="text" name="failure_reason" value="{{ old('failure_reason') }}" class="form-control" placeholder="{{ __('Reason if failed') }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Attempted At') }}</label>
                                            <input type="datetime-local" name="attempted_at" value="{{ old('attempted_at') }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Next Attempt At') }}</label>
                                            <input type="datetime-local" name="next_attempt_at" value="{{ old('next_attempt_at') }}" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Notes') }}</label>
                                    <textarea name="notes" rows="3" class="form-control" placeholder="{{ __('Write attempt notes...') }}">{{ old('notes') }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> {{ __('Add Attempt') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Delivery Attempts Timeline') }}</h3>
                        </div>
                        <div class="card-body">
                            @forelse($shipment->deliveryAttempts as $attempt)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div>
                                                <strong>Attempt #{{ $attempt->attempt_no }}</strong>
                                                <span class="{{ $attempt->status_badge_class }} ml-2">
                                                    {{ status_label($attempt->status) }}
                                                </span>
                                            </div>

                                            <div class="text-muted mt-2">
                                                <div><strong>{{ __('Driver:') }}</strong> {{ $attempt->driver_name ?: '—' }}</div>
                                                <div><strong>{{ __('Phone:') }}</strong> {{ $attempt->driver_phone ?: '—' }}</div>
                                                <div><strong>{{ __('Location:') }}</strong> {{ $attempt->location ?: '—' }}</div>
                                                <div><strong>{{ __('Failure Reason:') }}</strong> {{ $attempt->failure_reason ?: '—' }}</div>
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
                                <p class="text-muted mb-0">{{ __('No delivery attempts yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
