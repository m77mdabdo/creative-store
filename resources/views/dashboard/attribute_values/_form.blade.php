<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ __('Value Information') }}</h3>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label>{{ __('Attribute') }}</label>
                    <input type="text" class="form-control" value="{{ $attribute->name }}" disabled>
                </div>

                <div class="form-group">
                    <label>{{ __('Value') }} <span class="text-danger">*</span></label>
                    <input type="text" name="value" class="form-control" value="{{ $form['value'] }}" required>
                    @error('value') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>{{ __('Slug') }}</label>
                    <input type="text" name="slug" class="form-control" value="{{ $form['slug'] }}" placeholder="{{ __('Leave empty to auto-generate') }}">
                    @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>{{ __('Sort Order') }}</label>
                    <input type="number" min="0" name="sort_order" class="form-control" value="{{ $form['sort_order'] }}">
                    @error('sort_order') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group mb-0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ $form['is_active'] ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-1"></i> {{ __('Save') }}
                </button>
            </div>
        </div>
    </div>
</div>

