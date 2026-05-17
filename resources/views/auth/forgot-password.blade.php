@include('auth.app.head')

<link rel="stylesheet" href="{{ versioned_asset('css/site/auth-forgot-password.css') }}">

<body class="hold-transition">

<div class="auth-video">
    <video autoplay muted loop playsinline>
        <source src="{{ asset('cozastore/videos/register-bg.mp4') }}" type="video/mp4">
    </video>
</div>
<div class="video-overlay"></div>

<div class="auth-wrapper">
    <div class="login-box">
        <div class="card">

            <div class="card-header logo-header">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}" alt="Creative Store">
                </a>
            </div>

            <div class="card-body">
                <p class="login-box-msg">
                    {{ __('site.forgot_password_message') }}
                </p>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('forgot-password.submit') }}" method="POST">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="{{ __('site.email') }}"
                               value="{{ old('email') }}"
                               required>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>

                    @error('email')
                        <div class="text-danger mb-2">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn btn-primary btn-block">
                        {{ __('site.request_new_password') }}
                    </button>
                </form>

                <p class="mt-3 mb-1 text-center">
                    <a href="{{ route('login') }}">
                        {{ __('site.back_to_login') }}
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>

@include('auth.app.script')
</body>
</html>
