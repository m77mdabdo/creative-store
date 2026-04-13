@csrf

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $formTitle ?? 'Blog Form' }}</h3>
            </div>

            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="blog_category_id">Category <span class="text-danger">*</span></label>
                    <select name="blog_category_id" id="blog_category_id" class="form-control @error('blog_category_id') is-invalid @enderror" required>
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('blog_category_id', $blog->blog_category_id ?? '') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('blog_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $blog->title ?? '') }}" maxlength="255" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="slug">Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug', $blog->slug ?? '') }}" maxlength="255" required>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="author">Author</label>
                    <input type="text" name="author" id="author" class="form-control @error('author') is-invalid @enderror"
                           value="{{ old('author', $blog->author ?? auth()->user()->name ?? 'Admin') }}" maxlength="120">
                    @error('author')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="published_at">Published At</label>
                    <input type="datetime-local" name="published_at" id="published_at"
                           class="form-control @error('published_at') is-invalid @enderror"
                           value="{{ old('published_at', isset($blog->published_at) && $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}">
                    @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="excerpt">Excerpt</label>
                    <textarea name="excerpt" id="excerpt" rows="4" maxlength="1000"
                              class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
                    @error('excerpt')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="content">Content <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="10"
                              class="form-control @error('content') is-invalid @enderror"
                              required>{{ old('content', $blog->content ?? '') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image"
                           class="form-control @error('image') is-invalid @enderror"
                           accept=".jpg,.jpeg,.png,.webp">
                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    @if(!empty($blog?->image))
                        <div class="mt-3">
                           @php
    $img = $blog->image
        ? Storage::url($blog->image)
        : asset('cozastore/images/product-01.jpg');
@endphp

<img src="{{ $img }}"
     alt="{{ $blog->title }}"
     style="width: 60px; height: 45px; object-fit: cover; border-radius: 8px;">
                        </div>
                    @endif
                </div>

                <div class="form-group mb-0">
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" value="1"
                               @checked(old('is_featured', $blog->is_featured ?? false))>
                        <label class="custom-control-label" for="is_featured">Featured</label>
                    </div>

                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1"
                               @checked(old('status', $blog->status ?? true))>
                        <label class="custom-control-label" for="status">Published / Active</label>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('dashboard.blogs.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save' }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('title');
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

    if (!titleInput || !slugInput) return;

    let slugTouched = slugInput.value.trim() !== '';

    slugInput.addEventListener('input', function () {
        slugTouched = this.value.trim() !== '';
        this.value = slugify(this.value);
    });

    titleInput.addEventListener('input', function () {
        if (!slugTouched) {
            slugInput.value = slugify(this.value);
        }
    });
});
</script>
@endpush
