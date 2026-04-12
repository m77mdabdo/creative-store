<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('site.Dashboard')) | {{ config('app.name', 'Creative Store') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">

    {{-- Ionicons --}}
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    {{-- Tempusdominus Bootstrap 4 --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">

    {{-- iCheck --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    {{-- JQVMap --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/jqvmap/jqvmap.min.css') }}">

    {{-- Theme style --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

    {{-- overlayScrollbars --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    {{-- Daterange picker --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/daterangepicker/daterangepicker.css') }}">

    {{-- summernote --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/summernote/summernote-bs4.min.css') }}">

    <style>
        :root {
            --sidebar-width: 250px;
            --radius-md: 0.75rem;
        }

        html, body {
            min-height: 100%;
        }

        html[lang="en"] body {
            font-family: 'Source Sans Pro', sans-serif;
        }

        html[lang="ar"] body {
            font-family: 'Cairo', sans-serif;
        }

        body {
            font-size: 14px;
            text-align: start;
            overflow-x: hidden;
        }

        .content-wrapper > .content {
            padding: 0 0.75rem;
        }

        .content-header {
            padding: 12px 0.75rem;
        }

        .card-body {
            padding: 1rem;
        }

        .table td,
        .table th {
            vertical-align: middle;
            padding: 0.65rem;
        }

        .btn {
            border-radius: 0.35rem;
        }

        .main-sidebar {
            width: var(--sidebar-width);
        }

        /* =====================
           LTR (English)
        ===================== */
        html[dir="ltr"] .main-sidebar {
            left: 0;
            right: auto;
        }

        html[dir="ltr"] .content-wrapper,
        html[dir="ltr"] .main-footer,
        html[dir="ltr"] .main-header {
            margin-left: var(--sidebar-width);
            margin-right: 0;
            transition: margin .2s ease-in-out;
        }

        /* =====================
           RTL (Arabic)
        ===================== */
        html[dir="rtl"] body {
            direction: rtl;
            text-align: right;
        }

        html[dir="rtl"] .main-sidebar {
            right: 0;
            left: auto;
        }

        html[dir="rtl"] .content-wrapper,
        html[dir="rtl"] .main-footer,
        html[dir="rtl"] .main-header {
            margin-right: var(--sidebar-width);
            margin-left: 0;
            transition: margin .2s ease-in-out;
        }

        @media (max-width: 991.98px) {
            html[dir="ltr"] .content-wrapper,
            html[dir="ltr"] .main-footer,
            html[dir="ltr"] .main-header,
            html[dir="rtl"] .content-wrapper,
            html[dir="rtl"] .main-footer,
            html[dir="rtl"] .main-header {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }

        html[dir="rtl"] .brand-link,
        html[dir="rtl"] .user-panel,
        html[dir="rtl"] .nav-sidebar .nav-link,
        html[dir="rtl"] .navbar-nav,
        html[dir="rtl"] .content-header .container-fluid,
        html[dir="rtl"] .card-header,
        html[dir="rtl"] .dropdown-item,
        html[dir="rtl"] .small-box-footer {
            text-align: right;
        }

        html[dir="rtl"] .brand-link {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: row-reverse;
        }

        html[dir="rtl"] .brand-image {
            margin-right: 0;
            margin-left: .5rem;
        }

        html[dir="rtl"] .user-panel {
            flex-direction: row-reverse;
        }

        html[dir="rtl"] .user-panel .image {
            padding-left: .75rem;
            padding-right: 0;
        }

        html[dir="rtl"] .user-panel .info {
            padding-left: 5px;
            padding-right: 0;
        }

        html[dir="rtl"] .nav-sidebar > .nav-item > .nav-link,
        html[dir="rtl"] .nav-treeview > .nav-item > .nav-link {
            display: flex;
            align-items: center;
            flex-direction: row-reverse;
        }

        html[dir="rtl"] .nav-sidebar .nav-icon {
            margin-left: .5rem;
            margin-right: 0;
        }

        html[dir="rtl"] .nav-sidebar .nav-link > p {
            width: 100%;
            display: flex;
            align-items: center;
            flex-direction: row-reverse;
            margin: 0;
        }

        html[dir="rtl"] .nav-sidebar .nav-link p .right {
            margin-right: auto !important;
            margin-left: 0 !important;
        }

        html[dir="rtl"] .nav-sidebar .menu-open > .nav-link i.right,
        html[dir="rtl"] .nav-sidebar .nav-link > p > i.right {
            transform: rotate(180deg);
        }

        html[dir="rtl"] .nav-sidebar .nav-treeview {
            padding-right: 1rem;
            padding-left: 0;
        }

        html[dir="rtl"] .dropdown-menu-right {
            right: auto !important;
            left: 0 !important;
        }

        html[dir="rtl"] .navbar-nav.ml-auto {
            margin-left: 0 !important;
            margin-right: auto !important;
        }

        html[dir="rtl"] .main-header .navbar-nav {
            flex-direction: row-reverse;
        }

        html[dir="rtl"] .breadcrumb,
        html[dir="rtl"] .pagination {
            flex-direction: row-reverse;
        }

        html[dir="rtl"] .table th,
        html[dir="rtl"] .table td {
            text-align: right;
        }

        html[dir="rtl"] .text-left {
            text-align: right !important;
        }

        html[dir="rtl"] .text-right {
            text-align: left !important;
        }

        html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: .25rem !important; }
        html[dir="rtl"] .mr-2 { margin-right: 0 !important; margin-left: .5rem !important; }
        html[dir="rtl"] .mr-3 { margin-right: 0 !important; margin-left: 1rem !important; }

        html[dir="rtl"] .ml-1 { margin-left: 0 !important; margin-right: .25rem !important; }
        html[dir="rtl"] .ml-2 { margin-left: 0 !important; margin-right: .5rem !important; }
        html[dir="rtl"] .ml-3 { margin-left: 0 !important; margin-right: 1rem !important; }

        html[dir="rtl"] .float-right {
            float: left !important;
        }

        html[dir="rtl"] .float-left {
            float: right !important;
        }

        html[dir="rtl"] .small-box .icon {
            right: auto;
            left: 15px;
        }

        html[dir="rtl"] .close,
        html[dir="rtl"] button.close {
            margin: 0 auto 0 0;
        }

        html[lang="ar"] .nav-header,
        html[lang="ar"] .small-box-footer,
        html[lang="ar"] .btn,
        html[lang="ar"] .card-title,
        html[lang="ar"] .table th {
            letter-spacing: 0;
            text-transform: none;
        }
    </style>

    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    {{-- Navbar --}}
    @include('dashboard.app.partials.navbar')

    {{-- Sidebar --}}
    @include('dashboard.app.partials.sidebar')

    {{-- Content --}}
    @yield('content')

    {{-- Footer --}}
    @include('dashboard.app.partials.footer')

</div>

{{-- jQuery --}}
<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>

{{-- jQuery UI --}}
<script src="{{ asset('AdminLTE/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

{{-- Bootstrap 4 --}}
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

{{-- overlayScrollbars --}}
<script src="{{ asset('AdminLTE/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

{{-- AdminLTE App --}}
<script src="{{ asset('AdminLTE/dist/js/adminlte.min.js') }}"></script>

{{-- Chart.js --}}
<script src="{{ asset('AdminLTE/plugins/chart.js/Chart.min.js') }}"></script>

{{-- Tempusdominus --}}
<script src="{{ asset('AdminLTE/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

{{-- Summernote --}}
<script src="{{ asset('AdminLTE/plugins/summernote/summernote-bs4.min.js') }}"></script>

{{-- Select2 --}}
<script src="{{ asset('AdminLTE/plugins/select2/js/select2.full.min.js') }}"></script>

@stack('scripts')

</body>
</html>
