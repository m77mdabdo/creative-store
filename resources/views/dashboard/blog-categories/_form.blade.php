@csrf

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $formTitle ?? 'Category Form' }}</h3>
            </div>

            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $category->name ?? '') }}"
                        maxlength="150"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="slug">Slug <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        class="form-control @error('slug') is-invalid @enderror"
                        value="{{ old('slug', $category->slug ?? '') }}"
                        maxlength="180"
                        required
                    >
                    <small class="form-text text-muted">
                        Lowercase letters, numbers, and hyphens only.
                    </small>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('dashboard.blog-categories.index') }}" class="btn btn-secondary">
                    Cancel
                </a>

                <button type="submit" class="btn btn-primary">
                    {{ $submitLabel ?? 'Save' }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nameInput = document.getElementById('name');
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

        if (!nameInput || !slugInput) return;

        let slugTouched = slugInput.value.trim() !== '';

        slugInput.addEventListener('input', function () {
            slugTouched = this.value.trim() !== '';
            this.value = slugify(this.value);
        });

        nameInput.addEventListener('input', function () {
            if (!slugTouched) {
                slugInput.value = slugify(this.value);
            }
        });
    });
</script>
@endpush
