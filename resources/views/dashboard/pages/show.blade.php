@extends('dashboard.app.layout')

@section('title', __('View Page'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('View Page') }}</h1>

            <div>
                <a href="{{ route('dashboard.pages.edit', $page) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>

                <a href="{{ route('dashboard.pages.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Page Details') }}</h3>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <strong>ID</strong>
                            <div>{{ $page->id }}</div>
                        </div>

                        <div class="col-md-3">
                            <strong>{{ __('Slug') }}</strong>
                            <div>{{ $page->slug }}</div>
                        </div>

                        <div class="col-md-3">
                            <strong>{{ __('Status') }}</strong>
                            <div>
                                @if($page->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <strong>{{ __('Updated At') }}</strong>
                            <div>{{ $page->updated_at?->translatedFormat('Y-m-d h:i A') }}</div>
                        </div>
                    </div>

                    <hr>

                    @php
                        $aboutAr = $aboutAr ?? [];
                        $aboutEn = $aboutEn ?? [];
                        $contactAr = $contactAr ?? [];
                        $contactEn = $contactEn ?? [];

                        $arImage1 = $aboutAr['image_1'] ?? null;
                        $arImage2 = $aboutAr['image_2'] ?? null;
                        $enImage1 = $aboutEn['image_1'] ?? null;
                        $enImage2 = $aboutEn['image_2'] ?? null;

                        $arImage1Url = $arImage1
                            ? (str_starts_with($arImage1, 'images/')
                                ? asset($arImage1)
                                : asset('storage/' . ltrim($arImage1, '/')))
                            : null;

                        $arImage2Url = $arImage2
                            ? (str_starts_with($arImage2, 'images/')
                                ? asset($arImage2)
                                : asset('storage/' . ltrim($arImage2, '/')))
                            : null;

                        $enImage1Url = $enImage1
                            ? (str_starts_with($enImage1, 'images/')
                                ? asset($enImage1)
                                : asset('storage/' . ltrim($enImage1, '/')))
                            : null;

                        $enImage2Url = $enImage2
                            ? (str_starts_with($enImage2, 'images/')
                                ? asset($enImage2)
                                : asset('storage/' . ltrim($enImage2, '/')))
                            : null;
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-3">{{ __('Arabic') }}</h4>

                            <p><strong>{{ __('Title:') }}</strong> {{ $translationAr?->title ?? '-' }}</p>

                            @if($page->slug === 'about')
                                <p><strong>{{ __('Hero Title:') }}</strong> {{ $aboutAr['hero_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Story Title:') }}</strong> {{ $aboutAr['story_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Story Text 1:') }}</strong><br>{{ $aboutAr['story_text_1'] ?? '-' }}</p>
                                <p><strong>{{ __('Story Text 2:') }}</strong><br>{{ $aboutAr['story_text_2'] ?? '-' }}</p>
                                <p><strong>{{ __('Story Text 3:') }}</strong><br>{{ $aboutAr['story_text_3'] ?? '-' }}</p>
                                <p><strong>{{ __('Mission Title:') }}</strong> {{ $aboutAr['mission_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Mission Text:') }}</strong><br>{{ $aboutAr['mission_text'] ?? '-' }}</p>
                                <p><strong>{{ __('Quote Text:') }}</strong><br>{{ $aboutAr['quote_text'] ?? '-' }}</p>
                                <p><strong>{{ __('Quote Author:') }}</strong> {{ $aboutAr['quote_author'] ?? '-' }}</p>

                                @if($arImage1Url)
                                    <div class="mb-3">
                                        <strong>{{ __('Image 1:') }}</strong><br>
                                        <img src="{{ $arImage1Url }}" alt="Arabic Image 1" style="max-width: 180px; border-radius: 8px;">
                                    </div>
                                @endif

                                @if($arImage2Url)
                                    <div class="mb-3">
                                        <strong>{{ __('Image 2:') }}</strong><br>
                                        <img src="{{ $arImage2Url }}" alt="Arabic Image 2" style="max-width: 180px; border-radius: 8px;">
                                    </div>
                                @endif

                            @elseif($page->slug === 'contact')
                                <p><strong>{{ __('Hero Title:') }}</strong> {{ $contactAr['hero_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Form Title:') }}</strong> {{ $contactAr['form_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Address Title:') }}</strong> {{ $contactAr['address_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Address Text:') }}</strong><br>{{ $contactAr['address_text'] ?? '-' }}</p>
                                <p><strong>{{ __('Phone Title:') }}</strong> {{ $contactAr['phone_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Phone Text:') }}</strong> {{ $contactAr['phone_text'] ?? '-' }}</p>
                                <p><strong>{{ __('Email Title:') }}</strong> {{ $contactAr['email_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Email Text:') }}</strong> {{ $contactAr['email_text'] ?? '-' }}</p>
                                <p><strong>{{ __('Map X:') }}</strong> {{ $contactAr['map_x'] ?? '-' }}</p>
                                <p><strong>{{ __('Map Y:') }}</strong> {{ $contactAr['map_y'] ?? '-' }}</p>
                                <p><strong>{{ __('Map Zoom:') }}</strong> {{ $contactAr['map_zoom'] ?? '-' }}</p>

                            @else
                                <p><strong>{{ __('Content:') }}</strong></p>
                                <div>{!! nl2br(e($translationAr?->content ?? '-')) !!}</div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h4 class="mb-3">{{ __('English') }}</h4>

                            <p><strong>{{ __('Title:') }}</strong> {{ $translationEn?->title ?? '-' }}</p>

                            @if($page->slug === 'about')
                                <p><strong>{{ __('Hero Title:') }}</strong> {{ $aboutEn['hero_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Story Title:') }}</strong> {{ $aboutEn['story_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Story Text 1:') }}</strong><br>{{ $aboutEn['story_text_1'] ?? '-' }}</p>
                                <p><strong>{{ __('Story Text 2:') }}</strong><br>{{ $aboutEn['story_text_2'] ?? '-' }}</p>
                                <p><strong>{{ __('Story Text 3:') }}</strong><br>{{ $aboutEn['story_text_3'] ?? '-' }}</p>
                                <p><strong>{{ __('Mission Title:') }}</strong> {{ $aboutEn['mission_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Mission Text:') }}</strong><br>{{ $aboutEn['mission_text'] ?? '-' }}</p>
                                <p><strong>{{ __('Quote Text:') }}</strong><br>{{ $aboutEn['quote_text'] ?? '-' }}</p>
                                <p><strong>{{ __('Quote Author:') }}</strong> {{ $aboutEn['quote_author'] ?? '-' }}</p>

                                @if($enImage1Url)
                                    <div class="mb-3">
                                        <strong>{{ __('Image 1:') }}</strong><br>
                                        <img src="{{ $enImage1Url }}" alt="English Image 1" style="max-width: 180px; border-radius: 8px;">
                                    </div>
                                @endif

                                @if($enImage2Url)
                                    <div class="mb-3">
                                        <strong>{{ __('Image 2:') }}</strong><br>
                                        <img src="{{ $enImage2Url }}" alt="English Image 2" style="max-width: 180px; border-radius: 8px;">
                                    </div>
                                @endif

                            @elseif($page->slug === 'contact')
                                <p><strong>{{ __('Hero Title:') }}</strong> {{ $contactEn['hero_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Form Title:') }}</strong> {{ $contactEn['form_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Address Title:') }}</strong> {{ $contactEn['address_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Address Text:') }}</strong><br>{{ $contactEn['address_text'] ?? '-' }}</p>
                                <p><strong>{{ __('Phone Title:') }}</strong> {{ $contactEn['phone_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Phone Text:') }}</strong> {{ $contactEn['phone_text'] ?? '-' }}</p>
                                <p><strong>{{ __('Email Title:') }}</strong> {{ $contactEn['email_title'] ?? '-' }}</p>
                                <p><strong>{{ __('Email Text:') }}</strong> {{ $contactEn['email_text'] ?? '-' }}</p>
                                <p><strong>{{ __('Map X:') }}</strong> {{ $contactEn['map_x'] ?? '-' }}</p>
                                <p><strong>{{ __('Map Y:') }}</strong> {{ $contactEn['map_y'] ?? '-' }}</p>
                                <p><strong>{{ __('Map Zoom:') }}</strong> {{ $contactEn['map_zoom'] ?? '-' }}</p>

                            @else
                                <p><strong>{{ __('Content:') }}</strong></p>
                                <div>{!! nl2br(e($translationEn?->content ?? '-')) !!}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
