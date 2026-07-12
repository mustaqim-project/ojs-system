@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
      <div class="auth-card-header text-center mb-4">
        <h1 class="auth-title">Reset Password</h1>
        <p class="auth-sub text-muted">Enter your new password below.</p>
      </div>

      <div class="auth-card-body">
        <form method="POST" action="{{ route('password.update') }}" novalidate>
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

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
                value="{{ old('email', $request->email) }}" 
                required 
                style="padding-left: 2.75rem;"
                readonly
              />
            </div>
            @error('email')
              <div class="invalid-feedback d-block">
                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
              </div>
            @enderror
          </div>
          
          <div class="mb-4">
            <label class="form-label" for="password">
              New Password 
              <span class="text-danger">*</span>
            </label>
            <div class="position-relative">
              <span class="position-absolute top-50 translate-middle-y text-muted ms-3">
                <i class="bi bi-lock"></i>
              </span>
              <input 
                class="form-control form-control-lg {{ $errors->has('password') ? 'is-invalid':'' }}"
                type="password" 
                name="password" 
                id="password"
                required 
                style="padding-left: 2.75rem; padding-right: 2.75rem;"
                data-toggle-password
              />
              <button type="button" class="btn btn-link text-muted p-0 position-absolute top-50 translate-middle-y end-0 me-3 text-decoration-none btn-password-toggle" aria-label="Toggle password visibility">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            @error('password')
              <div class="invalid-feedback d-block">
                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
              </div>
            @enderror
          </div>
          
          <div class="mb-4">
            <label class="form-label" for="password_confirmation">
              Confirm Password 
              <span class="text-danger">*</span>
            </label>
            <div class="position-relative">
              <span class="position-absolute top-50 translate-middle-y text-muted ms-3">
                <i class="bi bi-lock-fill"></i>
              </span>
              <input 
                class="form-control form-control-lg"
                type="password" 
                name="password_confirmation" 
                id="password_confirmation"
                required 
                style="padding-left: 2.75rem;"
              />
            </div>
          </div>
          
          <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
            <i class="bi bi-arrow-repeat me-2"></i> Reset Password
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggleBtn = document.querySelector('.btn-password-toggle');
  if (toggleBtn) {
    const input = document.getElementById('password');
    toggleBtn.addEventListener('click', function() {
      const icon = this.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  }
});
</script>
@endpush
