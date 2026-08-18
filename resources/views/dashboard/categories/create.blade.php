@extends('dashboard.app.layout')

@section('title', __('Create Category'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1>{{ __('Create Category') }}</h1>

            <a href="{{ route('dashboard.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Categories') }}
            </a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dashboard.categories.store') }}" method="POST">
                @csrf
                @include('dashboard.categories._form')
            </form>
        </div>
    </section>
</div>
@endsection
