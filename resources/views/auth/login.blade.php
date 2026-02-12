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

    /* ✅ الفيديو ملون */
    .register-video video{
      width:100%;
      height:100%;
      object-fit:cover;
      filter:none;  /* تم إلغاء الأبيض والأسود */
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

    .btn-facebook:hover{
      filter: brightness(0.95);
      color:#fff;
    }

    .btn-google{
      background:#db4437;
      border:none;
      color:#fff;
    }

    .btn-google:hover{
      background:#c1351d;
      color:#fff;
    }

    a{ color:#000; }
    a:hover{ color:#555; }

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

    .auth-split-left h2{
      font-size: 34px;
      font-weight: 800;
      margin:0;
    }

    .auth-split-left p{
      margin:0 0 8px 0;
      opacity:.75;
      line-height:1.6;
      max-width: 300px;
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

    @media(max-width: 992px){
      .auth-split-card{ flex-direction:column; min-height:auto; }
      .auth-split-left, .auth-split-right{ width:100%; }
      .auth-split-left{ border-right:none; border-bottom:1px solid rgba(0,0,0,0.08); }
    }
  </style>
</head>

<body class="hold-transition">

<div class="register-wrapper">

  <!-- LEFT SIDE - VIDEO -->
  <div class="register-video">
    <video autoplay muted loop playsinline>
      <source src="{{ asset('cozastore/videos/register-bg.mp4') }}" type="video/mp4">
    </video>
    <div class="video-overlay"></div>
  </div>

  <!-- RIGHT SIDE - LOGIN CARD -->
  <div class="register-form-side">
    <div class="register-box">
      <div class="card">

        <!-- LOGO -->
        <div class="card-header logo-header">
          <a href="{{ url('/') }}">
            <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}" alt="Creative Store">
          </a>
        </div>

        <!-- SPLIT BODY -->
        <div class="card-body p-0">
          <div class="auth-split-card">

            <!-- LEFT PANEL -->
            <div class="auth-split-left">
              <h2>Hello Again</h2>
              <p>Login to continue and manage your account.</p>
              <a href="{{ route('register') }}" class="btn btn-primary" style="width:160px;">
                Create Account
              </a>
            </div>

            <!-- RIGHT PANEL -->
            <div class="auth-split-right">
              <div class="auth-right-inner">

                <p class="login-box-msg mb-4">Sign in to start your session</p>

                <form action="{{ route('login') }}" method="post">
                  @csrf

                  <div class="input-group mb-3">
                    <input name="email" type="email" class="form-control" placeholder="Email" required>
                    <div class="input-group-append">
                      <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                  </div>

                  <div class="input-group mb-3">
                    <input name="password" type="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                      <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-8">
                      <div class="icheck-primary">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                      </div>
                    </div>
                    <div class="col-4">
                      <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                  </div>

                  <!-- SOCIAL LOGIN -->
                  <div class="social-auth-links text-center mt-3">
                    <a href="#" class="btn btn-block btn-facebook">
                      <i class="fab fa-facebook mr-2"></i>
                      Sign in using Facebook
                    </a>

                    <a href="#" class="btn btn-block btn-google">
                      <i class="fab fa-google-plus mr-2"></i>
                      Sign in using Google
                    </a>
                  </div>

                </form>

                <div class="mt-3">
                  <a href="{{ route('forgot-password') }}" class="d-block">I forgot my password</a>
                  {{-- <a href="{{ route('register') }}" class="d-block">Register a new membership</a> --}}
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
