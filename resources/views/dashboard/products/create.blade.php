@extends('dashboard.app.layout')

@section('title', __('Create Product'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ __('Create Product') }}</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <form action="{{ route('dashboard.products.store') }}" method="POST">
                @csrf
                @include('dashboard.products._form')
            </form>
        </div>
    </section>
</div>
@endsection
