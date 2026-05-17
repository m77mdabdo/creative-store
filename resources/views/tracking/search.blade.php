<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order</title>
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/tokens.css') }}">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
    <div class="content-wrapper" style="min-height: 100vh;">
        <div class="content-header">
            <div class="container">
                <div class="row mb-2 pt-4">
                    <div class="col-sm-12 text-center">
                        <h1 class="m-0">Track Your Order</h1>
                        <p class="text-muted mt-2">Enter tracking number, or order number with phone number.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Tracking Search</h3>
                            </div>

                            <form method="POST" action="{{ route('tracking.track') }}">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Tracking Number</label>
                                        <input
                                            type="text"
                                            name="tracking_number"
                                            value="{{ old('tracking_number') }}"
                                            class="form-control"
                                            placeholder="Enter tracking number"
                                        >
                                        <small class="text-muted">Use this if you already have a tracking number.</small>
                                    </div>

                                    <div class="text-center my-3">
                                        <strong>OR</strong>
                                    </div>

                                    <div class="form-group">
                                        <label>Order Number</label>
                                        <input
                                            type="text"
                                            name="order_number"
                                            value="{{ old('order_number') }}"
                                            class="form-control"
                                            placeholder="Enter order number"
                                        >
                                    </div>

                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input
                                            type="text"
                                            name="phone"
                                            value="{{ old('phone') }}"
                                            class="form-control"
                                            placeholder="Enter phone number"
                                        >
                                        <small class="text-muted">Use the same phone number used in the order.</small>
                                    </div>
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i> Track Shipment
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('AdminLTE/dist/js/adminlte.min.js') }}"></script>
</body>
</html>
