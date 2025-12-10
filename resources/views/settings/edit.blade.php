@extends('layouts.app')
@section('title')
    Settings
@endsection

@section('content')
    <div class="container">
        <div class="bg-white mb-4 shadow-sm p-3 rounded">
            <h2 class="m-0">Settings</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8 col-xl-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Profile & Security</h5>
                        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Avatar</label>
                                <input type="file" name="avatar" id="avatar" class="form-control">
                                @if($user->avatar)
                                    <small class="text-muted d-block mt-1">Current: <a href="{{ Storage::url($user->avatar) }}" target="_blank">View</a></small>
                                @endif
                                @error('avatar')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                                <small class="text-muted">Leave blank to keep current password.</small>
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<div>
    <!-- He who is contented is rich. - Laozi -->
</div>
