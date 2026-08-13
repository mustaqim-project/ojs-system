{{-- admin/issues/create.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Terbitan','href'=>route('admin.issues.index')],['label'=>'Tambah Terbitan Baru']]"/>
    <h1 class="ds-page-title">Tambah Terbitan Baru</h1>
    <p class="ds-page-subtitle">Buat volume/nomor terbitan baru untuk jurnal</p>
  </div>
</div>

<div style="max-width:560px;">
  <div class="ds-section" data-aos="fade-up">
    <div class="ds-section-hdr">
      <h3 class="ds-section-title"><i class="bi bi-collection me-2"></i>Informasi Terbitan</h3>
    </div>
    <div class="ds-section-body">
      <form method="POST" action="{{ route('admin.issues.store') }}" novalidate>
        @csrf
        <x-ui.form-field label="Jurnal" required :error="$errors->first('journal_id')">
          <x-ui.select name="journal_id" required :error="$errors->has('journal_id')">
            @foreach($journals as $j)
              <option value="{{ $j->id }}" {{ old('journal_id') == $j->id ? 'selected' : '' }}>{{ $j->title }}</option>
            @endforeach
          </x-ui.select>
        </x-ui.form-field>
        <x-ui.form-field label="Judul Terbitan" required :error="$errors->first('title')" hint="Contoh: Vol. 1 No. 1, Januari–Maret 2025">
          <x-ui.input type="text" name="title" :value="old('title')" placeholder="Vol. 1 No. 1, Jan–Mar 2025" required :error="$errors->has('title')"/>
        </x-ui.form-field>
        <div class="row g-3">
          <div class="col-4">
            <x-ui.form-field label="Volume" required :error="$errors->first('volume')">
              <x-ui.input type="number" name="volume" :value="old('volume',1)" min="1" required/>
            </x-ui.form-field>
          </div>
          <div class="col-4">
            <x-ui.form-field label="Nomor" required :error="$errors->first('number')">
              <x-ui.input type="number" name="number" :value="old('number',1)" min="1" required/>
            </x-ui.form-field>
          </div>
          <div class="col-4">
            <x-ui.form-field label="Tahun" required :error="$errors->first('year')">
              <x-ui.input type="number" name="year" :value="old('year',date('Y'))" min="2000" max="2100" required/>
            </x-ui.form-field>
          </div>
        </div>
        <x-ui.form-field label="Status Awal">
          <x-ui.select name="status">
            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draf</option>
            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Diterbitkan</option>
          </x-ui.select>
        </x-ui.form-field>
        <div style="display:flex;gap:12px;">
          <button type="submit" class="ds-btn ds-btn-pri"><i class="bi bi-check-lg"></i> Buat Terbitan</button>
          <a href="{{ route('admin.issues.index') }}" class="ds-btn ds-btn-out">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
