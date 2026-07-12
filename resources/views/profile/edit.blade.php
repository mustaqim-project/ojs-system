@extends('layouts.dashboard')

@section('content')
<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <div class="ds-breadcrumb">
      <span>Account</span>
      <span class="ds-breadcrumb-sep">›</span>
      <span style="color:var(--text-main);">Profile Settings</span>
    </div>
    <h1 class="ds-page-title">Profile Settings</h1>
    <p class="ds-page-subtitle">Manage your personal information and security preferences.</p>
  </div>
</div>

<div class="row g-4 mb-5">
  <div class="col-12 col-xl-7" data-aos="fade-up" data-aos-delay="100">
    <div class="ds-card" style="margin-bottom: 0;">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Personal Information</span>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}">
          @csrf
          @method('PUT')

          <div class="mb-4">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-main); margin-bottom:8px;">Full Name</label>
            <input type="text" name="name" class="form-control" style="background:#f8fafc; border:1px solid var(--border); border-radius:12px; padding:12px 16px;" value="{{ old('name', $user->name) }}" required>
            @error('name')
              <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-main); margin-bottom:8px;">Email Address</label>
            <input type="email" name="email" class="form-control" style="background:#f8fafc; border:1px solid var(--border); border-radius:12px; padding:12px 16px;" value="{{ old('email', $user->email) }}" required>
            @error('email')
              <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="ds-btn ds-btn-pri">Save Changes</button>
          </div>
          
          @if (session('status') === 'profile-updated')
            <div class="mt-3 text-success" style="font-size:13px; text-align:right;">Profile updated successfully.</div>
          @endif
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-5" data-aos="fade-up" data-aos-delay="200">
    <div class="ds-card" style="margin-bottom: 0;">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Update Password</span>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('profile.password') }}">
          @csrf
          @method('PUT')

          <div class="mb-4">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-main); margin-bottom:8px;">Current Password</label>
            <input type="password" name="current_password" class="form-control" style="background:#f8fafc; border:1px solid var(--border); border-radius:12px; padding:12px 16px;" required>
            @error('current_password', 'updatePassword')
              <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-main); margin-bottom:8px;">New Password</label>
            <input type="password" name="password" class="form-control" style="background:#f8fafc; border:1px solid var(--border); border-radius:12px; padding:12px 16px;" required>
            @error('password', 'updatePassword')
              <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-main); margin-bottom:8px;">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" style="background:#f8fafc; border:1px solid var(--border); border-radius:12px; padding:12px 16px;" required>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="ds-btn ds-btn-out">Change Password</button>
          </div>

          @if (session('status') === 'password-updated')
            <div class="mt-3 text-success" style="font-size:13px; text-align:right;">Password updated successfully.</div>
          @endif
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
