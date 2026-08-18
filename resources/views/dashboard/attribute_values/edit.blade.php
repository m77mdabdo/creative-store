@extends('dashboard.app.layout')

@section('title', __('Edit Attribute Value'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Edit Value - {{ $attribute->name }}</h1>

            <a href="{{ route('dashboard.attributes.show', $attribute) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')

            <form action="{{ route('dashboard.attributes.values.update', [$attribute, $value]) }}" method="POST">
                @csrf
                @method('PUT')
                @include('dashboard.attribute_values._form')
            </form>
        </div>
    </section>
</div>
@endsection

