@extends('layouts.dashboard')
@section('title', 'Edit Permission')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Permissions','href'=>route('admin.permissions.index')],['label'=>'Edit: '.$permission->name]]"/>
    <h1 class="ds-page-title">Edit Permission</h1>
    <p class="ds-page-subtitle">Ubah nama atau afiliasi permission ke role</p>
  </div>
</div>

<form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
  @csrf @method('PUT')
  <div style="max-width:620px;" data-aos="fade-up">
    <div class="ds-card">
      <div class="p-4">
        <x-ui.form-field label="Nama Permission" required :error="$errors->first('name')">
          <x-ui.input type="text" name="name" :value="old('name', $permission->name)" required :error="$errors->has('name')"/>
        </x-ui.form-field>

        <label class="form-label" style="font-size:13px;font-weight:600;display:block;">Berikan ke Role</label>
        <div class="d-flex flex-wrap gap-2 mb-2">
          @forelse($roles as $role)
            <label class="border rounded px-2 py-1" style="font-size:12px;cursor:pointer;background:var(--bg-app);display:inline-flex;align-items:center;gap:6px;">
              <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ in_array($role->name, $grantedRoles) ? 'checked' : '' }}>
              {{ $role->name }}
            </label>
          @empty
            <div class="form-text">Belum ada role.</div>
          @endforelse
        </div>

        <div style="display:flex;gap:12px;margin-top:4px;">
          <button type="submit" class="ds-btn ds-btn-pri"><i class="bi bi-save"></i> Simpan Perubahan</button>
          <a href="{{ route('admin.permissions.index') }}" class="ds-btn ds-btn-out">Batal</a>
        </div>
      </div>
    </div>
  </div>
</form>

@endsection