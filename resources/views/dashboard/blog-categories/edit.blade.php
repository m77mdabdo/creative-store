@extends('dashboard.app.layout')

@section('title', 'Edit Blog Category')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Edit Blog Category</h1>
                <a href="{{ route('dashboard.blog-categories.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('dashboard.partials.alerts')

            <form action="{{ route('dashboard.blog-categories.update', $category) }}" method="POST">
                @method('PUT')

                @include('dashboard.blog-categories._form', [
                    'category' => $category,
                    'formTitle' => 'Update Category',
                    'submitLabel' => 'Update Category',
                ])
            </form>
        </div>
    </section>
</div>
@endsection
