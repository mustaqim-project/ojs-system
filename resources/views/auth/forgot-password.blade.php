@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
      <div class="auth-card-header text-center mb-4">
        <h1 class="auth-title">Forgot Password</h1>
        <p class="auth-sub text-muted">Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.</p>
      </div>

      <div class="auth-card-body">
        @if(session('success'))
          <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
          </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" novalidate>
          @csrf

          <div class="mb-4">
            <label class="form-label" for="email">
              Email Address 
              <span class="text-danger">*</span>
            </label>
            <div class="position-relative">
              <span class="position-absolute top-50 translate-middle-y text-muted ms-3">
                <i class="bi bi-envelope"></i>
              </span>
              <input 
                class="form-control form-control-lg {{ $errors->has('email') ? 'is-invalid':'' }}"
                type="email" 
                id="email" 
                name="email"
                value="{{ old('email') }}" 
                placeholder="name@institution.ac.id"
                autofocus 
                required 
                style="padding-left: 2.75rem;"
              />
            </div>
            @error('email')
              <div class="invalid-feedback d-block">
                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
              </div>
            @enderror
          </div>
          
          <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
            <i class="bi bi-send me-2"></i> Email Password Reset Link
          </button>
          
          <p class="text-center text-muted mb-0 mt-4">
            Remember your password?
            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
              Sign in
            </a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
