@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb"><a href="{{ route('author.dashboard') }}">Dashboard</a><span>›</span><a href="{{ route('author.articles.index') }}">Artikel</a><span>›</span><span class="cur">Detail</span></div>
    <h2 class="pg-title" style="font-size:17px;max-width:600px;line-height:1.3;">{{ $article->title }}</h2>
  </div>
  @php $cls='bx-'.$article->status; @endphp
  <span class="bx {{ $cls }}" style="font-size:12px;padding:5px 14px;">{{ $article->status_label }}</span>
</div>

{{-- Action alerts --}}
@if($article->status==='revision_required')
<div class="alert-o a-warn fu">
  <i class="bi bi-exclamation-triangle-fill"></i>
  <div><strong>Revisi Diperlukan.</strong> {{ $article->editor_note ? 'Lihat catatan editor di bawah.' : '' }}
    <a href="{{ route('author.articles.revision',$article) }}" style="color:inherit;font-weight:700;margin-left:8px;">Upload Revisi →</a>
  </div>
</div>
@endif
@if($article->needsPayment())
<div class="alert-o a-info fu">
  <i class="bi bi-credit-card-fill"></i>
  <div><strong>Artikel Diterima!</strong> Lakukan pembayaran APC untuk melanjutkan ke proses publikasi.
    <a href="{{ route('author.payments.show',$article) }}" style="color:inherit;font-weight:700;margin-left:8px;">Ke Halaman Invoice →</a>
  </div>
</div>
@endif

<div class="row g-3">
  <div class="col-12 col-lg-8">
    {{-- Detail --}}
    <div class="card-ojs fu fd1">
      <div class="card-hdr"><span class="card-title">Informasi Artikel</span>
        @if($article->manuscript_file)
        <a href="{{ asset('storage/'.$article->manuscript_file) }}" target="_blank" class="btn-o btn-out btn-sm"><i class="bi bi-download"></i> Manuskrip</a>
        @endif
      </div>
      <div>
        <div class="info-row"><span class="info-key">Jurnal</span><span class="info-val">{{ $article->journal->title }}</span></div>
        <div class="info-row"><span class="info-key">Bahasa</span><span class="info-val">{{ strtoupper($article->language) }}</span></div>
        <div class="info-row"><span class="info-key">Abstrak</span><span class="info-val" style="line-height:1.7;color:#475569;">{{ $article->abstract }}</span></div>
        <div class="info-row">
          <span class="info-key">Kata Kunci</span>
          <span class="info-val" style="display:flex;flex-wrap:wrap;gap:5px;">
            @foreach($article->keywords_array as $kw)
            <span style="font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:5px;border:1px solid #e2e8f0;">{{ $kw }}</span>
            @endforeach
          </span>
        </div>
        <div class="info-row"><span class="info-key">Disubmit</span><span class="info-val">{{ $article->submitted_at?->format('d M Y H:i') }}</span></div>
        @if($article->accepted_at)<div class="info-row"><span class="info-key">Diterima</span><span class="info-val">{{ $article->accepted_at->format('d M Y H:i') }}</span></div>@endif
        @if($article->published_at)<div class="info-row"><span class="info-key">Dipublish</span><span class="info-val">{{ $article->published_at->format('d M Y H:i') }}</span></div>@endif
      </div>
    </div>

    {{-- Editor note --}}
    @if($article->editor_note)
    <div class="alert-o a-warn fu fd2">
      <i class="bi bi-chat-left-text-fill"></i>
      <div><strong>Catatan Editor:</strong><br/>{{ $article->editor_note }}</div>
    </div>
    @endif

    {{-- Review results --}}
    @if($article->reviews->where('status','completed')->count())
    <div class="card-ojs fu fd3">
      <div class="card-hdr"><span class="card-title">Hasil Review</span></div>
      <div>
        @foreach($article->reviews->where('status','completed') as $review)
        <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <span style="font-size:12px;color:#94a3b8;font-weight:600;">Reviewer {{ $loop->iteration }}</span>
            @if($review->recommendation)
            @php $rc='bx-'.$review->recommendation; @endphp
            <span class="bx {{ $rc }}">{{ $review->recommendation_label }}</span>
            @endif
          </div>
          @if($review->comments_to_author)
          <p style="font-size:13px;color:#475569;line-height:1.7;margin:0;">{{ $review->comments_to_author }}</p>
          @endif
          @if($review->average_score)
          <div style="font-size:11px;color:#94a3b8;margin-top:8px;">Skor rata-rata: <strong style="color:#0f172a;">{{ $review->average_score }}/5</strong></div>
          @endif
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Payment summary --}}
    @if($article->payment)
    <div class="card-ojs fu fd4">
      <div class="card-hdr">
        <span class="card-title">Pembayaran</span>
        <a href="{{ route('author.payments.show',$article) }}" class="btn-o btn-ghost btn-sm">Lihat Detail <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
      <div style="padding:20px;display:flex;align-items:center;gap:16px;">
        <div>
          <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">Invoice</div>
          <div style="font-size:13px;font-weight:700;font-family:'Courier New',monospace;color:#0f172a;">{{ $article->payment->invoice_code }}</div>
        </div>
        <div style="width:1px;height:32px;background:#e2e8f0;"></div>
        <div>
          <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">Nominal</div>
          <div style="font-size:16px;font-weight:800;color:#0f172a;">{{ $article->payment->formatted_amount }}</div>
        </div>
        <div style="margin-left:auto;">
          @php $pc='bx-'.$article->payment->status; @endphp
          <span class="bx {{ $pc }}">{{ $article->payment->status_label }}</span>
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- Sidebar: Timeline --}}
  <div class="col-12 col-lg-4">
    <div class="card-ojs fu fd2" style="position:sticky;top:80px;">
      <div class="card-hdr"><span class="card-title">Progress Artikel</span></div>
      <div style="padding:20px;">
        @php
        $stages=[
          ['key'=>'submitted','label'=>'Disubmit','sub'=>'Manuskrip diterima sistem'],
          ['key'=>'under_review','label'=>'Under Review','sub'=>'Reviewer sedang menilai'],
          ['key'=>'accepted','label'=>'Diterima','sub'=>'Editor menerima artikel'],
          ['key'=>'waiting_payment','label'=>'Pembayaran','sub'=>'APC menunggu dibayar'],
          ['key'=>'paid','label'=>'Lunas','sub'=>'Pembayaran terverifikasi'],
          ['key'=>'published','label'=>'Dipublish','sub'=>'Artikel tampil publik'],
        ];
        $order=['submitted','under_review','revision_required','accepted','rejected','waiting_payment','payment_uploaded','payment_verification','paid','published'];
        $ci=array_search($article->status,$order);
        @endphp
        <div class="ojs-tl">
          @foreach($stages as $i=>$s)
          @php
            $si=array_search($s['key'],$order);
            $done=$ci>$si;$active=$ci===$si;
          @endphp
          <div class="tl-item">
            <div class="tl-dot {{ $done?'tl-done':($active?'tl-active':'tl-todo') }}">
              @if($done)<i class="bi bi-check" style="font-size:11px;"></i>
              @elseif($active)<div style="width:8px;height:8px;border-radius:50%;background:#fff;"></div>
              @else<span style="font-size:9px;">{{ $i+1 }}</span>@endif
            </div>
            <div>
              <div class="tl-label" style="{{ $active?'color:var(--acc);':($done?'':'color:var(--txt3);') }}">{{ $s['label'] }}</div>
              <div class="tl-sub">{{ $s['sub'] }}</div>
            </div>
          </div>
          @endforeach

          @if($article->status==='rejected')
          <div class="tl-item">
            <div class="tl-dot" style="background:#fef2f2;border:2px solid var(--red);color:var(--red);"><i class="bi bi-x" style="font-size:12px;"></i></div>
            <div><div class="tl-label" style="color:var(--red);">Ditolak</div><div class="tl-sub">Artikel tidak diterima</div></div>
          </div>
          @endif
        </div>

        @if($article->status==='revision_required')
        <a href="{{ route('author.articles.revision',$article) }}" class="btn-o btn-warn w-100 justify-content-center mt-4">
          <i class="bi bi-pencil-square"></i> Upload Revisi
        </a>
        @endif
        @if($article->needsPayment())
        <a href="{{ route('author.payments.show',$article) }}" class="btn-o btn-pri w-100 justify-content-center mt-4" style="background:#7c3aed;border-color:#7c3aed;">
          <i class="bi bi-credit-card"></i> Bayar Sekarang
        </a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
