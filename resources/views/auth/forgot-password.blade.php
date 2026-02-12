@include('auth.app.head')

<style>
    body {
        margin: 0;
        overflow-x: hidden;
    }

    /* ================= VIDEO BACKGROUND ================= */
    .auth-video {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
    }

    .auth-video video {
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

    /* ================= CENTER CARD ================= */
    .auth-wrapper {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .login-box {
        width: 420px;
        max-width: 100%;
    }

    .card {
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
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
        margin-bottom: 25px;
        color: #fff;
        font-weight: 500;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.85);
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
        color: #fff;
    }

    a:hover {
        opacity: 0.8;
    }
</style>

<body class="hold-transition">

<!-- VIDEO BACKGROUND -->
<div class="auth-video">
    <video autoplay muted loop playsinline>
        <source src="{{ asset('cozastore/videos/register-bg.mp4') }}" type="video/mp4">
    </video>
</div>
<div class="video-overlay"></div>

<!-- CONTENT -->
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
                    You forgot your password? Here you can easily retrieve a new password.
                </p>

                <form action="recover-password.html" method="post">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Request new password
                    </button>
                </form>

                <p class="mt-3 mb-1 text-center">
                    <a href="{{ route('login') }}">Login</a>
                </p>
            </div>

        </div>
    </div>
</div>

@include('auth.app.script')
</body>
</html>
