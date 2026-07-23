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
                        value="{{ $form['translations']['ar']['name'] }}" required>
                    @error('translations.ar.name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Arabic Description</label>
                    <textarea name="translations[ar][description]" rows="4"
                        class="form-control">{{ $form['translations']['ar']['description'] }}</textarea>
                </div>

                <hr>

                <div class="form-group">
                    <label>English Name</label>
                    <input type="text" name="translations[en][name]" class="form-control"
                        value="{{ $form['translations']['en']['name'] }}">
                    @error('translations.en.name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>English Description</label>
                    <textarea name="translations[en][description]" rows="4"
                        class="form-control">{{ $form['translations']['en']['description'] }}</textarea>
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
                    <input type="text" name="meta_title" class="form-control" value="{{ $form['meta_title'] }}">
                </div>

                <div class="form-group">
                    <label>Meta Description</label>
                    <textarea name="meta_description" rows="3"
                        class="form-control">{{ $form['meta_description'] }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Product Settings</h3>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label>Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) $form['category_id'] === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name ?? 'No Name' }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ $form['slug'] }}"
                        placeholder="Leave empty to auto-generate">
                    @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>SKU <span class="text-danger">*</span></label>
                    <input type="text" name="sku" class="form-control" value="{{ $form['sku'] }}" required>
                    @error('sku') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Base Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="base_price" class="form-control"
                        value="{{ $form['base_price'] }}" required>
                    @error('base_price') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Currency</label>
                    <input type="text" name="currency" class="form-control" value="{{ $form['currency'] }}">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ $form['status'] === 'active' ? 'selected' : '' }}>Active</option>
                        
                        <option value="draft" {{ $form['status'] === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ $form['status'] === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tax Rate</label>
                    <input type="number" step="0.01" min="0" name="tax_rate" class="form-control"
                        value="{{ $form['tax_rate'] }}">
                </div>

                <div class="form-group">
                    <label>Weight</label>
                    <input type="number" step="0.01" min="0" name="weight" class="form-control"
                        value="{{ $form['weight'] }}">
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="has_variants" name="has_variants"
                            value="1" {{ $form['has_variants'] ? 'checked' : '' }}>
                        <label class="custom-control-label" for="has_variants">Has Variants</label>
                    </div>
                    <div class="form-group" id="product-attributes-wrapper"
                        style="{{ $form['has_variants'] ? '' : 'display:none;' }}">
                        <label>Variant Attributes</label>

                        <div class="border rounded p-2">
                            @forelse($attributes as $attribute)
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input product-attribute-checkbox"
                                        id="attribute_{{ $attribute->id }}" name="attribute_ids[]"
                                        value="{{ $attribute->id }}" {{ in_array((string) $attribute->id, array_map('strval', $form['attribute_ids'] ?? []), true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="attribute_{{ $attribute->id }}">
                                        <strong>{{ $attribute->name }}</strong>

                                        @if($attribute->activeValues->count())
                                            <small class="text-muted d-block ml-4">
                                                {{ $attribute->activeValues->pluck('value')->implode(' / ') }}
                                            </small>
                                        @endif
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No active attributes found.</p>
                            @endforelse
                        </div>

                        @error('attribute_ids') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        @error('attribute_ids.*') <small class="text-danger d-block">{{ $message }}</small> @enderror

                        <small class="text-muted d-block mt-2">
                            Select attributes like Color, Size, Material. These will be used to generate product
                            variants.
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured"
                            value="1" {{ $form['is_featured'] ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_featured">Featured</label>
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


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hasVariants = document.getElementById('has_variants');
    const wrapper = document.getElementById('product-attributes-wrapper');

    function toggleAttributesBox() {
        if (!hasVariants || !wrapper) return;
        wrapper.style.display = hasVariants.checked ? '' : 'none';
    }

    if (hasVariants) {
        hasVariants.addEventListener('change', toggleAttributesBox);
        toggleAttributesBox();
    }
});
</script>
@endpush
