@csrf

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Shipping Zone</label>
            <select name="shipping_zone_id" class="form-control @error('shipping_zone_id') is-invalid @enderror">
                <option value="">Select Zone</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}" @selected(old('shipping_zone_id', $shippingRate->shipping_zone_id ?? '') == $zone->id)>
                        {{ $zone->name }} ({{ $zone->country }})
                    </option>
                @endforeach
            </select>
            @error('shipping_zone_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Shipping Method</label>
            <select name="shipping_method_id" class="form-control @error('shipping_method_id') is-invalid @enderror">
                <option value="">Select Method</option>
                @foreach($methods as $method)
                    <option value="{{ $method->id }}" @selected(old('shipping_method_id', $shippingRate->shipping_method_id ?? '') == $method->id)>
                        {{ $method->name }} ({{ $method->code }})
                    </option>
                @endforeach
            </select>
            @error('shipping_method_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" value="{{ old('city', $shippingRate->city ?? '') }}" class="form-control @error('city') is-invalid @enderror">
            @error('city') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>District</label>
            <input type="text" name="district" value="{{ old('district', $shippingRate->district ?? '') }}" class="form-control @error('district') is-invalid @enderror">
            @error('district') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Min Weight</label>
            <input type="number" step="0.01" name="min_weight" value="{{ old('min_weight', $shippingRate->min_weight ?? 0) }}" class="form-control @error('min_weight') is-invalid @enderror">
            @error('min_weight') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>Max Weight</label>
            <input type="number" step="0.01" name="max_weight" value="{{ old('max_weight', $shippingRate->max_weight ?? 0) }}" class="form-control @error('max_weight') is-invalid @enderror">
            @error('max_weight') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>Price</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $shippingRate->price ?? 0) }}" class="form-control @error('price') is-invalid @enderror">
            @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>Priority</label>
            <input type="number" name="priority" value="{{ old('priority', $shippingRate->priority ?? 0) }}" class="form-control @error('priority') is-invalid @enderror">
            @error('priority') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>ETA Min Days</label>
            <input type="number" name="eta_min_days" value="{{ old('eta_min_days', $shippingRate->eta_min_days ?? 0) }}" class="form-control @error('eta_min_days') is-invalid @enderror">
            @error('eta_min_days') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>ETA Max Days</label>
            <input type="number" name="eta_max_days" value="{{ old('eta_max_days', $shippingRate->eta_max_days ?? 0) }}" class="form-control @error('eta_max_days') is-invalid @enderror">
            @error('eta_max_days') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>Free Shipping Threshold</label>
            <input type="number" step="0.01" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $shippingRate->free_shipping_threshold ?? 0) }}" class="form-control @error('free_shipping_threshold') is-invalid @enderror">
            @error('free_shipping_threshold') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>COD Fee</label>
            <input type="number" step="0.01" name="cash_on_delivery_fee" value="{{ old('cash_on_delivery_fee', $shippingRate->cash_on_delivery_fee ?? 0) }}" class="form-control @error('cash_on_delivery_fee') is-invalid @enderror">
            @error('cash_on_delivery_fee') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="form-group clearfix">
    <div class="icheck-primary d-inline">
        <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $shippingRate->is_active ?? true))>
        <label for="is_active">Active</label>
    </div>
</div>

<div class="card-footer px-0 pb-0">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save mr-1"></i> Save
    </button>

    <a href="{{ route('dashboard.shipping-rates.index') }}" class="btn btn-outline-secondary">
        Cancel
    </a>
</div>
