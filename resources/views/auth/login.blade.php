<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Creative Store</title>

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
    body{ margin:0; }

    .register-wrapper{
      display:flex;
      height:100vh;
      overflow:hidden;
      background:#fff;
    }

    .register-video{
      width:50%;
      position:relative;
      overflow:hidden;
      background:#000;
    }

    .register-video video{
      width:100%;
      height:100%;
      object-fit:cover;
      filter:none;
      transform: scale(1.02);
    }

    .video-overlay{
      position:absolute;
      inset:0;
      background:rgba(0,0,0,.25);
      pointer-events:none;
    }

    .register-form-side{
      width:50%;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 24px;
      background:#fff;
    }

    .register-form-side .register-box{
      width: 860px;
      margin: 0;
    }

    @media(max-width:992px){
      .register-video{ display:none; }
      .register-form-side{ width:100%; }
      .register-form-side .register-box{ width: 420px; }
    }

    .card{
      border-radius: 14px;
      border-top: 3px solid #000;
      box-shadow: 0 12px 30px rgba(0,0,0,0.08);
      overflow:hidden;
    }

    .logo-header{
      background:#fff;
      padding: 15px 20px 10px;
      border-bottom:none;
      text-align:center;
    }

    .logo-header img{
      max-height: 110px;
      width:auto;
      display:block;
      margin:0 auto;
    }

    .btn-primary{
      background-color:#000;
      border-color:#000;
      border-radius: 6px;
    }

    .btn-primary:hover{
      background-color:#333;
      border-color:#333;
    }

    .btn-facebook{
      background: linear-gradient(135deg, #1877f2, #0f5dc2);
      border:none;
      color:#fff;
    }

    .btn-google{
      background:#db4437;
      border:none;
      color:#fff;
    }

    .auth-split-card{
      display:flex;
      min-height: 560px;
    }

    .auth-split-left{
      width:42%;
      padding: 52px 44px;
      display:flex;
      flex-direction:column;
      justify-content:center;
      gap: 14px;
      border-right: 1px solid rgba(0,0,0,0.08);
    }

    .auth-split-right{
      width:58%;
      display:flex;
      align-items:center;
      justify-content:center;
      background:#fff;
    }

    .auth-right-inner{
      width: 100%;
      max-width: 420px;
      padding: 28px 28px 22px;
    }

    .invalid-feedback{
        font-size:13px;
        color:#dc3545;
    }

  </style>
</head>

<body class="hold-transition">

<div class="register-wrapper">

  <!-- LEFT VIDEO -->
  <div class="register-video">
    <video autoplay muted loop playsinline>
      <source src="{{ asset('cozastore/videos/register-bg.mp4') }}" type="video/mp4">
    </video>
    <div class="video-overlay"></div>
  </div>

  <!-- LOGIN SIDE -->
  <div class="register-form-side">
    <div class="register-box">
      <div class="card">

        <div class="card-header logo-header">
          <a href="{{ url('/') }}">
            <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}">
          </a>
        </div>

        <div class="card-body p-0">
          <div class="auth-split-card">

            <div class="auth-split-left">
              <h2>Hello Again</h2>
              <p>Login to continue and manage your account.</p>
              <a href="{{ route('register') }}" class="btn btn-primary" style="width:160px;">
                Create Account
              </a>
            </div>

            <div class="auth-split-right">
              <div class="auth-right-inner">

                <p class="login-box-msg mb-4">Sign in to start your session</p>

                {{-- رسالة عامة --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        Invalid email or password.
                    </div>
                @endif

                <form action="{{ route('login.submit') }}" method="post">
                  @csrf

                  <!-- EMAIL -->
                  <div class="input-group mb-3">
                    <input name="email"
                           type="email"
                           value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="Email"
                           required>

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


                  <!-- PASSWORD -->
                  <div class="input-group mb-3">
                    <input name="password"
                           type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Password"
                           required>

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
                        <label for="remember">Remember me</label>
                      </div>
                    </div>
                    <div class="col-4">
                      <button type="submit" class="btn btn-primary btn-block">
                        Sign In
                      </button>
                    </div>
                  </div>

                  <!-- SOCIAL LOGIN -->
                  <div class="social-auth-links text-center mt-3">
                    <a href="#" class="btn btn-block btn-facebook">
                      <i class="fab fa-facebook mr-2"></i>
                      Sign in using Facebook
                    </a>

                    <a href="{{ route('google.redirect') }}" class="btn btn-block btn-google">
                      <i class="fab fa-google-plus mr-2"></i>
                      Sign in using Google
                    </a>
                  </div>

                </form>

                <div class="mt-3">
                  <a href="{{ route('forgot-password') }}" class="d-block">
                    I forgot my password
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
