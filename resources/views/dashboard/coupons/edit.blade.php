@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Edit Coupon') }}</h1>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('dashboard.coupons.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Coupon Form') }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.coupons.update', $coupon) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('dashboard.coupons._form')
                        <button type="submit" class="btn btn-primary">{{ __('Update Coupon') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 
