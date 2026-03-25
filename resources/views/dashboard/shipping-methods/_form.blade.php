@csrf

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name', $shippingMethod->name ?? '') }}" class="form-control @error('name') is-invalid @enderror">
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Code</label>
            <input type="text" name="code" value="{{ old('code', $shippingMethod->code ?? '') }}" class="form-control @error('code') is-invalid @enderror">
            @error('code') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label>Description</label>
    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $shippingMethod->description ?? '') }}</textarea>
    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $shippingMethod->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror">
            @error('sort_order') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group clearfix" style="padding-top:32px;">
            <div class="icheck-primary d-inline">
                <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $shippingMethod->is_active ?? true))>
                <label for="is_active">Active</label>
            </div>
        </div>
    </div>
</div>

<div class="card-footer px-0 pb-0">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save mr-1"></i> Save
    </button>

    <a href="{{ route('dashboard.shipping-methods.index') }}" class="btn btn-outline-secondary">
        Cancel
    </a>
</div>
