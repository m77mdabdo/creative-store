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

    <link rel="stylesheet" href="{{ versioned_asset('css/site/auth-register.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/tokens.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/base.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/auth-layout.css') }}">
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
                                                        <a href="{{ route('frontend.pages.show', 'terms') }}" target="_blank" rel="noopener">{{ __('site.terms') }}</a>
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
