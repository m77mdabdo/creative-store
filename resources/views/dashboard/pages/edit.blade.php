@extends('dashboard.app.layout')

@section('title', 'Edit Page')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Edit Page') }}</h1>
            <a href="{{ route('dashboard.pages.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <form action="{{ route('dashboard.pages.update', $page) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('dashboard.pages._form', [
                    'page' => $page,
                    'translationAr' => $translationAr,
                    'translationEn' => $translationEn,
                    'aboutAr' => $aboutAr ?? [],
                    'aboutEn' => $aboutEn ?? [],
                    'formTitle' => __('Update Page'),
                    'submitLabel' => 'Update Page',
                ])
            </form>
        </div>
    </section>
</div>
@endsection
