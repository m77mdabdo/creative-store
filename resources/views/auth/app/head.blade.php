<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
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
  <link rel="stylesheet" href="{{ versioned_asset('css/site/auth-app-head.css') }}">

    <link rel="stylesheet" href="{{ versioned_asset('css/site/tokens.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/base.css') }}">
</head>
