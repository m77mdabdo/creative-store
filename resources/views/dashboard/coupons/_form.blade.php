<div class="row">
    <div class="col-md-6 form-group">
        <label>Coupon Code</label>
        <input type="text" name="code" class="form-control" value="{{ $form['code'] }}" required>
    </div>

    <div class="col-md-6 form-group">
        <label>Coupon Scope</label>
        <select name="product_id" class="form-control">
            <option value="">Cart Coupon (Applies to whole cart)</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" {{ (string) $form['product_id'] === (string) $product->id ? 'selected' : '' }}>
                    {{ $product->name ?? ('Product #' . $product->id) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label>Type</label>
        <select name="type" class="form-control" required>
            <option value="fixed" {{ $form['type'] === 'fixed' ? 'selected' : '' }}>Fixed</option>
            <option value="percent" {{ $form['type'] === 'percent' ? 'selected' : '' }}>Percent</option>
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label>Value</label>
        <input type="number" step="0.01" min="0.01" name="value" class="form-control" value="{{ $form['value'] }}" required>
    </div>

    <div class="col-md-4 form-group">
        <label>Status</label>
        <div class="form-check mt-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                {{ $form['is_active'] ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">Active</label>
        </div>
    </div>

    <div class="col-md-3 form-group">
        <label>Min Order Amount</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" class="form-control" value="{{ $form['min_order_amount'] }}">
    </div>

    <div class="col-md-3 form-group">
        <label>Max Discount Amount</label>
        <input type="number" step="0.01" min="0" name="max_discount_amount" class="form-control" value="{{ $form['max_discount_amount'] }}">
    </div>

    <div class="col-md-3 form-group">
        <label>Usage Limit</label>
        <input type="number" min="1" name="usage_limit" class="form-control" value="{{ $form['usage_limit'] }}">
    </div>

    <div class="col-md-3 form-group">
        <label>Per User Limit</label>
        <input type="number" min="1" name="per_user_limit" class="form-control" value="{{ $form['per_user_limit'] }}">
    </div>

    <div class="col-md-6 form-group">
        <label>Starts At</label>
        <input type="datetime-local" name="starts_at" class="form-control" value="{{ $form['starts_at'] }}">
    </div>

    <div class="col-md-6 form-group">
        <label>Expires At</label>
        <input type="datetime-local" name="expires_at" class="form-control" value="{{ $form['expires_at'] }}">
    </div>
</div> 
