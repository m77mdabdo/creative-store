@extends('dashboard.app.layout')

@section('title', 'Create User')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Create User</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('dashboard.users.store') }}" method="POST">
                <div class="card card-primary">
                    @include('dashboard.users._form')
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
