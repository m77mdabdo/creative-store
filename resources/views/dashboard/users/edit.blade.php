@extends('dashboard.app.layout')

@section('title', 'Edit User')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ __('Edit User') }}</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dashboard.users.update', $user) }}" method="POST">
                @method('PUT')
                <div class="card card-primary">
                    @include('dashboard.users._form')
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
