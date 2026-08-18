@extends('dashboard.app.layout')

@section('title', __('Create Page'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Create Page') }}</h1>
            <a href="{{ route('dashboard.pages.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <form action="{{ route('dashboard.pages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @include('dashboard.pages._form', [
                    'formTitle' => __('Create New Page'),
                    'submitLabel' => 'Create Page',
                    'page' => null,
                    'translationAr' => null,
                    'translationEn' => null,
                    'aboutAr' => [],
                    'aboutEn' => [],
                ])
            </form>
        </div>
    </section>
</div>
@endsection
