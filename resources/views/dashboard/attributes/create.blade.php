@extends('dashboard.app.layout')

@section('title', 'Create Attribute')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Create Attribute') }}</h1>

            <a href="{{ route('dashboard.attributes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <form action="{{ route('dashboard.attributes.store') }}" method="POST">
                @csrf
                @include('dashboard.attributes._form')
            </form>
        </div>
    </section>
</div>
@endsection
