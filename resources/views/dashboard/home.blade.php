<!DOCTYPE html>

@include('dashboard.app.head')

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->

  @include('dashboard.app.nav')
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  @include('dashboard.app.sidebar')

  <!-- Content Wrapper. Contains page content -->

  @yield('content')
  <!-- /.content-wrapper -->


  @include('dashboard.app.footer')

<!-- jQuery -->

@include('dashboard.app.script')
</body>

</html> 
