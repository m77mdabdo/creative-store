<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('site.login') }} | Creative Store</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;
            background: #fff;
        }

        .register-wrapper {
            display: flex;
            min-height: 100vh;
            background: #fff;
        }

        .register-video {
            width: 50%;
            position: relative;
            overflow: hidden;
            background: #000;
        }

        .register-video video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transform: scale(1.02);
        }

        .video-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .25);
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
            width: 100%;
            max-width: 860px;
            margin: 0;
        }

        .card {
            border-radius: 14px;
            border-top: 3px solid #000;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 0;
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
            max-width: 100%;
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

        .btn-facebook {
            background: linear-gradient(135deg, #1877f2, #0f5dc2);
            border: none;
            color: #fff;
        }

        .btn-google {
            background: #db4437;
            border: none;
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
            margin: 0;
            font-size: 32px;
            line-height: 1.25;
        }

        .auth-split-left p {
            margin: 0;
            color: #666;
            line-height: 1.8;
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

        .login-box-msg {
            font-size: 15px;
        }

        .invalid-feedback {
            font-size: 13px;
            color: #dc3545;
        }

        .lang-switcher {
            margin-top: 15px;
            text-align: center;
        }

        .lang-switcher a {
            padding: 6px 14px;
            border-radius: 20px;
            background: #eee;
            margin: 3px;
            display: inline-block;
            text-decoration: none;
            color: #222;
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

        html[dir="rtl"] .row.mb-3 {
            direction: rtl;
        }

        @media (max-width: 1200px) {
            .auth-split-left {
                padding: 40px 28px;
            }

            .auth-right-inner {
                padding: 24px 22px 20px;
            }
        }

        @media (max-width: 992px) {
            .register-video {
                display: none;
            }

            .register-form-side {
                width: 100%;
                min-height: 100vh;
                padding: 24px 16px;
            }

            .register-form-side .register-box {
                max-width: 720px;
            }

            .auth-split-card {
                min-height: auto;
            }
        }

        @media (max-width: 768px) {
            .register-form-side {
                align-items: flex-start;
                padding: 18px 12px;
            }

            .register-form-side .register-box {
                max-width: 100%;
            }

            .logo-header {
                padding: 14px 16px 8px;
            }

            .logo-header img {
                max-height: 90px;
            }

            .auth-split-card {
                flex-direction: column !important;
            }

            .auth-split-left,
            .auth-split-right {
                width: 100%;
            }

            .auth-split-left {
                border-right: 0 !important;
                border-left: 0 !important;
                border-bottom: 1px solid rgba(0, 0, 0, 0.08);
                padding: 24px 18px;
                gap: 10px;
                text-align: center !important;
                align-items: center !important;
            }

            .auth-split-left h2 {
                font-size: 24px;
            }

            .auth-split-left p {
                font-size: 14px;
                line-height: 1.7;
            }

            .auth-split-left .btn {
                width: 100% !important;
                max-width: 240px;
            }

            .auth-right-inner {
                max-width: 100%;
                padding: 20px 16px 18px;
            }

            .row.mb-3 {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-bottom: 16px !important;
            }

            .row.mb-3>.col-8,
            .row.mb-3>.col-4 {
                max-width: 100%;
                flex: 0 0 100%;
            }

            .row.mb-3 .btn {
                width: 100%;
            }

            .social-auth-links .btn {
                font-size: 14px;
                padding-top: 10px;
                padding-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .register-form-side {
                padding: 10px;
            }

            .card {
                border-radius: 12px;
            }

            .auth-split-left {
                padding: 20px 14px;
            }

            .auth-right-inner {
                padding: 18px 14px 16px;
            }

            .login-box-msg {
                font-size: 14px;
                margin-bottom: 18px !important;
            }

            .input-group .form-control {
                height: 44px;
                font-size: 14px;
            }

            .input-group-text {
                min-width: 44px;
                justify-content: center;
            }

            .btn {
                font-size: 14px;
            }

            .lang-switcher a {
                width: calc(50% - 10px);
                min-width: 120px;
            }
        }
    </style>
</head>

<body class="hold-transition">

    <div class="register-wrapper">

        <div class="register-video">
            <video autoplay muted loop playsinline>
                <source src="{{ asset('cozastore/videos/register-bg.mp4') }}" type="video/mp4">
            </video>
            <div class="video-overlay"></div>
        </div>

        <div class="register-form-side">
            <div class="register-box">
                <div class="card">

                    <div class="card-header logo-header">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}"
                                alt="Creative Store Logo">
                        </a>
                    </div>

                    <div class="card-body p-0">
                        <div class="auth-split-card">

                            <div class="auth-split-left">
                                <h2>{{ __('site.hello_again') }}</h2>
                                <p>{{ __('site.login_side_text') }}</p>
                                <a href="{{ route('register') }}" class="btn btn-primary" style="width:160px;">
                                    {{ __('site.create_account') }}
                                </a>
                            </div>

                            <div class="auth-split-right">
                                <div class="auth-right-inner">

                                    <p class="login-box-msg mb-4">{{ __('site.sign_in_start_session') }}</p>

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            {{ __('site.invalid_email_or_password') }}
                                        </div>
                                    @endif

                                    <form action="{{ route('login.submit') }}" method="post">
                                        @csrf

                                        <div class="input-group mb-3">
                                            <input name="email" type="email" value="{{ old('email') }}"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="{{ __('site.email') }}" required>

                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-envelope"></span>
                                                </div>
                                            </div>
                                        </div>

                                        @error('email')
                                            <div class="invalid-feedback d-block mb-2">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        <div class="input-group mb-3">
                                            <input name="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="{{ __('site.password') }}" required>

                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-lock"></span>
                                                </div>
                                            </div>
                                        </div>

                                        @error('password')
                                            <div class="invalid-feedback d-block mb-2">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        <div class="row mb-3">
                                            <div class="col-8">
                                                <div class="icheck-primary">
                                                    <input type="checkbox" id="remember" name="remember">
                                                    <label for="remember">{{ __('site.remember_me') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    {{ __('site.sign_in') }}
                                                </button>
                                            </div>
                                        </div>

                                        <div class="social-auth-links text-center mt-3">
                                            <a href="{{ route('google.redirect') }}" class="btn btn-block btn-google">
                                                <i class="fab fa-google-plus mr-2"></i>
                                                {{ __('site.sign_in_using_google') }}
                                            </a>
                                        </div>

                                    </form>

                                    <div class="lang-switcher">
                                        <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('ar', null, [], true) }}">
                                            🇸🇦 عربي
                                        </a>

                                        <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
                                            🇺🇸 EN
                                        </a>
                                    </div>

                                    <div class="mt-3">
                                        <a href="{{ route('forgot-password') }}" class="d-block">
                                            {{ __('site.forgot_password_question') }}
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
