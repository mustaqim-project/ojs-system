@extends('layouts.dashboard')
@section('title', 'Tambah Role')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Role & Permissions','href'=>route('admin.roles.index')],['label'=>'Tambah Role']]"/>
    <h1 class="ds-page-title">Tambah Role Baru</h1>
    <p class="ds-page-subtitle">Buat role kustom dan berikan permission</p>
  </div>
</div>

<form method="POST" action="{{ route('admin.roles.store') }}">
  @csrf
  <div style="max-width:620px;" class="mb-4" data-aos="fade-up">
    <div class="ds-card">
      <div class="p-4">
        <x-ui.form-field label="Nama Role" required :error="$errors->first('name')" hint="Huruf kecil, angka, strip & underscore (mis. layout-editor).">
          <x-ui.input type="text" name="name" :value="old('name')" placeholder="editor-konten" required :error="$errors->has('name')"/>
        </x-ui.form-field>
      </div>
    </div>
  </div>

  @include('admin.roles._permissions-picker', ['selected' => []])

  <div style="display:flex;gap:12px;margin-top:4px;">
    <button type="submit" class="ds-btn ds-btn-pri"><i class="bi bi-save"></i> Simpan Role</button>
    <a href="{{ route('admin.roles.index') }}" class="ds-btn ds-btn-out">Batal</a>
  </div>
</form>

@endsection