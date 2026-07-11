@extends('layouts.guest')

@section('content')
<div class="auth-card-header">
  <h1 class="auth-title">Welcome back</h1>
  <p class="auth-sub">Sign in to your account to continue managing your research</p>
</div>

<div class="auth-card-body">
  @if(session('error'))
    <div class="alert alert-error">
      <i class="bi bi-exclamation-circle-fill"></i>
      <span>{{ session('error') }}</span>
    </div>
  @endif
  
  @if(session('success'))
    <div class="alert alert-success">
      <i class="bi bi-check-circle-fill"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <form method="POST" action="{{ route('login.store') }}" novalidate>
    @csrf
    
    <div style="margin-bottom:20px;">
      <label class="lbl" for="email">
        Email Address 
        <span class="req">*</span>
      </label>
      <input 
        class="inp {{ $errors->has('email') ? 'is-invalid':'' }}"
        type="email" 
        id="email" 
        name="email"
        value="{{ old('email') }}" 
        placeholder="researcher@institution.ac.id"
        autofocus 
        required 
        autocomplete="email"
      />
      @error('email')
        <div class="f-err">
          <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
        </div>
      @enderror
    </div>
    
    <div style="margin-bottom:24px;">
      <label class="lbl" for="pwd">
        Password 
        <span class="req">*</span>
      </label>
      <div style="position:relative;">
        <input 
          class="inp {{ $errors->has('password') ? 'is-invalid':'' }}"
          type="password" 
          name="password" 
          id="pwd"
          placeholder="Enter your password" 
          required 
          autocomplete="current-password"
          style="padding-right:48px;"
        />
        <button 
          type="button" 
          onclick="togglePwd()" 
          aria-label="Toggle password visibility"
          style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#64748b;font-size:16px;padding:0;"
        >
          <i class="bi bi-eye" id="pwd-icon"></i>
        </button>
      </div>
      @error('password')
        <div class="f-err">
          <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
        </div>
      @enderror
    </div>
    
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
      <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b;cursor:pointer;">
        <input 
          type="checkbox" 
          name="remember" 
          style="width:16px;height:16px;accent-color:#2563eb;cursor:pointer;"
        />
        <span>Remember me</span>
      </label>
      <a href="#" style="font-size:13px;color:#2563eb;font-weight:500;text-decoration:none;">
        Forgot password?
      </a>
    </div>
    
    <button type="submit" class="btn-auth">
      <i class="bi bi-box-arrow-in-right"></i> 
      Sign In
    </button>

    {{-- SSO Login Options --}}
    @if(\App\Models\ApiIntegration::isEnabled('orcid') || \App\Models\ApiIntegration::isEnabled('google'))
    <div class="divider">Or continue with</div>
    
    <div style="display:flex;flex-direction:column;gap:10px;">
      @if(\App\Models\ApiIntegration::isEnabled('orcid'))
      <a href="{{ route('auth.orcid.redirect') }}" class="btn-sso">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 512 512">
          <circle cx="256" cy="256" r="256" fill="#A6CE39"/>
          <path fill="#FFF" d="M178.8 286.2h-21.3v-78.4h21.3v78.4zm-10.7-90.2c-7.3 0-13.2-5.9-13.2-13.2s5.9-13.2 13.2-13.2 13.2 5.9 13.2 13.2-5.9 13.2-13.2-13.2zm171.1 90.2h-38.6c-4.9 0-9-2.2-11.7-5.9-2.2 3.7-6.8 5.9-11.7 5.9H236v-78.4h42.1c16.1 0 26.2 9.3 26.2 21.6 0 9.2-5.5 15.6-13.8 18.2 10.3 2.1 16.7 9.3 16.7 20.3v10.7c0 4.1.8 5.7 3.3 5.7h18.7v7.7zm-90.1-70.7v24.6h17.9c10.2 0 15.6-4.5 15.6-12.3s-5.4-12.3-15.6-12.3h-17.9zm0 32.4v30.6h19.5c10.4 0 15.7-4.8 15.7-15.3 0-10.4-5.3-15.3-15.7-15.3h-19.5z"/>
        </svg>
        Sign in with ORCID
      </a>
      @endif

      @if(\App\Models\ApiIntegration::isEnabled('google'))
      <a href="{{ route('auth.google.redirect') }}" class="btn-sso">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
          <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
          <path fill="#FF3D00" d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
          <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
          <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
        </svg>
        Sign in with Google
      </a>
      @endif
    </div>
    @endif

    <p style="text-align:center;font-size:14px;color:#64748b;margin-top:24px;margin-bottom:0;">
      Don't have an account?
      <a href="{{ route('register') }}" style="color:#2563eb;font-weight:600;text-decoration:none;">
        Create account
      </a>
    </p>
  </form>

  {{-- Demo credentials box --}}
  <div class="demo-box">
    <div class="demo-title" style="display:flex;align-items:center;gap:6px;">
      <i class="bi bi-info-circle"></i> Demo Credentials
    </div>
    <div class="demo-row">
      <span style="color:#64748b;">Admin</span> 
      <span style="font-family:monospace;background:#f1f5f9;padding:2px 8px;border-radius:4px;">admin@ojs.id / password</span>
    </div>
    <div class="demo-row">
      <span style="color:#64748b;">Editor</span> 
      <span style="font-family:monospace;background:#f1f5f9;padding:2px 8px;border-radius:4px;">editor@ojs.id / password</span>
    </div>
    <div class="demo-row">
      <span style="color:#64748b;">Reviewer</span> 
      <span style="font-family:monospace;background:#f1f5f9;padding:2px 8px;border-radius:4px;">reviewer1@ojs.id / password</span>
    </div>
    <div class="demo-row">
      <span style="color:#64748b;">Author</span> 
      <span style="font-family:monospace;background:#f1f5f9;padding:2px 8px;border-radius:4px;">author@ojs.id / password</span>
    </div>
  </div>
</div>

<script>
function togglePwd(){
  const f = document.getElementById('pwd');
  const i = document.getElementById('pwd-icon');
  if(f.type === 'password'){
    f.type = 'text';
    i.className = 'bi bi-eye-slash';
  } else {
    f.type = 'password';
    i.className = 'bi bi-eye';
  }
}
</script>
@endsection
