@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

<div class="auth-card-header">
  <div class="text-center mb-4">
    <div class="brand-logo mb-3">
      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        <path d="M12 8l2 2-2 2"/>
        <path d="M14 10H8"/>
      </svg>
    </div>
    <h1 class="auth-title">Create your account</h1>
    <p class="auth-sub">Join the scholarly publishing platform and start your research journey</p>
  </div>
</div>

<div class="auth-card-body">
  <!-- Info Card -->
  <div class="alert alert-info mb-4" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    <div>
      <strong>Why register?</strong>
      <ul class="mb-0 mt-2" style="padding-left: 1.2rem; font-size: 0.875rem;">
        <li>Submit manuscripts to journals</li>
        <li>Track your submission status</li>
        <li>Access reviewer assignments</li>
        <li>Manage your academic profile</li>
      </ul>
    </div>
  </div>

  <form method="POST" action="{{ route('register.store') }}" novalidate>
    @csrf
    
    <!-- Full Name -->
    <div class="mb-4">
      <label class="form-label" for="name">
        Full Name
        <span class="text-danger">*</span>
      </label>
      <div class="position-relative">
        <span class="position-absolute top-50 translate-middle-y text-muted ms-3">
          <i class="bi bi-person"></i>
        </span>
        <input class="form-control form-control-lg {{ $errors->has('name') ? 'is-invalid' : '' }}"
               type="text" id="name" name="name"
               value="{{ old('name') }}" 
               placeholder="Dr. John Doe" 
               autofocus 
               required
               aria-describedby="nameHelp"
               style="padding-left: 2.75rem;"/>
      </div>
      @error('name')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
      <small id="nameHelp" class="form-text text-muted">Include your title (e.g., Dr., Prof.)</small>
    </div>

    <!-- Email -->
    <div class="mb-4">
      <label class="form-label" for="email">
        Institutional Email
        <span class="text-danger">*</span>
      </label>
      <div class="position-relative">
        <span class="position-absolute top-50 translate-middle-y text-muted ms-3">
          <i class="bi bi-envelope"></i>
        </span>
        <input class="form-control form-control-lg {{ $errors->has('email') ? 'is-invalid' : '' }}"
               type="email" id="email" name="email"
               value="{{ old('email') }}" 
               placeholder="name@institution.ac.id" 
               required
               aria-describedby="emailHelp"
               style="padding-left: 2.75rem;"/>
      </div>
      @error('email')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
      <small id="emailHelp" class="form-text text-muted">Use your official institutional email address</small>
    </div>

    <!-- Password Fields -->
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <label class="form-label" for="password">
          Password
          <span class="text-danger">*</span>
        </label>
        <div class="position-relative">
          <span class="position-absolute top-50 translate-middle-y text-muted ms-3">
            <i class="bi bi-lock"></i>
          </span>
          <input class="form-control form-control-lg {{ $errors->has('password') ? 'is-invalid' : '' }}"
                 type="password" id="password" name="password"
                 placeholder="Min. 8 characters" 
                 required
                 aria-describedby="passwordHelp"
                 style="padding-left: 2.75rem; padding-right: 2.75rem;"
                 data-toggle-password/>
          <button type="button" class="btn btn-link text-muted p-0 position-absolute top-50 translate-middle-y end-0 me-3 text-decoration-none btn-password-toggle" aria-label="Toggle password visibility">
            <i class="bi bi-eye"></i>
          </button>
        </div>
        @error('password')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small id="passwordHelp" class="form-text text-muted">Minimum 8 characters</small>
      </div>
      
      <div class="col-md-6">
        <label class="form-label" for="password_confirmation">
          Confirm Password
          <span class="text-danger">*</span>
        </label>
        <div class="position-relative">
          <span class="position-absolute top-50 translate-middle-y text-muted ms-3">
            <i class="bi bi-lock-fill"></i>
          </span>
          <input class="form-control form-control-lg"
                 type="password" id="password_confirmation"
                 name="password_confirmation" 
                 placeholder="Repeat password" 
                 required
                 aria-label="Confirm password"
                 style="padding-left: 2.75rem;"/>
        </div>
      </div>
    </div>

    <!-- Password Requirements -->
    <div class="password-requirements mb-4 p-3 bg-light rounded-3">
      <small class="text-muted fw-semibold d-block mb-2">Password must contain:</small>
      <ul class="mb-0" style="font-size: 0.75rem; padding-left: 1rem;">
        <li class="text-muted">At least 8 characters</li>
        <li class="text-muted">Mix of uppercase and lowercase letters</li>
        <li class="text-muted">At least one number</li>
      </ul>
    </div>

    <!-- Affiliation -->
    <div class="mb-4">
      <label class="form-label" for="affiliation">
        Institution / Affiliation
        <span class="text-muted">(Optional)</span>
      </label>
      <div class="position-relative">
        <span class="position-absolute top-50 translate-middle-y text-muted ms-3">
          <i class="bi bi-building"></i>
        </span>
        <input class="form-control form-control-lg"
               type="text" id="affiliation" name="affiliation"
               value="{{ old('affiliation') }}" 
               placeholder="University / Research Institute"
               style="padding-left: 2.75rem;"/>
      </div>
      <small class="form-text text-muted">Your academic or research institution</small>
    </div>

    <!-- ORCID ID -->
    <div class="mb-4">
      <label class="form-label" for="orcid">
        ORCID iD
        <span class="text-muted">(Optional)</span>
      </label>
      <div class="position-relative">
        <span class="position-absolute top-50 translate-middle-y text-muted ms-3">
          <i class="bi bi-upc-scan"></i>
        </span>
        <input class="form-control form-control-lg"
               type="text" id="orcid" name="orcid"
               value="{{ old('orcid') }}" 
               placeholder="0000-0000-0000-0000"
               pattern="\d{4}-\d{4}-\d{4}-\d{3}[\dX]"
               style="padding-left: 2.75rem;"/>
      </div>
      <small class="form-text text-muted">
        <a href="https://orcid.org" target="_blank" class="text-decoration-none">Get your ORCID iD</a> - A persistent digital identifier for researchers
      </small>
    </div>

    <!-- Country -->
    <div class="mb-4">
      <label class="form-label" for="country">
        Country
        <span class="text-muted">(Optional)</span>
      </label>
      <div class="position-relative">
        <span class="position-absolute top-50 translate-middle-y text-muted ms-3">
          <i class="bi bi-globe"></i>
        </span>
        <select class="form-select form-select-lg" id="country" name="country" style="padding-left: 2.75rem;">
          <option value="">Select country</option>
          <option value="ID" {{ old('country') == 'ID' ? 'selected' : '' }}>Indonesia</option>
          <option value="MY" {{ old('country') == 'MY' ? 'selected' : '' }}>Malaysia</option>
          <option value="SG" {{ old('country') == 'SG' ? 'selected' : '' }}>Singapore</option>
          <option value="TH" {{ old('country') == 'TH' ? 'selected' : '' }}>Thailand</option>
          <option value="PH" {{ old('country') == 'PH' ? 'selected' : '' }}>Philippines</option>
          <option value="VN" {{ old('country') == 'VN' ? 'selected' : '' }}>Vietnam</option>
          <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>United States</option>
          <option value="GB" {{ old('country') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
          <option value="AU" {{ old('country') == 'AU' ? 'selected' : '' }}>Australia</option>
        </select>
      </div>
    </div>

    <!-- Terms & Privacy -->
    <div class="mb-4">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="terms" name="terms" required />
        <label class="form-check-label" for="terms">
          I agree to the 
          <a href="{{ route('public.terms-conditions') }}" target="_blank" class="text-decoration-none">Terms of Service</a> and 
          <a href="{{ route('public.privacy-policy') }}" target="_blank" class="text-decoration-none">Privacy Policy</a>
          <span class="text-danger">*</span>
        </label>
      </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
      <i class="bi bi-person-plus me-2"></i> Create Account
    </button>

    <!-- Divider -->
    <div class="text-center my-4">
      <hr class="divider-text" style="margin:0;">
      <span class="divider-label text-muted px-3" style="position:relative; top:-0.7em; background:white;">Or register with</span>
    </div>
    
    <!-- SSO Buttons -->
    <div class="d-grid gap-2 mb-4">
      @if(\App\Models\ApiIntegration::isEnabled('orcid'))
      <a href="{{ route('auth.orcid.redirect') }}" class="btn btn-outline-dark btn-lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="me-2" style="fill: #A6CE39;">
          <path d="M512 256c0 141.4-114.6 256-256 256S0 397.4 0 256 114.6 0 256 0s256 114.6 256 256z"/>
          <path fill="#FFF" d="M178.8 286.2h-21.3v-78.4h21.3v78.4zm-10.7-90.2c-7.3 0-13.2-5.9-13.2-13.2s5.9-13.2 13.2-13.2 13.2 5.9 13.2 13.2-5.9 13.2-13.2-13.2zm171.1 90.2h-38.6c-4.9 0-9-2.2-11.7-5.9-2.2 3.7-6.8 5.9-11.7 5.9H236v-78.4h42.1c16.1 0 26.2 9.3 26.2 21.6 0 9.2-5.5 15.6-13.8 18.2 10.3 2.1 16.7 9.3 16.7 20.3v10.7c0 4.1.8 5.7 3.3 5.7h18.7v7.7zm-90.1-70.7v24.6h17.9c10.2 0 15.6-4.5 15.6-12.3s-5.4-12.3-15.6-12.3h-17.9zm0 32.4v30.6h19.5c10.4 0 15.7-4.8 15.7-15.3 0-10.4-5.3-15.3-15.7-15.3h-19.5z"/>
        </svg>
        Register with ORCID
      </a>
      @endif

      <a href="{{ route('auth.google') }}" class="btn btn-outline-dark btn-lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="me-2">
          <path fill="#4285F4" d="M23.766 12.2764c0-.8908-.0764-1.7563-.2292-2.5908H12v4.905h6.473a5.541 5.541 0 0 1-2.402 3.636v3.001h3.865c2.265-2.088 3.83-5.174 3.83-8.951z"/>
          <path fill="#34A853" d="M12 24c3.24 0 5.952-1.076 7.938-2.907l-3.865-3.001c-1.076.72-2.454 1.148-4.073 1.148-3.126 0-5.772-2.106-6.718-4.938H1.322v3.088C3.302 21.298 7.4 24 12 24z"/>
          <path fill="#FBBC05" d="M5.28 14.302a7.166 7.166 0 0 1 0-4.604V6.61C3.302 8.538 2.4 10.712 2.4 12s.902 3.462 2.88 5.39l3.865-3.001z"/>
          <path fill="#EA4335" d="M12 4.76c1.778 0 3.37.612 4.628 1.81l3.436-3.436C17.988 1.198 15.296 0 12 0 7.4 0 3.302 2.702 1.322 6.61l3.958 3.088C6.226 6.866 8.872 4.76 12 4.76z"/>
        </svg>
        Register with Google <span class="text-muted fw-normal">(Author)</span>
      </a>
    </div>

    <!-- Sign In Link -->
    <p class="text-center text-muted mb-0">
      Already have an account?
      <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Sign in</a>
    </p>
  </form>
</div>

    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Password visibility toggle
  const toggleButtons = document.querySelectorAll('[data-toggle-password]');
  toggleButtons.forEach(input => {
    const container = input.closest('.position-relative');
    const toggleBtn = container.querySelector('.btn-password-toggle');
    
    if (toggleBtn) {
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
  
  // Form validation
  const form = document.querySelector('form');
  form.addEventListener('submit', function(e) {
    const termsCheckbox = document.getElementById('terms');
    if (!termsCheckbox.checked) {
      e.preventDefault();
      alert('Please agree to the Terms of Service and Privacy Policy');
      termsCheckbox.focus();
    }
  });
});
</script>
@endpush


@endsection

