{{-- ════════════════════════════════════════
   author/articles/index.blade.php
════════════════════════════════════════ --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb"><a href="{{ route('author.dashboard') }}">Dashboard</a><span>›</span><span class="cur">Artikel Saya</span></div>
    <h2 class="pg-title">Artikel Saya</h2>
    <p class="pg-desc">{{ $articles->total() }} artikel yang telah Anda submit</p>
  </div>
  <a href="{{ route('author.articles.create') }}" class="btn-o btn-pri"><i class="bi bi-plus-lg"></i> Submit Baru</a>
</div>

{{-- Action alerts --}}
@foreach($articles->whereIn('status',['revision_required']) as $a)
<div class="alert-o a-warn mb-2 fu">
  <i class="bi bi-pencil-square"></i>
  <div style="flex:1;min-width:0;">
    <strong>Revisi diperlukan:</strong> {{ Str::limit($a->title,55) }}
    <a href="{{ route('author.articles.revision',$a) }}" style="color:inherit;font-weight:700;margin-left:8px;">Upload Revisi →</a>
  </div>
</div>
@endforeach
@foreach($articles->whereIn('status',['waiting_payment','payment_uploaded']) as $a)
<div class="alert-o a-info mb-2 fu">
  <i class="bi bi-credit-card-fill"></i>
  <div style="flex:1;min-width:0;">
    <strong>Menunggu pembayaran:</strong> {{ Str::limit($a->title,50) }}
    <a href="{{ route('author.payments.show',$a) }}" style="color:inherit;font-weight:700;margin-left:8px;">Bayar Sekarang →</a>
  </div>
</div>
@endforeach

<div class="card-ojs fu fd2">
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead>
        <tr>
          <th>Judul Artikel</th>
          <th>Jurnal</th>
          <th>Status</th>
          <th>Pembayaran</th>
          <th>Disubmit</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $article)
        <tr>
          <td>
            <div class="cell-pri" style="max-width:260px;">{{ Str::limit($article->title, 55) }}</div>
          </td>
          <td><span class="cell-mute">{{ $article->journal->abbreviation ?? '-' }}</span></td>
          <td>
            <span class="bx bx-{{ $article->status }}" style="font-size:10.5px;padding:3px 9px;">{{ $article->status_label }}</span>
          </td>
          <td>
            @if($article->payment)
              <span class="bx bx-{{ $article->payment->status }}" style="font-size:10.5px;padding:3px 9px;">{{ $article->payment->status_label }}</span>
            @else
              <span style="font-size:12px;color:var(--txt4);">—</span>
            @endif
          </td>
          <td><span class="cell-mute">{{ $article->submitted_at?->format('d M Y') }}</span></td>
          <td>
            <div style="display:flex;gap:5px;flex-wrap:wrap;">
              <a href="{{ route('author.articles.show', $article) }}" class="btn-o btn-out btn-sm">Detail</a>
              @if($article->status === 'revision_required')
                <a href="{{ route('author.articles.revision', $article) }}" class="btn-o btn-warn btn-sm">
                  <i class="bi bi-pencil"></i> Revisi
                </a>
              @endif
              @if($article->needsPayment())
                <a href="{{ route('author.payments.show', $article) }}" class="btn-o btn-sm" style="background:#7c3aed;color:#fff;border-color:#7c3aed;">
                  <i class="bi bi-credit-card"></i> Bayar
                </a>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <div class="empty-st">
              <div class="empty-icon"><i class="bi bi-file-earmark-text"></i></div>
              <div class="empty-title">Belum ada artikel</div>
              <div class="empty-desc">Mulai perjalanan publikasi Anda sekarang.</div>
              <a href="{{ route('author.articles.create') }}" class="btn-o btn-pri btn-sm" style="margin-top:16px;display:inline-flex;">
                <i class="bi bi-plus-lg"></i> Submit Artikel Pertama
              </a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-ftr">{{ $articles->links() }}</div>
</div>
@endsection
