@csrf

<div class="card-body">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>{{ __('Name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>{{ __('Email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>{{ __('Phone') }}</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}">
            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>{{ __('Roles') }}</label>
            <select name="roles[]" class="form-control" multiple>
                @foreach($roles as $role)
                    @php
                        $selectedRoleIds = old('roles', isset($user) ? $user->roles->pluck('id')->toArray() : []);
                    @endphp
                    <option value="{{ $role->id }}" @selected(in_array($role->id, $selectedRoleIds))>
                        {{ status_label($role->name) }}
                    </option>
                @endforeach
            </select>
            @error('roles') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>Password {{ isset($user) ? '(Leave blank to keep current password)' : '' }}</label>
            <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>{{ __('Confirm Password') }}</label>
            <input type="password" name="password_confirmation" class="form-control" {{ isset($user) ? '' : 'required' }}>
        </div>

        <div class="col-md-3 mb-3">
            <div class="form-check mt-4">
                <input type="hidden" name="email_verified" value="0">
                <input
                    type="checkbox"
                    name="email_verified"
                    value="1"
                    class="form-check-input"
                    id="email_verified"
                    @checked(old('email_verified', isset($user) && $user->email_verified_at ? 1 : 0))
                >
                <label for="email_verified" class="form-check-label">{{ __('Email Verified') }}</label>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="form-check mt-4">
                <input type="hidden" name="is_active" value="0">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    class="form-check-input"
                    id="is_active"
                    @checked(old('is_active', isset($user) ? (int) $user->is_active : 1))
                >
                <label for="is_active" class="form-check-label">{{ __('Active Account') }}</label>
            </div>
        </div>
    </div>

    <hr>

    <h5 class="mb-3">{{ __('Direct Permissions') }}</h5>

    @php
        $selectedAllowPermissions = old(
            'direct_permissions',
            isset($user)
                ? $user->permissions->filter(fn($permission) => ! $permission->pivot->is_denied)->pluck('code')->toArray()
                : []
        );

        $selectedDeniedPermissions = old(
            'denied_permissions',
            isset($user)
                ? $user->permissions->filter(fn($permission) => $permission->pivot->is_denied)->pluck('code')->toArray()
                : []
        );
    @endphp

    @foreach($permissionsGrouped as $group => $permissions)
        <div class="card card-outline card-secondary mb-3">
            <div class="card-header">
                <h3 class="card-title text-capitalize">{{ status_label($group) }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($permissions as $permission)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="border rounded p-2 h-100">
                                <div class="font-weight-bold">{{ __($permission->name) }}</div>
                                <small class="text-muted d-block mb-2">{{ $permission->code }}</small>

                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="direct_permissions[]"
                                        value="{{ $permission->code }}"
                                        id="allow_{{ $permission->id }}"
                                        @checked(in_array($permission->code, $selectedAllowPermissions, true))
                                    >
                                    <label class="form-check-label" for="allow_{{ $permission->id }}">
                                        {{ __('Allow') }}
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="denied_permissions[]"
                                        value="{{ $permission->code }}"
                                        id="deny_{{ $permission->id }}"
                                        @checked(in_array($permission->code, $selectedDeniedPermissions, true))
                                    >
                                    <label class="form-check-label text-danger" for="deny_{{ $permission->id }}">
                                        {{ __('Deny') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card-footer">
    <button class="btn btn-primary">{{ __('Save') }}</button>
    <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
</div>
