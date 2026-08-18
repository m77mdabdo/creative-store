@extends('dashboard.app.layout')

@section('title', __('Create Blog'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Create Blog') }}</h1>
            <a href="{{ route('dashboard.blogs.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <form action="{{ route('dashboard.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @include('dashboard.blogs._form', [
                    'formTitle' => __('Create New Blog'),
                    'submitLabel' => 'Create Blog',
                ])
            </form>
        </div>
    </section>
</div>
@endsection
