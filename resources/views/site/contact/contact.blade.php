@extends('site.app.layout')

@section('content')
    @php
        $content = $content ?? [];
    @endphp

    <section class="bg-img1 txt-center p-lr-15 p-tb-92"
        style="background-image: url('{{ asset('cozastore/images/bg-01.jpg') }}');">
        <h2 class="ltext-105 cl0 txt-center">
            {{ $content['hero_title'] ?? __('site.contact') }}
        </h2>
    </section>

    <section class="bg0 p-t-104 p-b-116">
        <div class="container">
            <div class="flex-w flex-tr">

                <div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">

                    @if(session('success'))
                        <div class="alert alert-success m-b-25">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger m-b-25">
                            <ul class="m-b-0 p-l-20">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf

                        <h4 class="mtext-105 cl2 txt-center p-b-30">
                            {{ $content['form_title'] ?? __('site.send_us_message') }}
                        </h4>

                        <div class="bor8 m-b-20">
                            <input class="stext-111 cl2 plh3 size-116 p-lr-28"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="{{ __('site.your_name') }}"
                                required>
                        </div>

                        <div class="bor8 m-b-20 how-pos4-parent">
                            <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="{{ __('site.your_email_address') }}"
                                required>

                            <img class="how-pos4 pointer-none"
                                src="{{ asset('cozastore/images/icons/icon-email.png') }}"
                                alt="ICON">
                        </div>

                        <div class="bor8 m-b-20">
                            <input class="stext-111 cl2 plh3 size-116 p-lr-28"
                                type="text"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="{{ __('site.phone_number') }}">
                        </div>

                        <div class="bor8 m-b-20">
                            <input class="stext-111 cl2 plh3 size-116 p-lr-28"
                                type="text"
                                name="subject"
                                value="{{ old('subject') }}"
                                placeholder="{{ __('site.subject') }}">
                        </div>

                        <div class="bor8 m-b-30">
                            <textarea class="stext-111 cl2 plh3 size-120 p-lr-28 p-tb-25"
                                name="message"
                                placeholder="{{ __('site.how_can_we_help') }}"
                                required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit"
                            class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer">
                            {{ __('site.submit') }}
                        </button>
                    </form>
                </div>

                <div class="size-210 bor10 flex-w flex-col-m p-lr-93 p-tb-30 p-lr-15-lg w-full-md">
                    <div class="flex-w w-full p-b-42">
                        <span class="fs-18 cl5 txt-center size-211">
                            <span class="lnr lnr-map-marker"></span>
                        </span>

                        <div class="size-212 p-t-2">
                            <span class="mtext-110 cl2">
                                {{ $content['address_title'] ?? __('site.address') }}
                            </span>

                            <p class="stext-115 cl6 size-213 p-t-18">
                                {{ $content['address_text'] ?? __('site.default_address') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex-w w-full p-b-42">
                        <span class="fs-18 cl5 txt-center size-211">
                            <span class="lnr lnr-phone-handset"></span>
                        </span>

                        <div class="size-212 p-t-2">
                            <span class="mtext-110 cl2">
                                {{ $content['phone_title'] ?? __('site.lets_talk') }}
                            </span>

                            <p class="stext-115 cl1 size-213 p-t-18">
                                {{ $content['phone_text'] ?? '+966 5X XXX XXXX' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex-w w-full">
                        <span class="fs-18 cl5 txt-center size-211">
                            <span class="lnr lnr-envelope"></span>
                        </span>

                        <div class="size-212 p-t-2">
                            <span class="mtext-110 cl2">
                                {{ $content['email_title'] ?? __('site.support_email') }}
                            </span>

                            <p class="stext-115 cl1 size-213 p-t-18">
                                {{ $content['email_text'] ?? 'support@example.com' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <div class="map">
        <div class="size-303"
            id="google_map"
            data-map-x="{{ $content['map_x'] ?? '24.7136' }}"
            data-map-y="{{ $content['map_y'] ?? '46.6753' }}"
            data-pin="{{ asset('cozastore/images/icons/pin.png') }}"
            data-scrollwhell="0"
            data-draggable="1"
            data-zoom="{{ $content['map_zoom'] ?? '11' }}">
        </div>
    </div>
@endsection
