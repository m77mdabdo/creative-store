@extends('dashboard.app.layout')

@section('title', __('Create Blog Category'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1>{{ __('Create Blog Category') }}</h1>
                <a href="{{ route('dashboard.blog-categories.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <form action="{{ route('dashboard.blog-categories.store') }}" method="POST">
                @include('dashboard.blog-categories._form', [
                    'formTitle' => __('Create New Category'),
                    'submitLabel' => 'Create Category',
                ])
            </form>
        </div>
    </section>
</div>
@endsection
