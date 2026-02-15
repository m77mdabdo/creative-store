@extends('myAccount.layout')
@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mb-0">My Account</h3>
            {{-- <span class="text-muted">Signed in as: <strong>{{ $user->email }}</strong></span> --}}
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header bg-white">
                        <strong>Account Details</strong>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Full Name:</strong> {{ $user->name }}</p>
                        <p class="mb-2"><strong>Email:</strong> {{ $user->email }}</p>
                        <p class="mb-0"><strong>Member Since:</strong> {{ $user->created_at?->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <strong>Update Name</strong>
                    </div>
                    <div class="card-body">

                        @if(session('success_profile'))
                            <div class="alert alert-success">{{ session('success_profile') }}</div>
                        @endif

                        <form action="{{ route('myaccount.profile') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="form-control @error('name') is-invalid @enderror">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <button class="btn btn-dark">
                                <i class="fas fa-save mr-1"></i> Save
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <strong>Change Password</strong>
                    </div>
                    <div class="card-body">

                        @if(session('success_password'))
                            <div class="alert alert-success">{{ session('success_password') }}</div>
                        @endif

                        <form action="{{ route('myaccount.password') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <button class="btn btn-dark">
                                <i class="fas fa-key mr-1"></i> Update Password
                            </button>
                        </form>

                        <div class="mt-3">
                            <a href="{{ route('forgot-password') }}" class="d-block">
                                I forgot my password
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
