@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Create Shipment') }}</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <form action="{{ route('dashboard.shipments.store') }}" method="POST">
                        @include('dashboard.shipments._form')
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

