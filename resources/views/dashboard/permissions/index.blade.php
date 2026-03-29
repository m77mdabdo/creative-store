@extends('dashboard.app.layout')

@section('title', 'Permissions')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Permissions</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_permissions'] }}</h3>
                            <p>Total Permissions</p>
                        </div>
                        <div class="icon"><i class="fas fa-key"></i></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['groups_count'] }}</h3>
                            <p>Permission Groups</p>
                        </div>
                        <div class="icon"><i class="fas fa-layer-group"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.permissions.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="q" class="form-control" placeholder="Search permissions..." value="{{ $search }}">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary btn-block">Search</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('dashboard.permissions.index') }}" class="btn btn-light btn-block">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @foreach($groupedPermissions as $group => $permissions)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-capitalize">{{ $group }}</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Roles Count</th>
                                    <th>Direct Users Count</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td><code>{{ $permission->code }}</code></td>
                                        <td>{{ $permission->roles_count }}</td>
                                        <td>{{ $permission->users_count }}</td>
                                        <td>
                                            <a href="{{ route('dashboard.permissions.show', $permission) }}" class="btn btn-sm btn-info">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

        </div>
    </section>
</div>
@endsection
