@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Portal Penulis'],['label'=>'Naskah Saya','href'=>route('author.articles.index')],['label'=>'Detail','href'=>route('author.articles.show',$article)],['label'=>'Unggah Revisi']]"/>
    <h1 class="ds-page-title">Unggah Revisi</h1>
    <p class="ds-page-subtitle" style="max-width:700px;line-height:1.4;">{{ Str::limit($article->title, 100) }}</p>
  </div>
  <x-status-badge status="revision_required" label="Revisi Diperlukan"/>
</div>

<div style="max-width:740px;">

  {{-- Editor Note --}}
  @if($article->editor_note)
  <div class="ds-alert ds-alert-warn" data-aos="fade-up" data-aos-delay="100" style="margin-bottom:20px;">
    <i class="bi bi-chat-left-text-fill" style="margin-top:2px;"></i>
    <div>
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:6px;">Catatan dari Editor</div>
      <div style="font-size:13px;line-height:1.7;">{{ $article->editor_note }}</div>
    </div>
  </div>
  @endif

  {{-- Pre-upload Checklist --}}
  <div class="ds-card" data-aos="fade-up" data-aos-delay="200" style="margin-bottom:20px;padding:20px 24px;">
    <div style="font-size:14px;font-weight:700;color:var(--text-main);margin-bottom:12px;display:flex;align-items:center;gap:8px;">
      <i class="bi bi-list-check" style="color:var(--primary);font-size:18px;"></i>
      Panduan Sebelum Mengunggah
    </div>
    @php
    $checks = [
      'Saya telah membaca dan menindaklanjuti komentar editor.',
      'Revisi telah dilakukan sesuai dengan saran dari para penelaah.',
      'Manuskrip revisi dalam format PDF, DOC, atau DOCX.',
      'Ukuran berkas tidak melebihi 10MB.',
      'Semua perubahan telah ditandai atau dicatat dalam catatan revisi.'
    ];
    @endphp
    <div style="display:flex;flex-direction:column;gap:10px;">
      @foreach($checks as $check)
      <label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;">
        <input type="checkbox" style="margin-top:3px;accent-color:var(--primary);width:16px;height:16px;border-radius:4px;border:1px solid var(--border);"/>
        <span style="font-size:13px;color:var(--text-main);line-height:1.5;">{{ $check }}</span>
      </label>
      @endforeach
    </div>
  </div>

  {{-- Upload Form --}}
  <div class="ds-section" data-aos="fade-up" data-aos-delay="300">
    <div class="ds-section-hdr">
      <h3 class="ds-section-title"><i class="bi bi-upload me-2" style="color:var(--warning);"></i>Unggah Berkas Revisi</h3>
    </div>
    <div class="ds-section-body">
      <form method="POST" action="{{ route('author.articles.revision.store', $article) }}" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- Previous Revision Warning --}}
        @if($article->revision_file)
        <div style="background:var(--bg-app);border:1px solid var(--border);border-radius:8px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:14px;">
          <div style="width:36px;height:36px;border-radius:8px;background:#E2E8F0;color:var(--text-muted);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
            <i class="bi bi-file-earmark-text"></i>
          </div>
          <div style="flex:1;">
            <div style="font-size:13px;font-weight:600;color:var(--text-main);">Berkas revisi sudah ada</div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">Mengunggah berkas baru akan menggantikan berkas revisi saat ini</div>
          </div>
          <a href="{{ asset($article->revision_file) }}" target="_blank" class="ds-btn ds-btn-out ds-btn-sm">
            <i class="bi bi-download"></i> Lihat
          </a>
        </div>
        @endif

        <x-ui.form-field label="Manuskrip Revisi" required :error="$errors->first('revision_file')" hint="Format yang diterima: PDF, DOC, DOCX. Maksimal 10MB.">
          <input type="file" name="revision_file" accept=".pdf,.doc,.docx" required
                 class="{{ $errors->has('revision_file') ? 'is-invalid' : '' }}"
                 style="display:block;width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-app);font-size:13px;color:var(--text-main);cursor:pointer;"/>
        </x-ui.form-field>

        <x-ui.form-field label="Catatan Revisi (Tanggapan untuk Penelaah)" hint="Jelaskan bagaimana Anda menanggapi komentar dari penelaah.">
          <x-ui.textarea name="author_note" rows="5" placeholder="Detailkan perubahan yang Anda buat berdasarkan umpan balik dari masing-masing penelaah...">{{ old('author_note') }}</x-ui.textarea>
        </x-ui.form-field>

        <div style="display:flex;gap:12px;margin-top:24px;">
          <button type="submit" class="ds-btn ds-btn-pri" style="height:44px;padding:0 24px;background:var(--warning);border-color:var(--warning);">
            <i class="bi bi-upload"></i> Unggah Revisi
          </button>
          <a href="{{ route('author.articles.show', $article) }}" class="ds-btn ds-btn-out" style="height:44px;padding:0 20px;">Batal</a>
        </div>
      </form>
    </div>
  </div>

</div>

@endsection
