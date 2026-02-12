<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">

  <!-- icheck bootstrap -->
  <link rel="stylesheet"
        href="{{ asset('AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

  <!-- Theme style -->
  <link rel="stylesheet"
        href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

  <!-- Black & White Creative Store Theme -->
  <style>

  /* ========== LOGO AREA ========== */
  .logo-header{
      background: #fff;
      padding: 15px 20px 10px;
      border-bottom: none;
  }

  .logo-header img{
      max-height: 125px;   /* حجم متوازن */
      width: auto;
      display: block;
      margin: 0 auto;
  }

  /* ========== CARD STYLE ========== */
  .card{
      border-radius: 14px;
      border-top: 3px solid #000;
      box-shadow: 0 12px 30px rgba(0,0,0,0.08);
  }

  .card-header{
      padding: 20px 20px 10px;
      border-bottom: none;
      background: #fff;
  }

  .card-body{
      padding-top: 20px;
  }

  /* ========== BUTTONS ========== */
  .btn-primary{
      background-color: #000;
      border-color: #000;
      border-radius: 6px;
  }

  .btn-primary:hover{
      background-color: #333;
      border-color: #333;
  }

  /* Facebook Button */
  .btn-facebook{
      background: linear-gradient(135deg, #1877f2, #0f5dc2);
      border:none;
      color:#fff;
  }

  .btn-facebook:hover{
      filter: brightness(0.95);
      color:#fff;
  }

  /* Google Button */
  .btn-google{
      background:#000;
      border:none;
      color:#fff;
  }

  .btn-google:hover{
      background:#222;
      color:#fff;
  }

  /* ========== LINKS ========== */
  a{
      color:#000;
  }

  a:hover{
      color:#555;
  }

  /* ================= VIDEO SPLIT LAYOUT (ADDED) ================= */
  .register-wrapper{
      display:flex;
      height:100vh;
      overflow:hidden;
  }

  /* الشمال فيديو */
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
      filter: grayscale(100%); /* أبيض وأسود */
      transform: scale(1.02);
  }

  /* طبقة احترافية */
  .video-overlay{
      position:absolute;
      top:0;
      left:0;
      width:100%;
      height:100%;
      background:rgba(0,0,0,0.35);
      pointer-events:none;
  }

  /* اليمين الفورم */
  .register-form-side{
      width:50%;
      background:#fff;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 24px;
  }

  /* تثبيت حجم الفورم */
  .register-form-side .register-box{
      width: 420px;
      margin: 0;
  }

  /* الموبايل: نخفي الفيديو */
  @media(max-width:992px){
      .register-video{
          display:none;
      }
      .register-form-side{
          width:100%;
      }
  }


  /* LEFT SIDE - Black & White Elegant */
.auth-left{
    width: 45%;
    padding: 60px 50px;
    background: #000;   /* خلفية سوداء */
    color: #fff;
    display:flex;
    flex-direction:column;
    justify-content:center;
    position:relative;
}

/* خط بسيط ديكور */
.auth-left::after{
    content:"";
    position:absolute;
    right:0;
    top:0;
    width:2px;
    height:100%;
    background:#e5e5e5;
}

/* عنوان */
.auth-left h2{
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 15px;
    letter-spacing: 1px;
}

/* النص */
.auth-left p{
    opacity:.85;
    line-height: 1.6;
    margin-bottom: 30px;
    max-width: 330px;
}

/* زرار أبيض */
.auth-left .left-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    height: 46px;
    padding: 0 28px;
    border-radius: 6px;
    background:#fff;
    color:#000;
    font-weight:600;
    text-decoration:none;
    transition:.3s ease;
}

.auth-left .left-btn:hover{
    background:#f1f1f1;
    transform: translateY(-2px);
}


  </style>

</head>
