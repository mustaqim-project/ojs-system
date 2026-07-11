@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb"><a href="{{ route('reviewer.dashboard') }}">Dashboard</a><span>›</span><a href="{{ route('reviewer.reviews.index') }}">Tugas Review</a><span>›</span><span class="cur">Detail</span></div>
    <h2 class="pg-title">Form Review</h2>
  </div>
  @php $sc=['pending'=>'bx-yellow','in_progress'=>'bx-blue','completed'=>'bx-green','declined'=>'bx-red'];
  $cl=$sc[$review->status]??'bx-gray'; @endphp
  <span class="bx {{ $cl }}" style="font-size:12px;padding:5px 14px;">{{ ucfirst(str_replace('_',' ',$review->status)) }}</span>
</div>

<div class="row g-3">
  {{-- Main: Article + Form --}}
  <div class="col-12 col-lg-8">

    {{-- Article summary --}}
    <div class="card-ojs fu fd1">
      <div class="card-hdr">
        <span class="card-title">Artikel yang Direview</span>
        <div class="d-flex gap-2">
          <a href="{{ asset('storage/'.$review->article->manuscript_file) }}" target="_blank" class="btn-o btn-pri btn-sm">
            <i class="bi bi-download"></i> Download Manuskrip
          </a>
          @if($review->article->revision_file)
          <a href="{{ asset('storage/'.$review->article->revision_file) }}" target="_blank" class="btn-o btn-warn btn-sm">
            <i class="bi bi-download"></i> Revisi
          </a>
          @endif
        </div>
      </div>
      <div class="card-body-p">
        <div style="display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
          <span style="font-size:11px;font-weight:600;color:#2563eb;background:#eff6ff;padding:3px 10px;border-radius:20px;">{{ $review->article->journal->title }}</span>
          <span style="font-size:11px;font-weight:600;color:#047857;background:#ecfdf5;padding:3px 10px;border-radius:20px;">{{ strtoupper($review->article->language) }}</span>
        </div>
        <h3 style="font-size:16px;font-weight:700;color:#0f172a;letter-spacing:-.02em;margin-bottom:12px;line-height:1.4;">{{ $review->article->title }}</h3>
        <p style="font-size:13px;color:#475569;line-height:1.75;margin-bottom:14px;">{{ $review->article->abstract }}</p>
        @if($review->article->keywords)
        <div style="display:flex;flex-wrap:wrap;gap:5px;">
          @foreach($review->article->keywords_array as $kw)
          <span style="font-size:11px;background:#f1f5f9;color:#64748b;padding:3px 9px;border-radius:5px;border:1px solid #e2e8f0;">{{ $kw }}</span>
          @endforeach
        </div>
        @endif
      </div>
      @if($review->due_date)
      <div class="card-ftr d-flex align-items-center gap-2">
        @php $ov=$review->due_date->isPast()&&$review->status!=='completed'; @endphp
        <i class="bi bi-clock" style="color:{{ $ov?'var(--red)':'var(--txt3)' }};"></i>
        <span style="font-size:12px;color:{{ $ov?'var(--red)':'var(--txt2)' }};font-weight:{{ $ov?'700':'400' }};">
          Batas review: {{ $review->due_date->format('d M Y') }}
          {{ $ov ? ' (Terlambat!)' : '' }}
        </span>
      </div>
      @endif
    </div>

    {{-- Accept/Decline (pending) --}}
    @if($review->status === 'pending')
    <div class="row g-3 mt-0 fu fd2">
      <div class="col-md-6">
        <div style="background:var(--green-bg);border:1px solid var(--green-b);border-radius:var(--r);padding:20px;">
          <h4 style="font-size:13px;font-weight:700;color:var(--green);margin-bottom:8px;"><i class="bi bi-check-circle me-2"></i>Terima Tugas</h4>
          <p style="font-size:12px;color:#166534;margin-bottom:14px;line-height:1.6;">Saya bersedia melakukan review artikel ini sesuai batas waktu.</p>
          <form method="POST" action="{{ route('reviewer.reviews.accept',$review) }}">
            @csrf
            <button type="submit" class="btn-o btn-suc w-100 justify-content-center"><i class="bi bi-check-lg"></i> Terima Tugas Review</button>
          </form>
        </div>
      </div>
      <div class="col-md-6">
        <div style="background:var(--red-bg);border:1px solid var(--red-b);border-radius:var(--r);padding:20px;">
          <h4 style="font-size:13px;font-weight:700;color:var(--red);margin-bottom:8px;"><i class="bi bi-x-circle me-2"></i>Tolak Tugas</h4>
          <p style="font-size:12px;color:#991b1b;margin-bottom:14px;line-height:1.6;">Tidak dapat mengerjakan review ini (konflik jadwal, dll).</p>
          <form method="POST" action="{{ route('reviewer.reviews.decline',$review) }}">
            @csrf
            <button type="submit" onclick="return confirm('Tolak tugas review ini?')" class="btn-o btn-danger w-100 justify-content-center"><i class="bi bi-x-lg"></i> Tolak Tugas Review</button>
          </form>
        </div>
      </div>
    </div>
    @endif

    {{-- Review Form (in_progress / accepted) --}}
    @if(in_array($review->status,['in_progress','accepted']))
    <div class="card-ojs fu fd3 mt-0">
      <div class="card-hdr">
        <span class="card-title"><i class="bi bi-pencil-square me-2" style="color:var(--acc);"></i>Submit Review</span>
      </div>
      <div class="card-body-p">
        <form method="POST" action="{{ route('reviewer.reviews.submit',$review) }}" enctype="multipart/form-data">
          @csrf

          {{-- Recommendation --}}
          <div class="f-group">
            <label class="lbl">Rekomendasi <span class="req">*</span></label>
            <div class="row g-2">
              @php $recs=[
                'accept' =>['label'=>'Accept','sub'=>'Artikel layak publish tanpa revisi','bg'=>'var(--green-bg)','border'=>'var(--green-b)','ic'=>'var(--green)','icon'=>'bi-check-circle-fill'],
                'minor'  =>['label'=>'Minor Revision','sub'=>'Perlu sedikit perbaikan kecil','bg'=>'var(--yllw-bg)','border'=>'var(--yllw-b)','ic'=>'var(--yllw)','icon'=>'bi-arrow-clockwise'],
                'major'  =>['label'=>'Major Revision','sub'=>'Perlu revisi substansial','bg'=>'var(--orng-bg)','border'=>'var(--orng-b)','ic'=>'var(--orng)','icon'=>'bi-exclamation-circle-fill'],
                'reject' =>['label'=>'Reject','sub'=>'Artikel tidak layak publish','bg'=>'var(--red-bg)','border'=>'var(--red-b)','ic'=>'var(--red)','icon'=>'bi-x-circle-fill'],
              ]; @endphp
              @foreach($recs as $val=>$r)
              <div class="col-6">
                <label style="display:flex;align-items:start;gap:10px;padding:12px;border:2px solid {{ old('recommendation')===$val?$r['border']:'var(--brd)' }};border-radius:8px;cursor:pointer;background:{{ old('recommendation')===$val?$r['bg']:'var(--surf)' }};transition:all .15s;"
                       onmouseover="this.style.borderColor='{{ $r['border'] }}'" onmouseout="if(!this.querySelector('input').checked){this.style.borderColor='var(--brd)';this.style.background='var(--surf)'}">
                  <input type="radio" name="recommendation" value="{{ $val }}" {{ old('recommendation')===$val?'checked':'' }} required style="margin-top:2px;accent-color:{{ $r['ic'] }};"
                         onchange="document.querySelectorAll('.rec-label').forEach(l=>{l.style.borderColor='var(--brd)';l.style.background='var(--surf)'});this.closest('label').style.borderColor='{{ $r['border'] }}';this.closest('label').style.background='{{ $r['bg'] }}'"/>
                  <div>
                    <div style="font-size:13px;font-weight:700;color:{{ $r['ic'] }};display:flex;align-items:center;gap:6px;"><i class="{{ $r['icon'] }}"></i>{{ $r['label'] }}</div>
                    <div style="font-size:11px;color:var(--txt2);margin-top:2px;">{{ $r['sub'] }}</div>
                  </div>
                </label>
              </div>
              @endforeach
            </div>
            @error('recommendation')<div class="f-err">{{ $message }}</div>@enderror
          </div>

          {{-- Scores --}}
          <div class="f-group">
            <label class="lbl">Penilaian <span class="hint">(1–5, opsional)</span></label>
            <div style="background:var(--canvas);border:1px solid var(--brd);border-radius:8px;padding:16px;">
              <div class="row g-3">
                @foreach(['originality_score'=>'Orisinalitas','methodology_score'=>'Metodologi','relevance_score'=>'Relevansi & Kontribusi','writing_score'=>'Kualitas Penulisan'] as $field=>$lbl)
                <div class="col-6">
                  <label class="lbl" style="font-size:11px;margin-bottom:4px;">{{ $lbl }}</label>
                  <div style="display:flex;gap:4px;">
                    @for($s=1;$s<=5;$s++)
                    <label style="cursor:pointer;">
                      <input type="radio" name="{{ $field }}" value="{{ $s }}" {{ old($field)==$s?'checked':'' }} style="display:none;"/>
                      <i class="bi bi-star-fill" style="font-size:18px;color:{{ old($field)>=$s?'#f59e0b':'#e2e8f0' }};transition:color .1s;" id="star_{{ $field }}_{{ $s }}"></i>
                    </label>
                    @endfor
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Comments to author --}}
          <div class="f-group">
            <label class="lbl">Komentar untuk Penulis <span class="req">*</span> <span class="hint">Minimal 50 karakter</span></label>
            <textarea name="comments_to_author" class="txta {{ $errors->has('comments_to_author')?'is-invalid':'' }}"
                      rows="7" required
                      placeholder="Berikan komentar konstruktif tentang isi artikel, metodologi, relevansi, dan kualitas penulisan. Komentar ini akan dibagikan kepada penulis...">{{ old('comments_to_author') }}</textarea>
            @error('comments_to_author')<div class="f-err">{{ $message }}</div>@enderror
          </div>

          {{-- Confidential comments --}}
          <div class="f-group">
            <label class="lbl">Komentar Konfidensial untuk Editor <span class="hint">(tidak terlihat penulis)</span></label>
            <textarea name="comments_to_editor" class="txta" rows="3"
                      placeholder="Catatan rahasia khusus untuk editor (kekhawatiran, konflik kepentingan, dll)...">{{ old('comments_to_editor') }}</textarea>
          </div>

          {{-- Review file --}}
          <div class="f-group">
            <label class="lbl">File Review Anotasi <span class="hint">(opsional)</span></label>
            <input class="file-inp" type="file" name="review_file" accept=".pdf,.doc,.docx"/>
            <div class="f-hint-txt"><i class="bi bi-info-circle me-1"></i>Upload manuskrip dengan anotasi jika ada. PDF/DOC/DOCX, maks 10MB.</div>
          </div>

          {{-- Warning --}}
          <div class="alert-o a-warn" style="margin-bottom:20px;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span style="font-size:13px;">Review yang sudah disubmit <strong>tidak dapat diubah</strong>. Pastikan semua informasi sudah benar.</span>
          </div>

          <button type="submit"
                  onclick="return confirm('Submit review? Tindakan ini tidak dapat dibatalkan.')"
                  class="btn-o btn-pri btn-lg w-100 justify-content-center">
            <i class="bi bi-send-fill"></i> Submit Review Sekarang
          </button>
        </form>
      </div>
    </div>
    @endif

    {{-- Completed view --}}
    @if($review->status === 'completed')
    <div class="card-ojs fu fd2 mt-0">
      <div class="card-hdr">
        <span class="card-title"><i class="bi bi-check-circle-fill me-2" style="color:var(--green);"></i>Review Selesai</span>
        @if($review->recommendation)
          @php $rc=['accept'=>'bx-green','minor'=>'bx-yellow','major'=>'bx-orange','reject'=>'bx-red'][$review->recommendation]??'bx-gray'; @endphp
          <span class="bx {{ $rc }}">{{ $review->recommendation_label }}</span>
        @endif
      </div>
      <div class="card-body-p">
        @if($review->average_score)
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--brd);">
          <span style="font-size:12px;color:var(--txt2);">Skor Rata-rata:</span>
          <div class="score-stars">
            @for($s=1;$s<=5;$s++)<i class="bi bi-star-fill {{ $s<=$review->average_score?'star-on':'star-off' }}"></i>@endfor
          </div>
          <span style="font-size:13px;font-weight:700;color:var(--txt);">{{ $review->average_score }}/5</span>
        </div>
        @endif
        @if($review->comments_to_author)
        <div style="margin-bottom:12px;">
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--txt2);margin-bottom:8px;">Komentar untuk Penulis:</div>
          <div style="background:var(--canvas);border-radius:8px;padding:14px;font-size:13px;color:var(--txt2);line-height:1.75;">{{ $review->comments_to_author }}</div>
        </div>
        @endif
        <div style="font-size:12px;color:var(--txt3);">Diselesaikan: {{ $review->completed_at?->format('d M Y H:i') }}</div>
      </div>
    </div>
    @endif

  </div>

  {{-- Sidebar --}}
  <div class="col-12 col-lg-4">
    <div style="position:sticky;top:80px;display:flex;flex-direction:column;gap:12px;">

      {{-- Article info --}}
      <div class="card-ojs fu fd1">
        <div class="card-hdr"><span class="card-title">Informasi Tugas</span></div>
        <div>
          <div class="info-row"><span class="info-key">Jurnal</span><span class="info-val" style="font-size:12px;">{{ $review->article->journal->title }}</span></div>
          <div class="info-row"><span class="info-key">Author</span><span class="info-val" style="font-size:12px;">{{ $review->article->author->name }}</span></div>
          @if($review->due_date)
          <div class="info-row">
            <span class="info-key">Batas Waktu</span>
            <span class="info-val" style="font-size:12px;color:{{ $review->due_date->isPast()&&$review->status!=='completed'?'var(--red)':'var(--txt)' }};font-weight:600;">
              {{ $review->due_date->format('d M Y') }}
            </span>
          </div>
          @endif
          <div class="info-row"><span class="info-key">Diterima</span><span class="info-val" style="font-size:12px;">{{ $review->created_at->format('d M Y') }}</span></div>
          @if($review->completed_at)<div class="info-row"><span class="info-key">Selesai</span><span class="info-val" style="font-size:12px;">{{ $review->completed_at->format('d M Y') }}</span></div>@endif
        </div>
      </div>

      {{-- Scoring guide --}}
      @if(in_array($review->status,['in_progress','accepted']))
      <div class="card-ojs fu fd3">
        <div class="card-hdr"><span class="card-title">Panduan Penilaian</span></div>
        <div class="card-body-p">
          @foreach(['5 — Sangat Baik / Excellent','4 — Baik / Good','3 — Cukup / Fair','2 — Kurang / Poor','1 — Sangat Kurang / Very Poor'] as $g)
          <div style="font-size:12px;color:var(--txt2);padding:3px 0;border-bottom:1px solid var(--canvas);">{{ $g }}</div>
          @endforeach
        </div>
      </div>
      @endif

    </div>
  </div>
</div>

@push('scripts')
<script>
// Interactive star rating
document.querySelectorAll('.f-group .row.g-3 label').forEach(lbl => {
  lbl.addEventListener('mouseover', () => {
    const input = lbl.querySelector('input[type=radio]');
    if (!input) return;
    const name = input.name;
    const val  = parseInt(input.value);
    document.querySelectorAll(`input[name="${name}"]`).forEach((inp, i) => {
      const star = inp.closest('label').querySelector('i');
      if (star) star.style.color = (i < val) ? '#f59e0b' : '#e2e8f0';
    });
  });
  lbl.addEventListener('click', () => {
    const input = lbl.querySelector('input[type=radio]');
    if (!input) return;
    const name = input.name;
    const val  = parseInt(input.value);
    document.querySelectorAll(`input[name="${name}"]`).forEach((inp, i) => {
      const star = inp.closest('label').querySelector('i');
      if (star) star.style.color = (i < val) ? '#f59e0b' : '#e2e8f0';
    });
  });
});
</script>
@endpush
@endsection
