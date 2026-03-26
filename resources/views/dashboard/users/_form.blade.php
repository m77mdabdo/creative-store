@csrf

<div class="card-body">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}">
            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>Roles</label>
            <select name="roles[]" class="form-control" multiple>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        @selected(in_array($role->id, old('roles', isset($user) ? $user->roles->pluck('id')->toArray() : [])))>
                        {{ $role->name }}
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
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" {{ isset($user) ? '' : 'required' }}>
        </div>

        <div class="col-md-6 mb-3">
            <div class="form-check mt-4">
                <input type="checkbox" name="email_verified" value="1" class="form-check-input" id="email_verified"
                    @checked(old('email_verified', isset($user) && $user->email_verified_at ? 1 : 0))>
                <label for="email_verified" class="form-check-label">Email Verified</label>
            </div>
        </div>
    </div>
</div>

<div class="card-footer">
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary">Cancel</a>
</div>
