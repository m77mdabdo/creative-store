@extends('dashboard.app.layout')

@section('title', __('Create Role'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ __('Create Role') }}</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dashboard.roles.store') }}" method="POST">
                <div class="card card-primary">
                    @include('dashboard.roles._form')
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-group').forEach(button => {
    button.addEventListener('click', function () {
        const group = this.dataset.group;
        document.querySelectorAll(`.permission-group[data-group="${group}"] input[type="checkbox"]`)
            .forEach(checkbox => checkbox.checked = !checkbox.checked);
    });
});
</script>
@endpush
