@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
<div class="account-page">
    <div class="account-brand mb-4"><span class="brand-mark">A</span> ANSH Enterprises</div>
    <div class="card account-card"><div class="card-body">
        <div class="eyebrow">OPERATIONS PORTAL</div><h1 class="mb-1">Welcome back</h1><p class="text-body-secondary mb-4">Sign in to manage your contracts and services.</p>
        @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
        <form id="loginForm" method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="mb-3"><label for="email" class="form-label">Email address</label><input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label for="password" class="form-label">Password</label><div class="input-group"><input id="password" class="form-control" type="password" name="password" autocomplete="current-password" required><button class="btn btn-light border" type="button" data-toggle-password="password" aria-label="Show password"><i class="bi bi-eye"></i></button></div></div>
            <div class="form-check mb-4"><input class="form-check-input" type="checkbox" id="remember" name="remember" value="1"><label class="form-check-label small" for="remember">Keep me signed in</label></div>
            <button class="btn btn-primary w-100 py-2" type="submit">Sign in <i class="bi bi-arrow-right ms-1"></i></button>
        </form>
        <div class="text-center small text-body-secondary mt-4">New to the portal? <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Create an account</a></div>
    </div></div>
    <div class="text-center text-body-secondary small mt-3">© {{ date('Y') }} ANSH Enterprises</div>
</div>
@endsection
