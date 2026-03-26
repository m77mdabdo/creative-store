@extends('dashboard.app.layout')

@section('title', 'Edit User')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit User</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
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
