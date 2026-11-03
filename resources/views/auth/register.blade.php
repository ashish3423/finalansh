@extends('layouts.auth')

@section('title', 'Create account')

@section('content')
<div class="account-page">
    <div class="account-brand mb-4"><span class="brand-mark">A</span> ANSH Enterprises</div>
    <div class="card account-card"><div class="card-body">
        <div class="eyebrow">OPERATIONS PORTAL</div><h1 class="mb-1">Create an account</h1><p class="text-body-secondary mb-4">Set up your user profile to access the portal.</p>
        @if ($errors->any()) <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="mb-3"><label for="name" class="form-label">Full name</label><input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="120" required autofocus></div>
            <div class="mb-3"><label for="email" class="form-label">Email address</label><input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></div>
            <div class="mb-3"><label for="password" class="form-label">Password</label><input id="password" class="form-control" type="password" name="password" autocomplete="new-password" minlength="8" required></div>
            <div class="mb-3"><label for="password_confirmation" class="form-label">Confirm password</label><input id="password_confirmation" class="form-control" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required></div>
            <div class="alert alert-info small py-2"><i class="bi bi-info-circle me-1"></i>New registrations receive the <strong>Admin</strong> role with read-only access.</div>
            <button class="btn btn-primary w-100 py-2" type="submit">Create account <i class="bi bi-arrow-right ms-1"></i></button>
        </form>
        <div class="text-center small text-body-secondary mt-4">Already have an account? <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Sign in</a></div>
    </div></div>
    <div class="text-center text-body-secondary small mt-3">© {{ date('Y') }} ANSH Enterprises</div>
</div>
@endsection
