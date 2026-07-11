@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb"><a href="{{ route('author.dashboard') }}">Dashboard</a><span>›</span><a href="{{ route('author.articles.index') }}">Artikel</a><span>›</span><span class="cur">Submit Baru</span></div>
    <h2 class="pg-title">Submit Artikel Baru</h2>
    <p class="pg-desc">Isi semua informasi artikel dengan lengkap dan benar.</p>
  </div>
</div>

<div style="max-width:760px;">
  <form method="POST" action="{{ route('author.articles.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- Jurnal --}}
    <div class="f-section fu fd1">
      <div class="f-section-hdr"><h3 class="f-section-title">Pilih Jurnal</h3></div>
      <div class="f-section-body">
        <div class="f-group">
          <label class="lbl">Jurnal Tujuan <span class="req">*</span></label>
          <select name="journal_id" class="sel {{ $errors->has('journal_id') ? 'is-invalid':'' }}" required>
            <option value="">-- Pilih jurnal yang sesuai --</option>
            @foreach($journals as $j)
            <option value="{{ $j->id }}" {{ old('journal_id')==$j->id?'selected':'' }}>{{ $j->title }} ({{ $j->abbreviation }})</option>
            @endforeach
          </select>
          @error('journal_id')<div class="f-err"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

    {{-- Info Artikel --}}
    <div class="f-section fu fd2">
      <div class="f-section-hdr"><h3 class="f-section-title">Informasi Artikel</h3></div>
      <div class="f-section-body">
        <div class="f-group">
          <label class="lbl">Judul Artikel <span class="req">*</span></label>
          <input class="inp {{ $errors->has('title')?'is-invalid':'' }}" type="text" name="title"
                 value="{{ old('title') }}" placeholder="Masukkan judul artikel yang deskriptif" required/>
          @error('title')<div class="f-err">{{ $message }}</div>@enderror
        </div>
        <div class="f-group">
          <label class="lbl">Abstrak <span class="req">*</span> <span class="hint">Minimal 100 karakter</span></label>
          <textarea name="abstract" class="txta {{ $errors->has('abstract')?'is-invalid':'' }}"
                    rows="6" placeholder="Tuliskan abstrak penelitian Anda secara ringkas dan jelas..." required>{{ old('abstract') }}</textarea>
          @error('abstract')<div class="f-err">{{ $message }}</div>@enderror
        </div>
        <div class="row g-3">
          <div class="col-md-8">
            <label class="lbl">Kata Kunci <span class="req">*</span> <span class="hint">Pisahkan dengan koma</span></label>
            <input class="inp {{ $errors->has('keywords')?'is-invalid':'' }}" type="text" name="keywords"
                   value="{{ old('keywords') }}" placeholder="machine learning, deep learning, NLP"/>
            @error('keywords')<div class="f-err">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="lbl">Bahasa <span class="req">*</span></label>
            <select name="language" class="sel">
              <option value="id" {{ old('language','id')==='id'?'selected':'' }}>Bahasa Indonesia</option>
              <option value="en" {{ old('language')==='en'?'selected':'' }}>English</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    {{-- File --}}
    <div class="f-section fu fd3">
      <div class="f-section-hdr"><h3 class="f-section-title">Upload File</h3></div>
      <div class="f-section-body">
        <div class="f-group">
          <label class="lbl">File Manuskrip <span class="req">*</span></label>
          <input class="file-inp {{ $errors->has('manuscript_file')?'is-invalid':'' }}" type="file"
                 name="manuscript_file" accept=".pdf,.doc,.docx" required/>
          <div class="f-hint-txt"><i class="bi bi-info-circle me-1"></i>Format: PDF, DOC, DOCX. Maks 10MB.</div>
          @error('manuscript_file')<div class="f-err">{{ $message }}</div>@enderror
        </div>
        <div class="f-group">
          <label class="lbl">Cover Letter <span class="hint">(opsional)</span></label>
          <input class="file-inp" type="file" name="cover_letter" accept=".pdf,.doc,.docx"/>
          <div class="f-hint-txt"><i class="bi bi-info-circle me-1"></i>Surat pengantar kepada editor.</div>
        </div>
        <div class="f-group mb-0">
          <label class="lbl">Catatan untuk Editor <span class="hint">(opsional)</span></label>
          <textarea name="author_note" class="txta" rows="3" placeholder="Informasi tambahan untuk editor...">{{ old('author_note') }}</textarea>
        </div>
      </div>
    </div>

    {{-- Warning --}}
    <div class="alert-o a-info fu fd4" style="margin-bottom:20px;">
      <i class="bi bi-info-circle-fill"></i>
      <div style="font-size:13px;">Pastikan manuskrip Anda sudah sesuai panduan penulisan jurnal sebelum disubmit. Artikel yang disubmit tidak dapat ditarik kembali.</div>
    </div>

    <div class="d-flex gap-3 fu fd5">
      <button type="submit" class="btn-o btn-pri btn-lg"><i class="bi bi-send-fill"></i> Submit Artikel</button>
      <a href="{{ route('author.articles.index') }}" class="btn-o btn-out btn-lg">Batal</a>
    </div>
  </form>
</div>
@endsection
