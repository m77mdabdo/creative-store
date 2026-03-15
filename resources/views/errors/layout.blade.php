<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Error @yield('code')</title>

<style>
body{
    font-family: Arial;
    background:#f7f7f7;
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

.error-box{
    text-align:center;
}

.error-code{
    font-size:120px;
    font-weight:bold;
}

.error-message{
    font-size:22px;
    margin:10px 0;
}

.error-actions a{
    display:inline-block;
    padding:12px 25px;
    background:#000;
    color:#fff;
    text-decoration:none;
    margin-top:15px;
}
</style>

</head>

<body>

<div class="error-box">

<div class="error-code">
@yield('code')
</div>

<div class="error-message">
@yield('message')
</div>

<div class="error-actions">
<a href="{{ url('/') }}">Back to Home</a>
</div>

</div>

</body>
</html>

