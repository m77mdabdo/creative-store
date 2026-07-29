@extends('dashboard.app.layout')

@section('title', 'Edit Product')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Edit Product') }}</h1>

            <a href="{{ route('dashboard.products.show', $product) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <form action="{{ route('dashboard.products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')
                @include('dashboard.products._form')
            </form>
        </div>
    </section>
</div>
@endsection
