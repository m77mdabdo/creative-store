@extends('dashboard.app.layout')

@section('title', 'Create Category')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Create Category</h1>
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
