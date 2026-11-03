@extends('layouts.newapp')

@section('title', 'My Profile | ANSH Enterprises')
@section('page-title', 'My Profile')

@section('content')
<main class="employee-management-page enterprise-page">
    <div class="page-heading mb-4"><div><div class="eyebrow">ACCOUNT SETTINGS</div><h3 class="mb-1">My profile</h3><p class="text-muted mb-0">Manage your sign-in details and personal information.</p></div></div>
    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
    <div class="row g-4">
        <div class="col-12 col-xl-4"><section class="card records-card h-100"><div class="card-body text-center py-5"><span class="profile-avatar mb-3"><i class="bi bi-person-fill"></i></span><h5 class="mb-1">{{ $user->name }}</h5><div class="text-muted small mb-3">{{ $user->email }}</div><span class="user-role-badge {{ $user->role === 'admin' ? 'admin' : '' }}">{{ $user->role === 'super-admin' ? 'Super admin · full access' : 'Admin · read only' }}</span><div class="text-muted small mt-4">Member since {{ $user->created_at->format('d M Y') }}</div></div></section></div>
        <div class="col-12 col-xl-8"><section class="card records-card"><div class="card-header"><h5 class="mb-1">Profile details</h5><div class="text-muted small">Update your name, email, or password.</div></div><div class="card-body p-4">
            <form method="POST" action="{{ route('profile.update') }}">@csrf @method('PUT')
                <div class="row g-3"><div class="col-md-6"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="120" required></div><div class="col-md-6"><label class="form-label" for="email">Email address</label><input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required></div></div>
                <hr class="my-4"><h6 class="fw-bold">Change password</h6><p class="text-muted small">Leave these blank to keep your current password.</p>
                <div class="row g-3"><div class="col-md-6"><label class="form-label" for="password">New password</label><input class="form-control" type="password" id="password" name="password" autocomplete="new-password" minlength="8"></div><div class="col-md-6"><label class="form-label" for="password_confirmation">Confirm new password</label><input class="form-control" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" minlength="8"></div></div>
                <div class="mt-4"><button class="btn btn-primary" type="submit"><i class="bi bi-check2 me-1"></i>Save profile</button></div>
            </form>
        </div></section></div>
    </div>
</main>
@endsection
