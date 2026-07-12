{{-- admin/users/edit.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Users','href'=>route('admin.users.index')],['label'=>'Edit: '.$user->name]]"/>
    <h1 class="ds-page-title">Edit User</h1>
    <p class="ds-page-subtitle">Updating: {{ $user->name }} &middot; {{ $user->email }}</p>
  </div>
</div>

<div style="max-width:620px;">
  <form method="POST" action="{{ route('admin.users.update',$user) }}" novalidate>
    @csrf @method('PUT')

    {{-- Profile Info --}}
    <div class="ds-section" data-aos="fade-up">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-person me-2"></i>Profile Information</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Full Name" required :error="$errors->first('name')">
          <x-ui.input type="text" name="name" :value="old('name',$user->name)" required :error="$errors->has('name')"/>
        </x-ui.form-field>
        <x-ui.form-field label="Email Address" required :error="$errors->first('email')">
          <x-ui.input type="email" name="email" :value="old('email',$user->email)" required :error="$errors->has('email')"/>
        </x-ui.form-field>
        <x-ui.form-field label="Institution / Affiliation">
          <x-ui.input type="text" name="affiliation" :value="old('affiliation',$user->affiliation)" placeholder="University / Research Institute"/>
        </x-ui.form-field>
        <div class="row g-3">
          <div class="col-md-6">
            <x-ui.form-field label="Role" required :error="$errors->first('role')">
              <x-ui.select name="role" required :error="$errors->has('role')">
                @foreach($roles as $r)
                  <option value="{{ $r }}" {{ old('role',$user->role) === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
              </x-ui.select>
            </x-ui.form-field>
          </div>
          <div class="col-md-6">
            <x-ui.form-field label="Account Status">
              <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:var(--bg-app);border:1px solid var(--border);border-radius:var(--radius-sm);">
                <input type="hidden" name="is_active" value="0"/>
                <x-ui.checkbox id="ia" name="is_active" value="1" :checked="old('is_active',$user->is_active)" label="Account Active"/>
              </div>
            </x-ui.form-field>
          </div>
        </div>
      </div>
    </div>

    {{-- Password --}}
    <div class="ds-section" data-aos="fade-up" data-aos-delay="200">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-key me-2"></i>Change Password</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="New Password" hint="Leave blank to keep current password." :error="$errors->first('password')">
          <x-ui.input type="password" name="password" placeholder="Min. 8 characters" :error="$errors->has('password')"/>
        </x-ui.form-field>
      </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:4px;">
      <button type="submit" class="ds-btn ds-btn-pri">
        <i class="bi bi-check-lg"></i> Save Changes
      </button>
      <a href="{{ route('admin.users.index') }}" class="ds-btn ds-btn-out">Cancel</a>
    </div>
  </form>
</div>

@endsection
