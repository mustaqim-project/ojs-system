@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb"><a href="{{ route('editor.dashboard') }}">Dashboard</a><span>›</span><a href="{{ route('editor.articles.index') }}">Artikel</a><span>›</span><span class="cur">Detail</span></div>
    <h2 class="pg-title" style="font-size:17px;max-width:620px;line-height:1.3;">{{ $article->title }}</h2>
  </div>
  <span class="bx bx-{{ $article->status }}" style="font-size:12px;padding:5px 14px;">{{ $article->status_label }}</span>
</div>

<div class="row g-3">
  <div class="col-12 col-xl-8">
    {{-- Article detail --}}
    <div class="card-ojs fu fd1">
      <div class="card-hdr">
        <span class="card-title">Informasi Artikel</span>
        <div class="d-flex gap-2">
          @if($article->manuscript_file)
          <a href="{{ asset('storage/'.$article->manuscript_file) }}" target="_blank" class="btn-o btn-out btn-sm"><i class="bi bi-download"></i> Manuskrip</a>
          @endif
          @if($article->revision_file)
          <a href="{{ asset('storage/'.$article->revision_file) }}" target="_blank" class="btn-o btn-warn btn-sm"><i class="bi bi-download"></i> Revisi</a>
          @endif
        </div>
      </div>
      <div>
        <div class="info-row"><span class="info-key">Jurnal</span><span class="info-val">{{ $article->journal->title }}</span></div>
        <div class="info-row"><span class="info-key">Author</span><span class="info-val">{{ $article->author->name }} <span style="color:#94a3b8;font-size:12px;">{{ $article->author->affiliation ? '· '.$article->author->affiliation : '' }}</span></span></div>
        <div class="info-row"><span class="info-key">Abstrak</span><span class="info-val" style="line-height:1.7;color:#475569;">{{ $article->abstract }}</span></div>
        <div class="info-row"><span class="info-key">Kata Kunci</span>
          <span class="info-val" style="display:flex;flex-wrap:wrap;gap:4px;">
            @foreach($article->keywords_array as $kw)<span style="font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:5px;border:1px solid #e2e8f0;">{{ $kw }}</span>@endforeach
          </span>
        </div>
        <div class="info-row"><span class="info-key">Disubmit</span><span class="info-val">{{ $article->submitted_at?->format('d M Y H:i') }}</span></div>
        @if($article->author_note)<div class="info-row"><span class="info-key">Catatan Author</span><span class="info-val" style="color:#64748b;">{{ $article->author_note }}</span></div>@endif
      </div>
    </div>

    {{-- Reviews --}}
    <div class="card-ojs fu fd2" style="margin-top:12px;">
      <div class="card-hdr"><span class="card-title">Review ({{ $article->reviews->count() }})</span></div>
      @forelse($article->reviews as $review)
      <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;">
        <div style="display:flex;align-items:start;justify-content:space-between;gap:12px;margin-bottom:10px;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;border-radius:8px;background:#f5f3ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;">
              {{ strtoupper(substr($review->reviewer->name,0,1)) }}
            </div>
            <div>
              <div style="font-size:13px;font-weight:600;color:#0f172a;">{{ $review->reviewer->name }}</div>
              <div style="font-size:11px;color:#94a3b8;">{{ $review->reviewer->affiliation }}</div>
            </div>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
            <span class="bx bx-gray">{{ ucfirst($review->status) }}</span>
            @if($review->recommendation)<span class="bx bx-{{ $review->recommendation }}">{{ $review->recommendation_label }}</span>@endif
          </div>
        </div>
        @if($review->status==='completed')
        @if($review->average_score)
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
          <span style="font-size:11px;color:#94a3b8;">Skor:</span>
          <div class="score-stars">
            @for($s=1;$s<=5;$s++)<i class="bi bi-star-fill {{ $s<=$review->average_score?'star-on':'star-off' }}"></i>@endfor
          </div>
          <span style="font-size:11px;font-weight:600;color:#0f172a;">{{ $review->average_score }}/5</span>
        </div>
        @endif
        @if($review->comments_to_author)<div style="background:#f8f9fb;border-radius:8px;padding:12px;margin-bottom:6px;font-size:12px;color:#475569;line-height:1.7;"><strong style="color:#0f172a;font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Komentar Author:</strong><br/>{{ $review->comments_to_author }}</div>@endif
        @if($review->comments_to_editor)<div style="background:#fefce8;border-radius:8px;padding:12px;font-size:12px;color:#713f12;line-height:1.7;"><strong style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Komentar Konfidensial:</strong><br/>{{ $review->comments_to_editor }}</div>@endif
        @else
        <div style="font-size:12px;color:#94a3b8;">Batas review: {{ $review->due_date?->format('d M Y') }}</div>
        @endif
      </div>
      @empty
      <div class="empty-st" style="padding:32px;"><div class="empty-icon"><i class="bi bi-clipboard"></i></div><div class="empty-title">Belum ada reviewer</div><div class="empty-desc">Assign reviewer di panel kanan.</div></div>
      @endforelse
    </div>
  </div>

  {{-- Right panel --}}
  <div class="col-12 col-xl-4">
    <div style="display:flex;flex-direction:column;gap:12px;position:sticky;top:80px;">

      {{-- Assign reviewer --}}
      @if(in_array($article->status,['submitted','under_review','revision_required']))
      <div class="card-ojs fu fd1">
        <div class="card-hdr"><span class="card-title"><i class="bi bi-person-check me-2" style="color:var(--acc);"></i>Assign Reviewer</span></div>
        <div class="card-body-p">
          <form method="POST" action="{{ route('editor.articles.assign-reviewer',$article) }}">
            @csrf
            <div class="f-group">
              <label class="lbl">Pilih Reviewer</label>
              <select name="reviewer_id" class="sel" required>
                <option value="">-- Pilih reviewer --</option>
                @foreach($reviewers as $rv)
                <option value="{{ $rv->id }}">{{ $rv->name }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn-o btn-pri w-100 justify-content-center">
              <i class="bi bi-person-plus"></i> Assign Reviewer
            </button>
          </form>
        </div>
      </div>
      @endif

      {{-- Decision --}}
      @if(in_array($article->status,['submitted','under_review','revision_required']))
      <div class="card-ojs fu fd2">
        <div class="card-hdr"><span class="card-title"><i class="bi bi-gavel me-2" style="color:var(--orng);"></i>Keputusan Editor</span></div>
        <div class="card-body-p">
          <form method="POST" action="{{ route('editor.articles.decision',$article) }}">
            @csrf
            <div class="f-group">
              <label class="lbl">Keputusan <span class="req">*</span></label>
              <select name="decision" class="sel" required>
                <option value="">-- Pilih keputusan --</option>
                <option value="accept">✅ Accept — Terima Artikel</option>
                <option value="revision">🔄 Revision — Minta Revisi</option>
                <option value="reject">❌ Reject — Tolak Artikel</option>
              </select>
            </div>
            <div class="f-group mb-0">
              <label class="lbl">Catatan untuk Author</label>
              <textarea name="editor_note" class="txta" rows="3" placeholder="Alasan keputusan, instruksi revisi, dll..."></textarea>
            </div>
            <button type="submit" onclick="return confirm('Yakin dengan keputusan ini?')"
                    class="btn-o btn-danger w-100 justify-content-center mt-3">
              <i class="bi bi-gavel"></i> Kirim Keputusan
            </button>
          </form>
        </div>
      </div>
      @endif

      {{-- Payment --}}
      @if($article->payment)
      <div class="card-ojs fu fd3">
        <div class="card-hdr"><span class="card-title">Pembayaran</span><span class="bx bx-{{ $article->payment->status }}" style="font-size:11px;">{{ $article->payment->status_label }}</span></div>
        <div style="padding:16px 20px;">
          <div style="font-size:13px;font-weight:800;color:#0f172a;">Rp {{ number_format($article->payment->amount,0,',','.') }}</div>
          <div style="font-size:11px;font-family:'Courier New',monospace;color:#94a3b8;margin-top:2px;">{{ $article->payment->invoice_code }}</div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
