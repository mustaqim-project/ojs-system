{{-- admin/users/create.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Users','href'=>route('admin.users.index')],['label'=>'Add New User']]"/>
    <h1 class="ds-page-title">Add New User</h1>
    <p class="ds-page-subtitle">Create a new account and assign a role</p>
  </div>
</div>

<div style="max-width:620px;">
  <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
    @csrf

    <div class="ds-section" data-aos="fade-up">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-person me-2"></i>Personal Information</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Full Name" required :error="$errors->first('name')">
          <x-ui.input type="text" name="name" :value="old('name')" placeholder="Dr. Full Name" autofocus required :error="$errors->has('name')"/>
        </x-ui.form-field>
        <x-ui.form-field label="Email Address" required :error="$errors->first('email')" hint="Use an institutional email for academic credibility.">
          <x-ui.input type="email" name="email" :value="old('email')" placeholder="name@institution.ac.id" required :error="$errors->has('email')"/>
        </x-ui.form-field>
        <x-ui.form-field label="Institution / Affiliation" :error="$errors->first('affiliation')">
          <x-ui.input type="text" name="affiliation" :value="old('affiliation')" placeholder="University / Research Institute"/>
        </x-ui.form-field>
        <x-ui.form-field label="Phone Number">
          <x-ui.input type="text" name="phone" :value="old('phone')" placeholder="+62 812 3456 7890"/>
        </x-ui.form-field>
      </div>
    </div>

    <div class="ds-section" data-aos="fade-up" data-aos-delay="200">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-shield-lock me-2"></i>Account Credentials</h3>
      </div>
      <div class="ds-section-body">
        <div class="row g-3">
          <div class="col-md-6">
            <x-ui.form-field label="Password" required :error="$errors->first('password')">
              <x-ui.input type="password" name="password" placeholder="Min. 8 characters" required :error="$errors->has('password')"/>
            </x-ui.form-field>
          </div>
          <div class="col-md-6">
            <x-ui.form-field label="Role" required :error="$errors->first('role')">
              <x-ui.select name="role" :error="$errors->has('role')" placeholder="Select role" required>
                @foreach($roles as $r)
                  <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
              </x-ui.select>
            </x-ui.form-field>
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:4px;">
      <button type="submit" class="ds-btn ds-btn-pri">
        <i class="bi bi-person-plus-fill"></i> Create User
      </button>
      <a href="{{ route('admin.users.index') }}" class="ds-btn ds-btn-out">Cancel</a>
    </div>
  </form>
</div>

@endsection
