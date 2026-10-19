@extends('layouts.dashboard')
@section('title', 'Edit Role')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Role & Permissions','href'=>route('admin.roles.index')],['label'=>'Edit: '.$role->name]]"/>
    <h1 class="ds-page-title">Edit Role: {{ $role->name }}</h1>
    <p class="ds-page-subtitle">Atur permission untuk role ini</p>
  </div>
</div>

<form method="POST" action="{{ route('admin.roles.update', $role) }}">
  @csrf @method('PUT')
  <div style="max-width:620px;" class="mb-4" data-aos="fade-up">
    <div class="ds-card">
      <div class="p-4">
        <x-ui.form-field label="Nama Role" required :error="$errors->first('name')">
          <x-ui.input type="text" name="name" :value="old('name', $role->name)" required :error="$errors->has('name')"
            {{ in_array($role->name, ['super-admin','system-admin']) ? 'disabled' : '' }}/>
          @if(in_array($role->name, ['super-admin','system-admin']))
            <div class="form-text" style="font-size:11px;">Nama role sistem tidak dapat diubah.</div>
            <input type="hidden" name="name" value="{{ $role->name }}">
          @endif
        </x-ui.form-field>
      </div>
    </div>
  </div>

  @include('admin.roles._permissions-picker', ['selected' => $role->permissions->pluck('name')->all()])

  <div style="display:flex;gap:12px;margin-top:4px;">
    <button type="submit" class="ds-btn ds-btn-pri"><i class="bi bi-save"></i> Simpan Perubahan</button>
    <a href="{{ route('admin.roles.index') }}" class="ds-btn ds-btn-out">Batal</a>
  </div>
</form>

@endsection