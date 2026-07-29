<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ __('dashboard.categories') }} - {{ __('dashboard.name') }} & {{ __('dashboard.description') }}</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">العربية</h3>
                            </div>

                            <div class="card-body">
                                <div class="form-group">
                                    <label>الاسم العربي <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        name="ar[name]"
                                        class="form-control @error('ar.name') is-invalid @enderror"
                                        value="{{ $form['ar']['name'] }}"
                                        required
                                    >
                                    @error('ar.name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group mb-0">
                                    <label>الوصف العربي</label>
                                    <textarea
                                        name="ar[description]"
                                        rows="5"
                                        class="form-control @error('ar.description') is-invalid @enderror"
                                    >{{ $form['ar']['description'] }}</textarea>
                                    @error('ar.description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('English') }}</h3>
                            </div>

                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('English Name') }} <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        name="en[name]"
                                        class="form-control @error('en.name') is-invalid @enderror"
                                        value="{{ $form['en']['name'] }}"
                                        required
                                    >
                                    @error('en.name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group mb-0">
                                    <label>{{ __('English Description') }}</label>
                                    <textarea
                                        name="en[description]"
                                        rows="5"
                                        class="form-control @error('en.description') is-invalid @enderror"
                                    >{{ $form['en']['description'] }}</textarea>
                                    @error('en.description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">{{ __('SEO') }}</h3>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label>{{ __('Meta Title') }}</label>
                    <input
                        type="text"
                        name="meta_title"
                        class="form-control @error('meta_title') is-invalid @enderror"
                        value="{{ $form['meta_title'] }}"
                    >
                    @error('meta_title')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group mb-0">
                    <label>{{ __('Meta Description') }}</label>
                    <textarea
                        name="meta_description"
                        rows="3"
                        class="form-control @error('meta_description') is-invalid @enderror"
                    >{{ $form['meta_description'] }}</textarea>
                    @error('meta_description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">{{ __('Category Settings') }}</h3>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label>{{ __('Slug') }}</label>
                    <input
                        type="text"
                        name="slug"
                        class="form-control @error('slug') is-invalid @enderror"
                        value="{{ $form['slug'] }}"
                        placeholder="{{ __('Leave empty to auto-generate') }}"
                    >
                    @error('slug')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ __('Parent Category') }}</label>
                    <select name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                        <option value="">{{ __('Root Category') }}</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ (string) $form['parent_id'] === (string) $parent->id ? 'selected' : '' }}>
                                {{ $parent->name ?? __('No Name') }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ __('Sort Order') }}</label>
                    <input
                        type="number"
                        min="0"
                        name="sort_order"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ $form['sort_order'] }}"
                    >
                    @error('sort_order')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group mb-0">
                    <div class="custom-control custom-switch">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="is_active"
                            name="is_active"
                            value="1"
                            {{ $form['is_active'] ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="is_active">{{ __('Active') }}</label>
                    </div>
                    @error('is_active')
                        <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-save mr-1"></i> {{ __('dashboard.save') }}
                </button>
            </div>
        </div>
    </div>
</div>
