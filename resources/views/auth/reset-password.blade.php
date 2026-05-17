@include('auth.app.head')

<link rel="stylesheet" href="{{ versioned_asset('css/site/auth-reset-password.css') }}">

<body class="hold-transition">

<div class="auth-video">
    <video autoplay muted loop playsinline>
        <source src="{{ asset('cozastore/videos/recover-password.mp4') }}" type="video/mp4">
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
                    {{ __('site.reset_password_message') }}
                </p>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ request()->query('email') }}">

                    <!-- email -->
                    <div class="input-group mb-3">
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="{{ __('site.email') }}"
                               value="{{ request()->query('email') }}"
                               readonly>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>

                    <!-- new password -->
                    <div class="input-group mb-3">
                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="{{ __('site.new_password') }}"
                               required>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    @error('password')
                        <div class="text-danger mb-2">{{ $message }}</div>
                    @enderror

                    <!-- confirm password -->
                    <div class="input-group mb-3">
                        <input type="password"
                               name="password_confirmation"
                               class="form-control"
                               placeholder="{{ __('site.confirm_password') }}"
                               required>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        {{ __('site.change_password') }}
                    </button>
                </form>

                <p class="mt-3 text-center">
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
