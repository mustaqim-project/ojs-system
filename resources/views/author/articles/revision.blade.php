{{-- author/articles/revision.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb">
      <a href="{{ route('author.dashboard') }}">Dashboard</a><span>›</span>
      <a href="{{ route('author.articles.index') }}">Artikel</a><span>›</span>
      <a href="{{ route('author.articles.show', $article) }}">Detail</a><span>›</span>
      <span class="cur">Upload Revisi</span>
    </div>
    <h2 class="pg-title">Upload Revisi</h2>
    <p class="pg-desc">{{ Str::limit($article->title, 70) }}</p>
  </div>
  <span class="bx bx-revision_required" style="font-size:12px;padding:5px 14px;">Revision Required</span>
</div>

<div style="max-width:640px;">

  {{-- Editor note --}}
  @if($article->editor_note)
  <div class="alert-o a-warn fu fd1" style="margin-bottom:16px;">
    <i class="bi bi-chat-left-text-fill" style="flex-shrink:0;margin-top:1px;"></i>
    <div>
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Catatan dari Editor:</div>
      <div style="font-size:13px;line-height:1.7;">{{ $article->editor_note }}</div>
    </div>
  </div>
  @endif

  {{-- Checklist --}}
  <div style="background:var(--surf);border:1px solid var(--brd);border-radius:var(--r);padding:20px;margin-bottom:16px;" class="fu fd2">
    <div style="font-size:12px;font-weight:700;color:var(--txt);margin-bottom:12px;display:flex;align-items:center;gap:8px;">
      <i class="bi bi-list-check" style="color:var(--acc);font-size:15px;"></i>
      Checklist Sebelum Upload
    </div>
    @php $checks=[
      'Telah membaca dan memahami catatan editor',
      'Revisi sudah sesuai dengan saran reviewer',
      'File revisi dalam format PDF/DOC/DOCX',
      'File revisi berukuran maksimal 10MB',
      'Tidak ada perubahan yang belum disetujui editor',
    ]; @endphp
    @foreach($checks as $check)
    <label style="display:flex;align-items:center;gap:10px;padding:6px 0;cursor:pointer;font-size:13px;color:var(--txt2);">
      <input type="checkbox" style="width:15px;height:15px;accent-color:var(--acc);flex-shrink:0;"/> {{ $check }}
    </label>
    @endforeach
  </div>

  {{-- Form --}}
  <div class="f-section fu fd3">
    <div class="f-section-hdr"><h3 class="f-section-title">Upload File Revisi</h3></div>
    <div class="f-section-body">
      <form method="POST" action="{{ route('author.articles.revision.store', $article) }}" enctype="multipart/form-data">
        @csrf

        {{-- Previous revision info --}}
        @if($article->revision_file)
        <div style="background:var(--canvas);border:1px solid var(--brd);border-radius:7px;padding:12px 14px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
          <i class="bi bi-file-earmark-text" style="color:var(--txt3);font-size:16px;flex-shrink:0;"></i>
          <div style="flex:1;">
            <div style="font-size:12px;font-weight:600;color:var(--txt);">Revisi sebelumnya sudah ada</div>
            <div style="font-size:11px;color:var(--txt3);">File baru akan menggantikan revisi sebelumnya</div>
          </div>
          <a href="{{ asset('storage/'.$article->revision_file) }}" target="_blank" class="btn-o btn-ghost btn-sm">
            <i class="bi bi-download"></i> Lihat
          </a>
        </div>
        @endif

        <div class="f-group">
          <label class="lbl">File Revisi <span class="req">*</span></label>
          <input class="file-inp {{ $errors->has('revision_file') ? 'is-invalid' : '' }}"
                 type="file" name="revision_file" accept=".pdf,.doc,.docx" required/>
          <div class="f-hint-txt"><i class="bi bi-info-circle me-1"></i>Format: PDF, DOC, DOCX. Maksimal 10MB.</div>
          @error('revision_file')<div class="f-err"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
        </div>

        <div class="f-group mb-0">
          <label class="lbl">Catatan Revisi <span class="hint">(opsional)</span></label>
          <textarea name="author_note" class="txta" rows="3"
                    placeholder="Jelaskan perubahan yang telah Anda lakukan sesuai saran reviewer dan editor...">{{ old('author_note') }}</textarea>
        </div>

        <div style="margin-top:20px;display:flex;gap:10px;">
          <button type="submit" class="btn-o btn-warn btn-lg">
            <i class="bi bi-upload"></i> Upload Revisi
          </button>
          <a href="{{ route('author.articles.show', $article) }}" class="btn-o btn-out btn-lg">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
