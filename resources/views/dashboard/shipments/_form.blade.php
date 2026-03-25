@csrf

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Order</label>
            <select name="order_id" class="form-control @error('order_id') is-invalid @enderror">
                <option value="">Select Order</option>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}" @selected(old('order_id', $shipment->order_id ?? '') == $order->id)>
                        {{ $order->order_number }} - {{ $order->user?->name ?? 'Guest' }}
                    </option>
                @endforeach
            </select>
            @error('order_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Shipping Method</label>
            <select name="shipping_method_id" class="form-control @error('shipping_method_id') is-invalid @enderror">
                <option value="">Select Method</option>
                @foreach($methods as $method)
                    <option value="{{ $method->id }}" @selected(old('shipping_method_id', $shipment->shipping_method_id ?? '') == $method->id)>
                        {{ $method->name }}
                    </option>
                @endforeach
            </select>
            @error('shipping_method_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Carrier</label>
            <input type="text" name="carrier" value="{{ old('carrier', $shipment->carrier ?? '') }}" class="form-control @error('carrier') is-invalid @enderror">
            @error('carrier') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Tracking Number</label>
            <input type="text" name="tracking_number" value="{{ old('tracking_number', $shipment->tracking_number ?? '') }}" class="form-control @error('tracking_number') is-invalid @enderror">
            @error('tracking_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control @error('status') is-invalid @enderror">
                @foreach(\App\Models\Shipment::statuses() as $status)
                    <option value="{{ $status }}" @selected(old('status', $shipment->status ?? 'pending') === $status)>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
            @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Current Location</label>
            <input type="text" name="current_location" value="{{ old('current_location', $shipment->current_location ?? '') }}" class="form-control @error('current_location') is-invalid @enderror">
            @error('current_location') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Carrier Status</label>
            <input type="text" name="carrier_status" value="{{ old('carrier_status', $shipment->carrier_status ?? '') }}" class="form-control @error('carrier_status') is-invalid @enderror">
            @error('carrier_status') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Shipped At</label>
            <input type="datetime-local" name="shipped_at" value="{{ old('shipped_at', isset($shipment->shipped_at) && $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('shipped_at') is-invalid @enderror">
            @error('shipped_at') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Delivered At</label>
            <input type="datetime-local" name="delivered_at" value="{{ old('delivered_at', isset($shipment->delivered_at) && $shipment->delivered_at ? $shipment->delivered_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('delivered_at') is-invalid @enderror">
            @error('delivered_at') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label>Notes</label>
    <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $shipment->notes ?? '') }}</textarea>
    @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>

<div class="card-footer px-0 pb-0">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save mr-1"></i> Save
    </button>

    <a href="{{ route('dashboard.shipments.index') }}" class="btn btn-outline-secondary">
        Cancel
    </a>
</div>
