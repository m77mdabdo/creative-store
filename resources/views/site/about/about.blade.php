@extends('site.app.layout')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $image1 = $content['image_1'] ?? 'images/about-01.jpg';
    $image2 = $content['image_2'] ?? 'images/about-02.jpg';

    $image1Url = str_starts_with($image1, 'images/')
        ? asset($image1)
        : Storage::url($image1);

    $image2Url = str_starts_with($image2, 'images/')
        ? asset($image2)
        : Storage::url($image2);
@endphp

<section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('{{ asset('images/bg-01.jpg') }}');">
    <h2 class="ltext-105 cl0 txt-center">
        {{ $content['hero_title'] ?? $pageTitle ?? 'About' }}
    </h2>
</section>

<section class="bg0 p-t-75 p-b-120">
    <div class="container">
        <div class="row p-b-148">
            <div class="col-md-7 col-lg-8">
                <div class="p-t-7 p-r-85 p-r-15-lg p-r-0-md">
                    <h3 class="mtext-111 cl2 p-b-16">
                        {{ $content['story_title'] ?? 'Our Story' }}
                    </h3>

                    @if(!empty($content['story_text_1']))
                        <p class="stext-113 cl6 p-b-26">
                            {{ $content['story_text_1'] }}
                        </p>
                    @endif

                    @if(!empty($content['story_text_2']))
                        <p class="stext-113 cl6 p-b-26">
                            {{ $content['story_text_2'] }}
                        </p>
                    @endif

                    @if(!empty($content['story_text_3']))
                        <p class="stext-113 cl6 p-b-26">
                            {{ $content['story_text_3'] }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="col-11 col-md-5 col-lg-4 m-lr-auto">
                <div class="how-bor1">
                    <div class="hov-img0">
                        <img src="{{ $image1Url }}" alt="About Image 1" loading="lazy" decoding="async">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="order-md-2 col-md-7 col-lg-8 p-b-30">
                <div class="p-t-7 p-l-85 p-l-15-lg p-l-0-md">
                    <h3 class="mtext-111 cl2 p-b-16">
                        {{ $content['mission_title'] ?? 'Our Mission' }}
                    </h3>

                    @if(!empty($content['mission_text']))
                        <p class="stext-113 cl6 p-b-26">
                            {{ $content['mission_text'] }}
                        </p>
                    @endif

                    @if(!empty($content['quote_text']))
                        <div class="bor16 p-l-29 p-b-9 m-t-22">
                            <p class="stext-114 cl6 p-r-40 p-b-11">
                                {{ $content['quote_text'] }}
                            </p>

                            @if(!empty($content['quote_author']))
                                <span class="stext-111 cl8">
                                    - {{ $content['quote_author'] }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="order-md-1 col-11 col-md-5 col-lg-4 m-lr-auto p-b-30">
                <div class="how-bor2">
                    <div class="hov-img0">
                        <img src="{{ $image2Url }}" alt="About Image 2" loading="lazy" decoding="async">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
