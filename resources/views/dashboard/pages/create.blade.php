@extends('dashboard.app.layout')

@section('title', 'Create Page')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Create Page</h1>
            <a href="{{ route('dashboard.pages.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <form action="{{ route('dashboard.pages.store') }}" method="POST">
                @include('dashboard.pages._form', [
                    'formTitle' => 'Create New Page',
                    'submitLabel' => 'Create Page',
                ])
            </form>
        </div>
    </section>
</div>
@endsection
