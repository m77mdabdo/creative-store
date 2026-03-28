@csrf

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $formTitle ?? 'Page Form' }}</h3>
            </div>

            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="slug">Slug <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        class="form-control @error('slug') is-invalid @enderror"
                        value="{{ old('slug', $page->slug ?? '') }}"
                        maxlength="255"
                        required
                    >
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>
                <h5 class="mb-3">Arabic Content</h5>

                <div class="form-group mb-3">
                    <label for="title_ar">Title (AR) <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="title_ar"
                        id="title_ar"
                        class="form-control @error('title_ar') is-invalid @enderror"
                        value="{{ old('title_ar', $translationAr->title ?? '') }}"
                        maxlength="255"
                        required
                    >
                    @error('title_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="content_ar">Content (AR) <span class="text-danger">*</span></label>
                    <textarea
                        name="content_ar"
                        id="content_ar"
                        rows="8"
                        class="form-control @error('content_ar') is-invalid @enderror"
                        required
                    >{{ old('content_ar', $translationAr->content ?? '') }}</textarea>
                    @error('content_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>
                <h5 class="mb-3">English Content</h5>

                <div class="form-group mb-3">
                    <label for="title_en">Title (EN) <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="title_en"
                        id="title_en"
                        class="form-control @error('title_en') is-invalid @enderror"
                        value="{{ old('title_en', $translationEn->title ?? '') }}"
                        maxlength="255"
                        required
                    >
                    @error('title_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="content_en">Content (EN) <span class="text-danger">*</span></label>
                    <textarea
                        name="content_en"
                        id="content_en"
                        rows="8"
                        class="form-control @error('content_en') is-invalid @enderror"
                        required
                    >{{ old('content_en', $translationEn->content ?? '') }}</textarea>
                    @error('content_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="custom-control custom-switch">
                    <input
                        type="checkbox"
                        class="custom-control-input"
                        id="is_active"
                        name="is_active"
                        value="1"
                        @checked(old('is_active', $page->is_active ?? true))
                    >
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('dashboard.pages.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save' }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleEnInput = document.getElementById('title_en');
    const slugInput = document.getElementById('slug');

    function slugify(value) {
        return String(value || '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }

    if (!titleEnInput || !slugInput) return;

    let slugTouched = slugInput.value.trim() !== '';

    slugInput.addEventListener('input', function () {
        slugTouched = this.value.trim() !== '';
        this.value = slugify(this.value);
    });

    titleEnInput.addEventListener('input', function () {
        if (!slugTouched) {
            slugInput.value = slugify(this.value);
        }
    });
});
</script>
@endpush
