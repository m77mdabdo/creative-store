@php
    $category = $category ?? null;

    $arName = old('translations.ar.name', optional($category?->translations->firstWhere('locale', 'ar'))->name);
    $arDescription = old('translations.ar.description', optional($category?->translations->firstWhere('locale', 'ar'))->description);

    $enName = old('translations.en.name', optional($category?->translations->firstWhere('locale', 'en'))->name);
    $enDescription = old('translations.en.description', optional($category?->translations->firstWhere('locale', 'en'))->description);
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Basic Information</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Arabic Name <span class="text-danger">*</span></label>
                    <input type="text" name="translations[ar][name]" class="form-control"
                           value="{{ $arName }}" required>
                    @error('translations.ar.name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Arabic Description</label>
                    <textarea name="translations[ar][description]" rows="4" class="form-control">{{ $arDescription }}</textarea>
                </div>

                <hr>

                <div class="form-group">
                    <label>English Name</label>
                    <input type="text" name="translations[en][name]" class="form-control"
                           value="{{ $enName }}">
                    @error('translations.en.name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label>English Description</label>
                    <textarea name="translations[en][description]" rows="4" class="form-control">{{ $enDescription }}</textarea>
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">SEO</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Meta Title</label>
                    <input type="text" name="meta_title" class="form-control"
                           value="{{ old('meta_title', $category->meta_title ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Meta Description</label>
                    <textarea name="meta_description" rows="3" class="form-control">{{ old('meta_description', $category->meta_description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Category Settings</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" class="form-control"
                           value="{{ old('slug', $category->slug ?? '') }}" required>
                    @error('slug')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Parent Category</label>
                    <select name="parent_id" class="form-control">
                        <option value="">Root Category</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}"
                                {{ (string) old('parent_id', $category->parent_id ?? '') === (string) $parent->id ? 'selected' : '' }}>
                                {{ $parent->name ?? 'No Name' }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" min="0" name="sort_order" class="form-control"
                           value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="is_active"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>
