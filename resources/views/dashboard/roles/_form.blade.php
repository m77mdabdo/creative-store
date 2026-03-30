@csrf
@if(isset($role))
    @method('PUT')
@endif

<div class="card-body">
    <div class="row">
        <div class="col-md-4 mb-3">
            <label>Role Name</label>
            <input
                type="text"
                name="name"
                class="form-control"
                value="{{ old('name', $role->name ?? '') }}"
                required
            >
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <hr>

    <h5 class="mb-3">Assign Permissions</h5>

    @php
        $selectedPermissions = old(
            'permissions',
            isset($role) ? $role->permissions->pluck('code')->toArray() : []
        );
    @endphp

    @foreach($permissionsGrouped as $group => $permissions)
        <div class="card card-outline card-secondary mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title text-capitalize">{{ $group }}</h3>
                <button type="button" class="btn btn-xs btn-outline-primary toggle-group" data-group="{{ \Illuminate\Support\Str::slug($group) }}">
                    Toggle
                </button>
            </div>
            <div class="card-body">
                <div class="row permission-group" data-group="{{ \Illuminate\Support\Str::slug($group) }}">
                    @foreach($permissions as $permission)
                        <div class="col-md-6 col-lg-4 mb-2">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->code }}"
                                    id="permission_{{ $permission->id }}"
                                    @checked(in_array($permission->code, $selectedPermissions, true))
                                >
                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                    <strong>{{ $permission->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $permission->code }}</small>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('permissions') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
    @endforeach
</div>

<div class="card-footer">
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('dashboard.roles.index') }}" class="btn btn-secondary">Cancel</a>
</div>


