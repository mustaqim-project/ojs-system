@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb"><a href="{{ route('admin.articles.index') }}">Artikel</a><span>›</span><span class="cur">Detail</span></div>
    <h2 class="pg-title" style="font-size:17px;max-width:640px;line-height:1.3;">{{ $article->title }}</h2>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <a href="{{ route('admin.export.article', $article) }}" class="btn-o btn-out btn-sm"><i class="bi bi-file-earmark-code"></i> Export XML</a>
    <span class="bx bx-{{ $article->status }}" style="font-size:12px;padding:5px 14px;">{{ $article->status_label }}</span>
  </div>
</div>

{{-- Publish CTA --}}
@if($article->canBePublished())
<div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:2px solid var(--green-b);border-radius:var(--r);padding:20px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;" class="fu">
  <div style="flex:1;">
    <div style="font-size:14px;font-weight:700;color:var(--green);margin-bottom:3px;"><i class="bi bi-check-circle-fill me-2"></i>Artikel Siap Dipublish!</div>
    <div style="font-size:13px;color:#166534;">Pembayaran sudah terverifikasi. Pilih issue dan publish artikel ini.</div>
  </div>
  <button class="btn-o btn-suc btn-lg" data-bs-toggle="modal" data-bs-target="#publishModal">
    <i class="bi bi-rocket-takeoff-fill"></i> Publish Artikel
  </button>
</div>
{{-- Publish Modal --}}
<div class="modal fade" id="publishModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-rocket-takeoff-fill me-2" style="color:var(--green);"></i>Publish Artikel</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p style="font-size:13px;color:var(--txt2);margin-bottom:16px;">Pilih issue untuk menempatkan artikel ini:</p>
        <form method="POST" action="{{ route('admin.articles.publish',$article) }}" id="publishForm">
          @csrf
          <div class="f-group mb-0">
            <label class="lbl">Issue <span class="req">*</span></label>
            <select name="issue_id" class="sel" required>
              <option value="">-- Pilih Issue --</option>
              @foreach($issues as $issue)
              <option value="{{ $issue->id }}">{{ $issue->display_title }} — {{ $issue->journal->title }}</option>
              @endforeach
            </select>
            @error('issue_id')<div class="f-err">{{ $message }}</div>@enderror
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-o btn-out" data-bs-dismiss="modal">Batal</button>
        <button type="submit" form="publishForm" class="btn-o btn-suc"><i class="bi bi-rocket-takeoff-fill"></i> Publish Sekarang</button>
      </div>
    </div>
  </div>
</div>
@endif

<div class="row g-3">
  <div class="col-12 col-lg-8">
    {{-- Detail --}}
    <div class="card-ojs fu fd1">
      <div class="card-hdr">
        <span class="card-title">Informasi Artikel</span>
        <div class="d-flex gap-2">
          @if($article->manuscript_file)<a href="{{ asset('storage/'.$article->manuscript_file) }}" target="_blank" class="btn-o btn-out btn-sm"><i class="bi bi-download"></i> Manuskrip</a>@endif
          @if($article->revision_file)<a href="{{ asset('storage/'.$article->revision_file) }}" target="_blank" class="btn-o btn-warn btn-sm"><i class="bi bi-download"></i> Revisi</a>@endif
        </div>
      </div>
      <div>
        <div class="info-row"><span class="info-key">Jurnal</span><span class="info-val">{{ $article->journal->title }}</span></div>
        <div class="info-row"><span class="info-key">Author</span><span class="info-val">{{ $article->author->name }} <span style="color:var(--txt3);font-size:12px;">({{ $article->author->email }})</span></span></div>
        @if($article->assignedEditor)<div class="info-row"><span class="info-key">Editor</span><span class="info-val">{{ $article->assignedEditor->name }}</span></div>@endif
        @if($article->issue)<div class="info-row"><span class="info-key">Issue</span><span class="info-val">{{ $article->issue->display_title }}</span></div>@endif
        <div class="info-row"><span class="info-key">Abstrak</span><span class="info-val" style="line-height:1.75;color:var(--txt2);">{{ $article->abstract }}</span></div>
        <div class="info-row"><span class="info-key">Kata Kunci</span><span class="info-val" style="display:flex;flex-wrap:wrap;gap:4px;">@foreach($article->keywords_array as $kw)<span style="font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:5px;border:1px solid #e2e8f0;">{{ $kw }}</span>@endforeach</span></div>
        <div class="info-row"><span class="info-key">Disubmit</span><span class="info-val">{{ $article->submitted_at?->format('d M Y H:i') }}</span></div>
        @if($article->accepted_at)<div class="info-row"><span class="info-key">Diterima</span><span class="info-val">{{ $article->accepted_at->format('d M Y H:i') }}</span></div>@endif
        @if($article->published_at)<div class="info-row"><span class="info-key">Dipublish</span><span class="info-val">{{ $article->published_at->format('d M Y H:i') }}</span></div>@endif
        @if($article->doi)<div class="info-row"><span class="info-key">DOI</span><span class="info-val" style="font-family:'Courier New',monospace;color:var(--acc);">{{ $article->doi }}</span></div>@endif
      </div>
    </div>

    {{-- Reviews --}}
    @if($article->reviews->count())
    <div class="card-ojs fu fd2" style="margin-top:12px;">
      <div class="card-hdr"><span class="card-title">Review ({{ $article->reviews->count() }})</span></div>
      @foreach($article->reviews as $review)
      <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
          <div style="display:flex;align-items:center;gap:8px;">
            <div style="width:28px;height:28px;border-radius:7px;background:#f5f3ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;">{{ strtoupper(substr($review->reviewer->name,0,1)) }}</div>
            <div><div style="font-size:12px;font-weight:600;color:var(--txt);">{{ $review->reviewer->name }}</div><div style="font-size:11px;color:var(--txt3);">{{ $review->reviewer->affiliation }}</div></div>
          </div>
          <div style="display:flex;gap:5px;">
            <span class="bx bx-gray" style="font-size:10px;">{{ ucfirst($review->status) }}</span>
            @if($review->recommendation)<span class="bx bx-{{ $review->recommendation }}" style="font-size:10px;">{{ $review->recommendation_label }}</span>@endif
          </div>
        </div>
        @if($review->average_score)<div style="font-size:11px;color:var(--txt3);margin-bottom:6px;">Skor: <strong style="color:var(--txt);">{{ $review->average_score }}/5</strong></div>@endif
        @if($review->comments_to_author)<div style="background:#f8f9fb;border-radius:7px;padding:10px;font-size:12px;color:var(--txt2);line-height:1.65;margin-bottom:5px;"><strong style="font-size:10px;text-transform:uppercase;letter-spacing:.04em;color:var(--txt3);">Komentar Author:</strong><br/>{{ $review->comments_to_author }}</div>@endif
        @if($review->comments_to_editor)<div style="background:#fefce8;border-radius:7px;padding:10px;font-size:12px;color:#713f12;line-height:1.65;"><strong style="font-size:10px;text-transform:uppercase;letter-spacing:.04em;">Konfidensial:</strong><br/>{{ $review->comments_to_editor }}</div>@endif
      </div>
      @endforeach
    </div>
    @endif

    {{-- Payment --}}
    @if($article->payment)
    <div class="card-ojs fu fd3" style="margin-top:12px;">
      <div class="card-hdr">
        <span class="card-title">Pembayaran</span>
        <a href="{{ route('admin.payments.show',$article->payment) }}" class="btn-o btn-ghost btn-sm">Kelola <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
      <div style="padding:16px 20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
        <div><div style="font-size:11px;color:var(--txt3);margin-bottom:2px;">Invoice</div><div style="font-family:'Courier New',monospace;font-size:13px;font-weight:700;color:var(--txt);">{{ $article->payment->invoice_code }}</div></div>
        <div style="width:1px;height:30px;background:var(--brd);"></div>
        <div><div style="font-size:11px;color:var(--txt3);margin-bottom:2px;">Nominal</div><div style="font-size:18px;font-weight:800;color:var(--txt);">Rp {{ number_format($article->payment->amount,0,',','.') }}</div></div>
        <div style="margin-left:auto;"><span class="bx bx-{{ $article->payment->status }}">{{ $article->payment->status_label }}</span></div>
      </div>
    </div>
    @endif
  </div>

  {{-- Sidebar --}}
  <div class="col-12 col-lg-4">
    <div style="position:sticky;top:80px;">
      <div class="card-ojs fu fd2">
        <div class="card-hdr"><span class="card-title">Info Singkat</span></div>
        <div>
          <div class="info-row"><span class="info-key">Status</span><span class="info-val"><span class="bx bx-{{ $article->status }}" style="font-size:11px;">{{ $article->status_label }}</span></span></div>
          <div class="info-row"><span class="info-key">Bahasa</span><span class="info-val">{{ strtoupper($article->language) }}</span></div>
          <div class="info-row"><span class="info-key">Total Review</span><span class="info-val">{{ $article->reviews->count() }} reviewer</span></div>
          @if($article->pages_start)<div class="info-row"><span class="info-key">Halaman</span><span class="info-val">{{ $article->pages_start }}–{{ $article->pages_end }}</span></div>@endif
        </div>
      </div>

      {{-- Kelola Metadata --}}
      <div class="card-ojs fu fd3 mt-3">
        <div class="card-hdr"><span class="card-title"><i class="bi bi-link-45deg me-2" style="color:var(--acc);"></i>Metadata & DOI</span></div>
        <div style="padding:16px 20px;">
          <form method="POST" action="{{ route('admin.articles.update-metadata', $article) }}">
            @csrf
            <div class="f-group">
              <label class="lbl">DOI</label>
              <input type="text" name="doi" class="inp" value="{{ $article->doi }}" placeholder="e.g. 10.12345/journal.2026.1"/>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="lbl">Halaman Awal</label>
                <input type="number" name="pages_start" class="inp" value="{{ $article->pages_start }}" placeholder="Awal"/>
              </div>
              <div class="col-6">
                <label class="lbl">Halaman Akhir</label>
                <input type="number" name="pages_end" class="inp" value="{{ $article->pages_end }}" placeholder="Akhir"/>
              </div>
            </div>
            <button type="submit" class="btn-o btn-pri w-100 justify-content-center">
              <i class="bi bi-floppy"></i> Simpan Metadata
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
