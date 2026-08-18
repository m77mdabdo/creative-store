@extends('dashboard.app.layout')

@section('title', __('Edit Category'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <h1>{{ __('Edit Category') }}</h1>

            <div>
                <a href="{{ route('dashboard.categories.show', $category) }}" class="btn btn-info">
                    <i class="fas fa-eye mr-1"></i> {{ __('View Details') }}
                </a>

                <a href="{{ route('dashboard.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
                </a>
            </div>
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

            <form action="{{ route('dashboard.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                @include('dashboard.categories._form')
            </form>
        </div>
    </section>
</div>
@endsection
