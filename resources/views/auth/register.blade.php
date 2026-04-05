<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('site.register') }} | Creative Store</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

    <style>
        body {
            margin: 0;
        }

        .register-wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
            background: #fff;
        }

        .register-video {
            width: 50%;
            position: relative;
            overflow: hidden;
            background: #efe9e9;
        }

        .register-video video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(0%);
            transform: scale(1.02);
        }

        .video-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .35);
            pointer-events: none;
        }

        .register-form-side {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #fff;
        }

        .register-form-side .register-box {
            width: 860px;
            margin: 0;
        }

        @media(max-width:992px) {
            .register-video {
                display: none;
            }

            .register-form-side {
                width: 100%;
            }

            .register-form-side .register-box {
                width: 420px;
            }
        }

        .card {
            border-radius: 14px;
            border-top: 3px solid #000;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .logo-header {
            background: #fff;
            padding: 15px 20px 10px;
            border-bottom: none;
            text-align: center;
        }

        .logo-header img {
            max-height: 110px;
            width: auto;
            display: block;
            margin: 0 auto;
        }

        .btn-primary {
            background-color: #000;
            border-color: #000;
            border-radius: 6px;
        }

        .btn-primary:hover {
            background-color: #333;
            border-color: #333;
        }

        a {
            color: #000;
        }

        a:hover {
            color: #555;
        }

        .btn-facebook {
            background: linear-gradient(135deg, #1877f2, #0f5dc2);
            border: none;
            color: #fff;
        }

        .btn-facebook:hover {
            filter: brightness(0.95);
            color: #fff;
        }

        .btn-google {
            background: #000;
            border: none;
            color: #fff;
        }

        .btn-google:hover {
            background: #222;
            color: #fff;
        }

        .auth-split-card {
            display: flex;
            min-height: 560px;
        }

        .auth-split-left {
            width: 42%;
            padding: 52px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 14px;
            border-right: 1px solid rgba(0, 0, 0, 0.08);
        }

        .auth-split-left h2 {
            font-size: 34px;
            font-weight: 800;
            margin: 0;
            letter-spacing: .2px;
        }

        .auth-split-left p {
            margin: 0 0 8px 0;
            opacity: .75;
            line-height: 1.6;
            max-width: 300px;
        }

        .auth-split-right {
            width: 58%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        .auth-right-inner {
            width: 100%;
            max-width: 420px;
            padding: 28px 28px 22px;
        }

        @media(max-width: 992px) {
            .auth-split-card {
                flex-direction: column;
                min-height: auto;
            }

            .auth-split-left,
            .auth-split-right {
                width: 100%;
            }

            .auth-split-left {
                border-right: none;
                border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            }
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
        }

        .input-group>.iti {
            flex: 1 1 auto;
            width: 1%;
            margin: 0;
        }

        .iti {
            width: 100%;
            display: block;
        }

        .iti input.phone-control {
            width: 100% !important;
            height: 38px !important;
            line-height: 38px !important;
            padding-left: 90px !important;
        }

        .iti__flag-container,
        .iti__selected-flag {
            height: 38px !important;
        }

        .input-group>.iti>input.phone-control {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        html[dir="rtl"] body {
            text-align: right;
        }

        html[dir="rtl"] .auth-split-card {
            flex-direction: row-reverse;
        }

        html[dir="rtl"] .auth-split-left {
            border-right: 0;
            border-left: 1px solid rgba(0, 0, 0, 0.08);
            text-align: right;
            align-items: flex-start;
        }

        html[dir="rtl"] .auth-right-inner,
        html[dir="rtl"] .login-box-msg,
        html[dir="rtl"] .alert,
        html[dir="rtl"] .d-block {
            text-align: right;
        }

        html[dir="rtl"] .input-group {
            direction: ltr;
        }

        html[dir="rtl"] .input-group input {
            text-align: right;
            direction: rtl;
        }

        html[dir="rtl"] .icheck-primary {
            text-align: right;
        }

        html[dir="rtl"] .row.mb-2 {
            direction: rtl;
        }

        html[dir="rtl"] .auth-split-left p {
            max-width: 100%;
        }
    </style>
</head>

<body class="hold-transition">

    <div class="register-wrapper">

        <div class="register-video">
            <video autoplay muted loop playsinline>
                <source src="{{ asset('cozastore/videos/register2.mp4') }}" type="video/mp4">
            </video>
            <div class="video-overlay"></div>
        </div>

        <div class="register-form-side">
            <div class="register-box">
                <div class="card card-outline card-primary">

                    <div class="card-header logo-header">
                        <a href="{{ url('/') }}" class="logo">
                            <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}"
                                alt="Creative Store">
                        </a>
                    </div>

                    <div class="card-body p-0">
                        <div class="auth-split-card">

                            <div class="auth-split-left">
                                <h2>{{ __('site.welcome_to_creative_store') }}</h2>
                                <p>{{ __('site.register_side_text') }}</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-dark mt-3">
                                    {{ __('site.i_already_have_account') }}
                                </a>
                            </div>

                            <div class="auth-split-right">
                                <div class="auth-right-inner">

                                    <p class="login-box-msg mb-3">{{ __('site.register_new_membership') }}</p>

                                    @if ($errors->any())
                                        <div class="alert alert-danger py-2">
                                            <ul class="mb-0 pl-3">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form action="{{ route('register.submit') }}" method="post">
                                        @csrf

                                        <div class="input-group mb-3">
                                            <input name="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="{{ __('site.full_name') }}"
                                                value="{{ old('name') }}" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><span class="fas fa-user"></span></div>
                                            </div>
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback mb-2">{{ $message }}</div>
                                        @enderror

                                        <div class="input-group mb-3">
                                            <input name="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="{{ __('site.email') }}"
                                                value="{{ old('email') }}" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><span class="fas fa-envelope"></span>
                                                </div>
                                            </div>
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback mb-2">{{ $message }}</div>
                                        @enderror

                                        <div class="input-group mb-3">
                                            <input id="phone_input" type="tel" dir="ltr"
                                                class="form-control phone-control @error('phone') is-invalid @enderror"
                                                placeholder="{{ __('site.phone') }}" autocomplete="tel">

                                            <input type="hidden" name="phone" id="phone_e164"
                                                value="{{ old('phone') }}">

                                            <div class="input-group-append">
                                                <div class="input-group-text"><span class="fas fa-phone"></span></div>
                                            </div>
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback mb-2">{{ $message }}</div>
                                        @enderror

                                        <div class="input-group mb-3">
                                            <input name="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="{{ __('site.password') }}" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                            </div>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback mb-2">{{ $message }}</div>
                                        @enderror

                                        <div class="input-group mb-3">
                                            <input name="password_confirmation" type="password" class="form-control"
                                                placeholder="{{ __('site.retype_password') }}" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-8">
                                                <div class="icheck-primary">
                                                    <input type="checkbox" id="agreeTerms" name="terms" value="agree"
                                                        required>
                                                    <label for="agreeTerms">
                                                        {{ __('site.i_agree_to') }}
                                                        <a href="#">{{ __('site.terms') }}</a>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <button type="submit"
                                                    class="btn btn-primary btn-block">{{ __('site.register') }}</button>
                                            </div>
                                        </div>

                                        <div class="social-auth-links text-center mt-3">
                                            {{-- <a href="#" class="btn btn-block btn-facebook">
                                                <i class="fab fa-facebook mr-2"></i>
                                                {{ __('site.sign_up_using_facebook') }}
                                            </a> --}}

                                            <a href="{{ route('google.redirect') }}" class="btn btn-block btn-danger">
                                                <i class="fab fa-google-plus mr-2"></i>
                                                {{ __('site.sign_up_using_google') }}
                                            </a>
                                        </div>
                                    </form>

                                      <!-- 🌍 LANGUAGE SWITCHER (ADD ONLY) -->
                                    <div style="margin-top:15px; text-align:center;">
                                        <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('ar', null, [], true) }}"
                                            style="padding:6px 14px; border-radius:20px; background:#eee; margin:3px; display:inline-block; text-decoration:none;">
                                            🇸🇦 عربي
                                        </a>

                                        <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                                            style="padding:6px 14px; border-radius:20px; background:#eee; margin:3px; display:inline-block; text-decoration:none;">
                                            🇺🇸 EN
                                        </a>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    @include('auth.app.script')

</body>

</html>
