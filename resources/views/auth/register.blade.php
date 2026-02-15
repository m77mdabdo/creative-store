<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Creative Store</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

    <style>
        body { margin: 0; }

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
            .register-video { display: none; }
            .register-form-side { width: 100%; }
            .register-form-side .register-box { width: 420px; }
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

        a { color: #000; }
        a:hover { color: #555; }

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

        .auth-split-card { display: flex; min-height: 560px; }

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
            .auth-split-card { flex-direction: column; min-height: auto; }
            .auth-split-left, .auth-split-right { width: 100%; }
            .auth-split-left { border-right: none; border-bottom: 1px solid rgba(0, 0, 0, 0.08); }
        }

        /* Validation UI */
        .is-invalid { border-color: #dc3545; }
        .invalid-feedback { display: block; }
    </style>
</head>

<body class="hold-transition">

<div class="register-wrapper">

    <!-- LEFT SIDE - VIDEO -->
    <div class="register-video">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('cozastore/videos/register2.mp4') }}" type="video/mp4">
        </video>
        <div class="video-overlay"></div>
    </div>

    <!-- RIGHT SIDE - REGISTER CARD -->
    <div class="register-form-side">
        <div class="register-box">
            <div class="card card-outline card-primary">

                <div class="card-header logo-header">
                    <a href="{{ url('/') }}" class="logo">
                        <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}" alt="Creative Store">
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="auth-split-card">

                        <!-- LEFT PANEL -->
                        <div class="auth-split-left">
                            <h2>Welcome to Creative Store</h2>
                            <p>Join us today and experience the best shopping experience with exclusive offers and a wide range of products.</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-dark mt-3">I already have an account</a>
                        </div>

                        <!-- RIGHT PANEL -->
                        <div class="auth-split-right">
                            <div class="auth-right-inner">

                                <p class="login-box-msg mb-3">Register a new membership</p>

                                {{-- Error summary (optional) --}}
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
                                            placeholder="Full name" value="{{ old('name') }}" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-user"></span></div>
                                        </div>
                                    </div>
                                    @error('name') <div class="invalid-feedback mb-2">{{ $message }}</div> @enderror

                                    <div class="input-group mb-3">
                                        <input name="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="Email" value="{{ old('email') }}" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                        </div>
                                    </div>
                                    @error('email') <div class="invalid-feedback mb-2">{{ $message }}</div> @enderror

                                    <div class="input-group mb-3">
                                        <input name="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Password" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                        </div>
                                    </div>
                                    @error('password') <div class="invalid-feedback mb-2">{{ $message }}</div> @enderror

                                    <div class="input-group mb-3">
                                        <input name="password_confirmation" type="password"
                                            class="form-control"
                                            placeholder="Retype password" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-8">
                                            <div class="icheck-primary">
                                                <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
                                                <label for="agreeTerms">I agree to the <a href="#">terms</a></label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                                        </div>
                                    </div>

                                    <!-- ✅ Social links (برا الـ row) -->
                                    <div class="social-auth-links text-center mt-3">
                                        <a href="#" class="btn btn-block btn-facebook">
                                            <i class="fab fa-facebook mr-2"></i>
                                            Sign up using Facebook
                                        </a>

                                        <a href="{{ route('google.redirect') }}" class="btn btn-block btn-danger">
                                            <i class="fab fa-google-plus mr-2"></i>
                                            Sign up using Google+
                                        </a>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div><!-- /card-body -->

            </div>
        </div>
    </div>

</div>

@include('auth.app.script')
</body>
</html>


{{--
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Creative Store</title>

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">

    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

    <style>
        body {
            margin: 0;
            overflow-x: hidden;
        }

        /* ================= VIDEO BACKGROUND ================= */
        .register-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
        }

        .register-video video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: -1;
        }

        /* ================= CENTER CONTENT ================= */
        .register-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
        }

        .register-box {
            width: 450px;
            max-width: 100%;
        }

        /* ================= GLASS CARD ================= */
        .card {
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .logo-header {
            text-align: center;
            padding: 20px 10px;
        }

        .logo-header img {
            max-height: 90px;
        }

        .card-body {
            padding: 30px;
        }

        .login-box-msg {
            text-align: center;
            font-weight: 600;
            margin-bottom: 25px;
            color: #fff;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.85);
        }

        /* ================= BUTTONS ================= */
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

        .btn-facebook:hover {
            filter: brightness(0.95);
            color: #fff;
        }

        .btn-google {
            background: #db4437;
            border: none;
            color: #fff;
        }

        .btn-google:hover {
            background: #c1351d;
            color: #fff;
        }

        a {
            color: #fff;
        }

        a:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body class="hold-transition">

    <!-- VIDEO BACKGROUND -->
    <div class="register-video">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('cozastore/videos/register1.mp4') }}" type="video/mp4">
        </video>
    </div>
    <div class="video-overlay"></div>

    <!-- FORM CENTER -->
    <div class="register-wrapper">
        <div class="register-box">
            <div class="card">

                <div class="card-header logo-header">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}"
                            alt="Creative Store">
                    </a>
                </div>

                <div class="card-body">

                    <p class="login-box-msg">Register a new membership</p>

                    <form action="{{ route('register') }}" method="post">
                        @csrf

                        <div class="input-group mb-3">
                            <input name="name" type="text" class="form-control"
                                placeholder="Full name" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <input name="email" type="email" class="form-control"
                                placeholder="Email" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <input name="password" type="password" class="form-control"
                                placeholder="Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <input name="password_confirmation" type="password"
                                class="form-control" placeholder="Retype password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            Register
                        </button>

                        <!-- SOCIAL BUTTONS -->
                        <div class="social-auth-links text-center mt-3">
                            <a href="#" class="btn btn-block btn-facebook">
                                <i class="fab fa-facebook mr-2"></i>
                                Sign up using Facebook
                            </a>

                            <a href="#" class="btn btn-block btn-google">
                                <i class="fab fa-google-plus mr-2"></i>
                                Sign up using Google
                            </a>
                        </div>

                    </form>

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}">I already have a membership</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('auth.app.script')
</body>

</html> --}}
