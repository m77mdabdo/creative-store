<div class="row">
    <div class="col-md-6 form-group">
        <label>{{ __('Coupon Code') }}</label>
        <input type="text" name="code" class="form-control" value="{{ $form['code'] }}" required>
    </div>

    <div class="col-md-6 form-group">
        <label>{{ __('Coupon Scope') }}</label>
        <select name="product_id" class="form-control">
            <option value="">{{ __('Cart Coupon (Applies to whole cart)') }}</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" {{ (string) $form['product_id'] === (string) $product->id ? 'selected' : '' }}>
                    {{ $product->name ?? ('Product #' . $product->id) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label>{{ __('Type') }}</label>
        <select name="type" class="form-control" required>
            <option value="fixed" {{ $form['type'] === 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
            <option value="percent" {{ $form['type'] === 'percent' ? 'selected' : '' }}>{{ __('Percent') }}</option>
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label>{{ __('Value') }}</label>
        <input type="number" step="0.01" min="0.01" name="value" class="form-control" value="{{ $form['value'] }}" required>
    </div>

    <div class="col-md-4 form-group">
        <label>{{ __('Status') }}</label>
        <div class="form-check mt-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                {{ $form['is_active'] ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">{{ __('Active') }}</label>
        </div>
    </div>

    <div class="col-md-3 form-group">
        <label>{{ __('Min Order Amount') }}</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" class="form-control" value="{{ $form['min_order_amount'] }}">
    </div>

    <div class="col-md-3 form-group">
        <label>{{ __('Max Discount Amount') }}</label>
        <input type="number" step="0.01" min="0" name="max_discount_amount" class="form-control" value="{{ $form['max_discount_amount'] }}">
    </div>

    <div class="col-md-3 form-group">
        <label>{{ __('Usage Limit') }}</label>
        <input type="number" min="1" name="usage_limit" class="form-control" value="{{ $form['usage_limit'] }}">
    </div>

    <div class="col-md-3 form-group">
        <label>{{ __('Per User Limit') }}</label>
        <input type="number" min="1" name="per_user_limit" class="form-control" value="{{ $form['per_user_limit'] }}">
    </div>

    <div class="col-md-6 form-group">
        <label>{{ __('Starts At') }}</label>
        <input type="datetime-local" name="starts_at" class="form-control" value="{{ $form['starts_at'] }}">
    </div>

    <div class="col-md-6 form-group">
        <label>{{ __('Expires At') }}</label>
        <input type="datetime-local" name="expires_at" class="form-control" value="{{ $form['expires_at'] }}">
    </div>
</div> 
