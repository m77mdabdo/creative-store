@extends('dashboard.app.layout')

@section('title', 'Edit Attribute')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Edit Attribute</h1>

            <a href="{{ route('dashboard.attributes.show', $attribute) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <form action="{{ route('dashboard.attributes.update', $attribute) }}" method="POST">
                @csrf
                @method('PUT')
                @include('dashboard.attributes._form')
            </form>
        </div>
    </section>
</div>
@endsection
