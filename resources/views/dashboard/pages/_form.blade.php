@php
    $slugValue = old('slug', $page->slug ?? '');
    $isAboutPage = $slugValue === 'about';
    $isContactPage = $slugValue === 'contact';

    $titleAr = old('title_ar', $translationAr->title ?? '');
    $titleEn = old('title_en', $translationEn->title ?? '');

    $contentAr = old('content_ar', $translationAr->content ?? '');
    $contentEn = old('content_en', $translationEn->content ?? '');

    $aboutAr = $aboutAr ?? [];
    $aboutEn = $aboutEn ?? [];
    $contactAr = $contactAr ?? [];
    $contactEn = $contactEn ?? [];

    $arImage1 = old('image_1_ar', $aboutAr['image_1'] ?? 'images/about-01.jpg');
    $arImage2 = old('image_2_ar', $aboutAr['image_2'] ?? 'images/about-02.jpg');
    $enImage1 = old('image_1_en', $aboutEn['image_1'] ?? 'images/about-01.jpg');
    $enImage2 = old('image_2_en', $aboutEn['image_2'] ?? 'images/about-02.jpg');

    $arImage1Url = $arImage1 ? (str_starts_with($arImage1, 'images/') ? asset($arImage1) : asset('storage/' . ltrim($arImage1, '/'))) : null;
    $arImage2Url = $arImage2 ? (str_starts_with($arImage2, 'images/') ? asset($arImage2) : asset('storage/' . ltrim($arImage2, '/'))) : null;
    $enImage1Url = $enImage1 ? (str_starts_with($enImage1, 'images/') ? asset($enImage1) : asset('storage/' . ltrim($enImage1, '/'))) : null;
    $enImage2Url = $enImage2 ? (str_starts_with($enImage2, 'images/') ? asset($enImage2) : asset('storage/' . ltrim($enImage2, '/'))) : null;
@endphp

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $formTitle ?? 'Page Form' }}</h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        class="form-control @error('slug') is-invalid @enderror"
                        value="{{ $slugValue }}"
                        placeholder="about / contact"
                    >
                    @error('slug')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="text-muted">Use "about" for About page and "contact" for Contact page.</small>
                </div>
            </div>

            <div class="col-md-4 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="is_active"
                        class="form-check-input"
                        value="1"
                        {{ old('is_active', $page->is_active ?? true) ? 'checked' : '' }}
                    >
                    <label for="is_active" class="form-check-label">Active</label>
                </div>
            </div>
        </div>

        <hr>

        <div id="about-fields" style="{{ $isAboutPage ? '' : 'display:none;' }}">
            <div class="alert alert-info">These fields are dedicated to the About page.</div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Arabic Content</h5>

                    <div class="form-group">
                        <label>Page Title (AR)</label>
                        <input type="text" name="title_ar" class="form-control" value="{{ $titleAr }}">
                    </div>

                    <div class="form-group">
                        <label>Hero Title (AR)</label>
                        <input type="text" name="hero_title_ar" class="form-control" value="{{ old('hero_title_ar', $aboutAr['hero_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Story Title (AR)</label>
                        <input type="text" name="story_title_ar" class="form-control" value="{{ old('story_title_ar', $aboutAr['story_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Story Text 1 (AR)</label>
                        <textarea name="story_text_1_ar" rows="4" class="form-control">{{ old('story_text_1_ar', $aboutAr['story_text_1'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Story Text 2 (AR)</label>
                        <textarea name="story_text_2_ar" rows="4" class="form-control">{{ old('story_text_2_ar', $aboutAr['story_text_2'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Story Text 3 (AR)</label>
                        <textarea name="story_text_3_ar" rows="4" class="form-control">{{ old('story_text_3_ar', $aboutAr['story_text_3'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Mission Title (AR)</label>
                        <input type="text" name="mission_title_ar" class="form-control" value="{{ old('mission_title_ar', $aboutAr['mission_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Mission Text (AR)</label>
                        <textarea name="mission_text_ar" rows="5" class="form-control">{{ old('mission_text_ar', $aboutAr['mission_text'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Quote Text (AR)</label>
                        <textarea name="quote_text_ar" rows="3" class="form-control">{{ old('quote_text_ar', $aboutAr['quote_text'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Quote Author (AR)</label>
                        <input type="text" name="quote_author_ar" class="form-control" value="{{ old('quote_author_ar', $aboutAr['quote_author'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Image 1 (AR)</label>
                        <input type="file" name="image_1_ar_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <input type="hidden" name="image_1_ar" value="{{ $arImage1 }}">
                        @if($arImage1Url)
                            <div class="mt-2">
                                <img src="{{ $arImage1Url }}" alt="Arabic Image 1" style="max-width: 140px; border-radius: 8px;">
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Image 2 (AR)</label>
                        <input type="file" name="image_2_ar_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <input type="hidden" name="image_2_ar" value="{{ $arImage2 }}">
                        @if($arImage2Url)
                            <div class="mt-2">
                                <img src="{{ $arImage2Url }}" alt="Arabic Image 2" style="max-width: 140px; border-radius: 8px;">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <h5>English Content</h5>

                    <div class="form-group">
                        <label>Page Title (EN)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ $titleEn }}">
                    </div>

                    <div class="form-group">
                        <label>Hero Title (EN)</label>
                        <input type="text" name="hero_title_en" class="form-control" value="{{ old('hero_title_en', $aboutEn['hero_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Story Title (EN)</label>
                        <input type="text" name="story_title_en" class="form-control" value="{{ old('story_title_en', $aboutEn['story_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Story Text 1 (EN)</label>
                        <textarea name="story_text_1_en" rows="4" class="form-control">{{ old('story_text_1_en', $aboutEn['story_text_1'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Story Text 2 (EN)</label>
                        <textarea name="story_text_2_en" rows="4" class="form-control">{{ old('story_text_2_en', $aboutEn['story_text_2'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Story Text 3 (EN)</label>
                        <textarea name="story_text_3_en" rows="4" class="form-control">{{ old('story_text_3_en', $aboutEn['story_text_3'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Mission Title (EN)</label>
                        <input type="text" name="mission_title_en" class="form-control" value="{{ old('mission_title_en', $aboutEn['mission_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Mission Text (EN)</label>
                        <textarea name="mission_text_en" rows="5" class="form-control">{{ old('mission_text_en', $aboutEn['mission_text'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Quote Text (EN)</label>
                        <textarea name="quote_text_en" rows="3" class="form-control">{{ old('quote_text_en', $aboutEn['quote_text'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Quote Author (EN)</label>
                        <input type="text" name="quote_author_en" class="form-control" value="{{ old('quote_author_en', $aboutEn['quote_author'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Image 1 (EN)</label>
                        <input type="file" name="image_1_en_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <input type="hidden" name="image_1_en" value="{{ $enImage1 }}">
                        @if($enImage1Url)
                            <div class="mt-2">
                                <img src="{{ $enImage1Url }}" alt="English Image 1" style="max-width: 140px; border-radius: 8px;">
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Image 2 (EN)</label>
                        <input type="file" name="image_2_en_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <input type="hidden" name="image_2_en" value="{{ $enImage2 }}">
                        @if($enImage2Url)
                            <div class="mt-2">
                                <img src="{{ $enImage2Url }}" alt="English Image 2" style="max-width: 140px; border-radius: 8px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div id="contact-fields" style="{{ $isContactPage ? '' : 'display:none;' }}">
            <div class="alert alert-info">These fields are dedicated to the Contact page.</div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Arabic Content</h5>

                    <div class="form-group">
                        <label>Page Title (AR)</label>
                        <input type="text" name="title_ar" class="form-control" value="{{ $titleAr }}">
                    </div>

                    <div class="form-group">
                        <label>Hero Title (AR)</label>
                        <input type="text" name="hero_title_ar" class="form-control" value="{{ old('hero_title_ar', $contactAr['hero_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Form Title (AR)</label>
                        <input type="text" name="form_title_ar" class="form-control" value="{{ old('form_title_ar', $contactAr['form_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Address Title (AR)</label>
                        <input type="text" name="address_title_ar" class="form-control" value="{{ old('address_title_ar', $contactAr['address_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Address Text (AR)</label>
                        <textarea name="address_text_ar" rows="3" class="form-control">{{ old('address_text_ar', $contactAr['address_text'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Phone Title (AR)</label>
                        <input type="text" name="phone_title_ar" class="form-control" value="{{ old('phone_title_ar', $contactAr['phone_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Phone Text (AR)</label>
                        <input type="text" name="phone_text_ar" class="form-control" value="{{ old('phone_text_ar', $contactAr['phone_text'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Email Title (AR)</label>
                        <input type="text" name="email_title_ar" class="form-control" value="{{ old('email_title_ar', $contactAr['email_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Email Text (AR)</label>
                        <input type="text" name="email_text_ar" class="form-control" value="{{ old('email_text_ar', $contactAr['email_text'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Map X (AR)</label>
                        <input type="text" name="map_x_ar" class="form-control" value="{{ old('map_x_ar', $contactAr['map_x'] ?? '24.7136') }}">
                    </div>

                    <div class="form-group">
                        <label>Map Y (AR)</label>
                        <input type="text" name="map_y_ar" class="form-control" value="{{ old('map_y_ar', $contactAr['map_y'] ?? '46.6753') }}">
                    </div>

                    <div class="form-group">
                        <label>Map Zoom (AR)</label>
                        <input type="number" name="map_zoom_ar" class="form-control" value="{{ old('map_zoom_ar', $contactAr['map_zoom'] ?? '11') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <h5>English Content</h5>

                    <div class="form-group">
                        <label>Page Title (EN)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ $titleEn }}">
                    </div>

                    <div class="form-group">
                        <label>Hero Title (EN)</label>
                        <input type="text" name="hero_title_en" class="form-control" value="{{ old('hero_title_en', $contactEn['hero_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Form Title (EN)</label>
                        <input type="text" name="form_title_en" class="form-control" value="{{ old('form_title_en', $contactEn['form_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Address Title (EN)</label>
                        <input type="text" name="address_title_en" class="form-control" value="{{ old('address_title_en', $contactEn['address_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Address Text (EN)</label>
                        <textarea name="address_text_en" rows="3" class="form-control">{{ old('address_text_en', $contactEn['address_text'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Phone Title (EN)</label>
                        <input type="text" name="phone_title_en" class="form-control" value="{{ old('phone_title_en', $contactEn['phone_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Phone Text (EN)</label>
                        <input type="text" name="phone_text_en" class="form-control" value="{{ old('phone_text_en', $contactEn['phone_text'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Email Title (EN)</label>
                        <input type="text" name="email_title_en" class="form-control" value="{{ old('email_title_en', $contactEn['email_title'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Email Text (EN)</label>
                        <input type="text" name="email_text_en" class="form-control" value="{{ old('email_text_en', $contactEn['email_text'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Map X (EN)</label>
                        <input type="text" name="map_x_en" class="form-control" value="{{ old('map_x_en', $contactEn['map_x'] ?? '24.7136') }}">
                    </div>

                    <div class="form-group">
                        <label>Map Y (EN)</label>
                        <input type="text" name="map_y_en" class="form-control" value="{{ old('map_y_en', $contactEn['map_y'] ?? '46.6753') }}">
                    </div>

                    <div class="form-group">
                        <label>Map Zoom (EN)</label>
                        <input type="number" name="map_zoom_en" class="form-control" value="{{ old('map_zoom_en', $contactEn['map_zoom'] ?? '11') }}">
                    </div>
                </div>
            </div>
        </div>

        <div id="generic-fields" style="{{ ($isAboutPage || $isContactPage) ? 'display:none;' : '' }}">
            <div class="row">
                <div class="col-md-6">
                    <h5>Arabic</h5>

                    <div class="form-group">
                        <label>Title (AR)</label>
                        <input type="text" name="title_ar" class="form-control" value="{{ $titleAr }}">
                    </div>

                    <div class="form-group">
                        <label>Content (AR)</label>
                        <textarea name="content_ar" rows="10" class="form-control">{{ $contentAr }}</textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5>English</h5>

                    <div class="form-group">
                        <label>Title (EN)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ $titleEn }}">
                    </div>

                    <div class="form-group">
                        <label>Content (EN)</label>
                        <textarea name="content_en" rows="10" class="form-control">{{ $contentEn }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $submitLabel ?? 'Save' }}
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const slugInput = document.getElementById('slug');
    const aboutFields = document.getElementById('about-fields');
    const contactFields = document.getElementById('contact-fields');
    const genericFields = document.getElementById('generic-fields');

    function togglePageFields() {
        const slug = (slugInput?.value || '').trim().toLowerCase();
        const isAbout = slug === 'about';
        const isContact = slug === 'contact';

        if (aboutFields) {
            aboutFields.style.display = isAbout ? '' : 'none';
        }

        if (contactFields) {
            contactFields.style.display = isContact ? '' : 'none';
        }

        if (genericFields) {
            genericFields.style.display = (isAbout || isContact) ? 'none' : '';
        }
    }

    if (slugInput) {
        slugInput.addEventListener('input', togglePageFields);
        slugInput.addEventListener('change', togglePageFields);
    }

    togglePageFields();
});
</script>
@endpush
