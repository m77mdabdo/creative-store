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

    <link rel="stylesheet" href="{{ versioned_asset('css/site/auth-login.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/tokens.css') }}">
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
                                        <div class="alert alert-danger" role="alert">
                                            {{ $errors->first() }}
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
