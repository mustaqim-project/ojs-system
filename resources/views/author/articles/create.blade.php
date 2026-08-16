@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Portal Penulis'],['label'=>'Naskah Saya','href'=>route('author.articles.index')],['label'=>'Kiriman Baru']]"/>
    <h1 class="ds-page-title">Kirim Manuskrip Baru</h1>
    <p class="ds-page-subtitle">Lengkapi semua informasi yang diperlukan sebelum mengirimkan manuskrip Anda.</p>
  </div>
</div>

<div style="max-width:780px;">
  <form method="POST" action="{{ route('author.articles.store') }}" enctype="multipart/form-data" novalidate>
    @csrf

    {{-- Step 1: Journal --}}
    <div class="ds-section" data-aos="fade-up" data-aos-delay="100">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-journal-bookmark me-2" style="color:var(--primary);"></i>Langkah 1: Pilih Jurnal</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Jurnal Tujuan" required :error="$errors->first('journal_id')">
          <x-ui.select name="journal_id" required :error="$errors->has('journal_id')" placeholder="Pilih jurnal yang paling sesuai dengan manuskrip Anda">
            @foreach($journals as $j)
              <option value="{{ $j->id }}" {{ old('journal_id') == $j->id ? 'selected' : '' }}>
                {{ $j->title }}{{ $j->abbreviation ? ' ('.$j->abbreviation.')' : '' }}
              </option>
            @endforeach
          </x-ui.select>
        </x-ui.form-field>
      </div>
    </div>

    {{-- Step 2: Article Info --}}
    <div class="ds-section" data-aos="fade-up" data-aos-delay="200">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-file-earmark-text me-2" style="color:var(--primary);"></i>Langkah 2: Informasi Manuskrip</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Judul Artikel" required :error="$errors->first('title')" hint="Buat judul yang spesifik dan deskriptif. Hindari singkatan.">
          <x-ui.input type="text" name="title" :value="old('title')" placeholder="Studi Komprehensif tentang..." required :error="$errors->has('title')"/>
        </x-ui.form-field>
        <x-ui.form-field label="Abstrak" required :error="$errors->first('abstract')" hint="Minimal 100 karakter. Ringkas masalah, metode, hasil, dan kesimpulan.">
          <x-ui.textarea name="abstract" rows="6" placeholder="Studi ini menyelidiki..." required :error="$errors->has('abstract')">{{ old('abstract') }}</x-ui.textarea>
        </x-ui.form-field>
        <div class="row g-3">
          <div class="col-md-8">
            <x-ui.form-field label="Kata Kunci" required :error="$errors->first('keywords')" hint="Pisahkan dengan koma (minimal 2 kata kunci).">
              <x-ui.input type="text" name="keywords" :value="is_array(old('keywords')) ? implode(', ', old('keywords')) : old('keywords')" placeholder="pembelajaran mesin, NLP, sistem pakar" required :error="$errors->has('keywords')"/>
            </x-ui.form-field>
          </div>
          <div class="col-md-4">
            <x-ui.form-field label="Bahasa" required :error="$errors->first('language')">
              <x-ui.select name="language" required :error="$errors->has('language')">
                <option value="id" {{ old('language','id') === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                <option value="en" {{ old('language') === 'en' ? 'selected' : '' }}>English</option>
              </x-ui.select>
            </x-ui.form-field>
          </div>
        </div>
      </div>
    </div>

    {{-- Step 3: File Upload --}}
    <div class="ds-section" data-aos="fade-up" data-aos-delay="300">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-upload me-2" style="color:var(--primary);"></i>Langkah 3: Unggah Berkas</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Berkas Manuskrip" required :error="$errors->first('manuscript_file')" hint="Format yang diterima: PDF, DOC, DOCX. Maksimal 10MB.">
          <input type="file" name="manuscript_file" accept=".pdf,.doc,.docx" required
                 class="{{ $errors->has('manuscript_file') ? 'is-invalid' : '' }}"
                 style="display:block;width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-app);font-size:13px;color:var(--text-main);cursor:pointer;"/>
        </x-ui.form-field>
      </div>
    </div>

    {{-- Submission Warning --}}
    <div class="ds-alert ds-alert-info" data-aos="fade-up" style="margin-bottom:24px;">
      <i class="bi bi-info-circle-fill"></i>
      <div style="font-size:13px;line-height:1.6;">Harap pastikan manuskrip Anda mengikuti panduan penulis jurnal sebelum dikirim. Setelah dikirim, manuskrip tidak dapat ditarik kembali tanpa menghubungi kantor editorial.</div>
    </div>

    <div style="display:flex;gap:12px;">
      <button type="submit" class="ds-btn ds-btn-pri" style="height:44px;padding:0 28px;font-size:15px;">
        <i class="bi bi-send-fill"></i> Kirim Manuskrip
      </button>
      <a href="{{ route('author.articles.index') }}" class="ds-btn ds-btn-out" style="height:44px;padding:0 20px;">Batal</a>
    </div>
  </form>
</div>

@endsection
