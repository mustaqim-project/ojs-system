@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">

<div class="auth-card-header">
  <h1 class="auth-title">Selamat Datang Kembali</h1>
  <p class="auth-sub">Masuk ke akun Anda untuk melanjutkan mengelola penelitian Anda</p>
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
    
    <div class="mb-4">
      <label class="form-label" for="email">
        Alamat Email 
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
          placeholder="researcher@institution.ac.id"
          autofocus 
          required 
          autocomplete="email"
          style="padding-left: 2.75rem;"
        />
      </div>
      @error('email')
        <div class="invalid-feedback d-block">
          <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
        </div>
      @enderror
    </div>
    
    <div class="mb-4">
      <label class="form-label" for="pwd">
        Kata Sandi 
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
          id="pwd"
          placeholder="Masukkan kata sandi Anda" 
          required 
          autocomplete="current-password"
          style="padding-left: 2.75rem; padding-right: 2.75rem;"
        />
        <button 
          type="button" 
          onclick="togglePwd()" 
          aria-label="Toggle password visibility"
          class="btn btn-link text-muted p-0 position-absolute top-50 translate-middle-y end-0 me-3 text-decoration-none"
        >
          <i class="bi bi-eye" id="pwd-icon"></i>
        </button>
      </div>
      @error('password')
        <div class="invalid-feedback d-block">
          <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
        </div>
      @enderror
    </div>
    
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label text-muted" for="remember">
          Ingat saya
        </label>
      </div>
      <a href="{{ route('password.request') }}" class="text-decoration-none fw-semibold">
        Lupa kata sandi?
      </a>
    </div>
    
    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
      <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
    </button>
    
    <div class="text-center my-4">
      <hr class="divider-text" style="margin:0;">
      <span class="divider-label text-muted px-3" style="position:relative; top:-0.7em; background:white;">Atau lanjutkan dengan</span>
    </div>
    
    <div class="d-grid gap-2 mb-4">
      <a href="{{ route('auth.google.redirect') }}" class="btn btn-outline-dark btn-lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="me-2">
          <path fill="#4285F4" d="M23.766 12.2764c0-.8908-.0764-1.7563-.2292-2.5908H12v4.905h6.473a5.541 5.541 0 0 1-2.402 3.636v3.001h3.865c2.265-2.088 3.83-5.174 3.83-8.951z"/>
          <path fill="#34A853" d="M12 24c3.24 0 5.952-1.076 7.938-2.907l-3.865-3.001c-1.076.72-2.454 1.148-4.073 1.148-3.126 0-5.772-2.106-6.718-4.938H1.322v3.088C3.302 21.298 7.4 24 12 24z"/>
          <path fill="#FBBC05" d="M5.28 14.302a7.166 7.166 0 0 1 0-4.604V6.61C3.302 8.538 2.4 10.712 2.4 12s.902 3.462 2.88 5.39l3.865-3.001z"/>
          <path fill="#EA4335" d="M12 4.76c1.778 0 3.37.612 4.628 1.81l3.436-3.436C17.988 1.198 15.296 0 12 0 7.4 0 3.302 2.702 1.322 6.61l3.958 3.088C6.226 6.866 8.872 4.76 12 4.76z"/>
        </svg>
        Masuk dengan Google
      </a>

      @if(\App\Models\ApiIntegration::isEnabled('orcid'))
      <a href="{{ route('auth.orcid.redirect') }}" class="btn btn-outline-dark btn-lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="me-2" style="fill: #A6CE39;">
          <path d="M512 256c0 141.4-114.6 256-256 256S0 397.4 0 256 114.6 0 256 0s256 114.6 256 256z"/>
          <path fill="#FFF" d="M178.8 286.2h-21.3v-78.4h21.3v78.4zm-10.7-90.2c-7.3 0-13.2-5.9-13.2-13.2s5.9-13.2 13.2-13.2 13.2 5.9 13.2 13.2-5.9 13.2-13.2-13.2zm171.1 90.2h-38.6c-4.9 0-9-2.2-11.7-5.9-2.2 3.7-6.8 5.9-11.7 5.9H236v-78.4h42.1c16.1 0 26.2 9.3 26.2 21.6 0 9.2-5.5 15.6-13.8 18.2 10.3 2.1 16.7 9.3 16.7 20.3v10.7c0 4.1.8 5.7 3.3 5.7h18.7v7.7zm-90.1-70.7v24.6h17.9c10.2 0 15.6-4.5 15.6-12.3s-5.4-12.3-15.6-12.3h-17.9zm0 32.4v30.6h19.5c10.4 0 15.7-4.8 15.7-15.3 0-10.4-5.3-15.3-15.7-15.3h-19.5z"/>
        </svg>
        Masuk dengan ORCID
      </a>
      @endif
    </div>

    <p class="text-center text-muted mb-0 mt-4">
      Belum punya akun?
      <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">
        Buat akun
      </a>
    </p>
  </form>

</div>

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

