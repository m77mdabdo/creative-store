@include('auth.app.head')

<style>
    body { margin:0; overflow-x:hidden; }
    .auth-video{ position:fixed; inset:0; z-index:-2; }
    .auth-video video{ width:100%; height:100%; object-fit:cover; }
    .video-overlay{ position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:-1; }
    .auth-wrapper{ min-height:100vh; display:flex; justify-content:center; align-items:center; padding:20px; }
    .login-box{ width:420px; max-width:100%; }
    .card{
        border-radius:16px;
        background:rgba(255,255,255,.15);
        backdrop-filter:blur(12px);
        -webkit-backdrop-filter:blur(12px);
        border:1px solid rgba(255,255,255,.3);
        box-shadow:0 15px 35px rgba(0,0,0,.25);
        overflow:hidden;
    }
    .logo-header{ text-align:center; padding:20px 10px; }
    .logo-header img{ max-height:90px; }
    .card-body{ padding:30px; }
    .login-box-msg{ text-align:center; margin-bottom:25px; color:#fff; font-weight:500; }
    .form-control{ background:rgba(255,255,255,.85); }
    .btn-primary{ background:#000; border-color:#000; border-radius:6px; }
    .btn-primary:hover{ background:#333; border-color:#333; }
    a{ color:#fff; } a:hover{ opacity:.8; }
</style>

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
                    You are only one step away from your new password. Recover your password now.
                </p>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ request()->query('email') }}">

                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="New Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                    </div>

                    @error('password')
                        <div class="text-danger mb-2">{{ $message }}</div>
                    @enderror

                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Change Password
                    </button>
                </form>

                <p class="mt-3 text-center">
                    <a href="{{ route('login') }}">Login</a>
                </p>

            </div>

        </div>
    </div>
</div>

@include('auth.app.script')
</body>
</html>
