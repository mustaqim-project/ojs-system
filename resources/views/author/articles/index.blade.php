{{-- author/articles/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Portal Penulis'],['label'=>'Dasbor','href'=>route('author.dashboard')],['label'=>'Naskah Saya']]"/>
    <h1 class="ds-page-title">Naskah Saya</h1>
    <p class="ds-page-subtitle">{{ $articles->total() }} naskah dikirim</p>
  </div>
  <a href="{{ route('author.articles.create') }}" class="ds-btn ds-btn-pri">
    <i class="bi bi-plus-lg"></i> Kirim Manuskrip
  </a>
</div>

{{-- Action Alerts --}}
@foreach($articles->whereIn('status',['revision_required']) as $a)
<div class="ds-alert ds-alert-warn" data-aos="fade-up" style="margin-bottom:10px;">
  <i class="bi bi-pencil-square"></i>
  <div style="flex:1;min-width:0;">
    <strong>Revisi Diperlukan:</strong> {{ Str::limit($a->title, 60) }}
    <a href="{{ route('author.articles.revision',$a) }}" style="color:inherit;font-weight:700;margin-left:8px;text-decoration:underline;">Unggah revisi →</a>
  </div>
  <button class="ds-alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endforeach
@foreach($articles->whereIn('status',['waiting_payment','payment_uploaded']) as $a)
<div class="ds-alert ds-alert-info" data-aos="fade-up" style="margin-bottom:10px;">
  <i class="bi bi-credit-card-fill"></i>
  <div style="flex:1;min-width:0;">
    <strong>Pembayaran Diperlukan:</strong> {{ Str::limit($a->title, 55) }}
    <a href="{{ route('author.payments.show',$a) }}" style="color:inherit;font-weight:700;margin-left:8px;text-decoration:underline;">Bayar sekarang →</a>
  </div>
  <button class="ds-alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endforeach

<div class="ds-card" data-aos="fade-up" data-aos-delay="200">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Judul</th>
          <th>Jurnal</th>
          <th>Status</th>
          <th>Pembayaran</th>
          <th>Dikirim</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $article)
        <tr>
          <td>
            <a href="{{ route('author.articles.show',$article) }}"
               style="font-weight:600;color:var(--text-main);text-decoration:none;max-width:280px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               title="{{ $article->title }}">{{ Str::limit($article->title, 55) }}</a>
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $article->journal->abbreviation ?? '—' }}</td>
          <td><x-status-badge :status="$article->status" :label="$article->status_label"/></td>
          <td>
            @if($article->payment)
              <x-status-badge :status="$article->payment->status" :label="$article->payment->status_label"/>
            @else
              <span style="font-size:12px;color:var(--text-muted);">—</span>
            @endif
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $article->submitted_at?->format('d M Y') }}</td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
              <a href="{{ route('author.articles.show',$article) }}" class="ds-btn ds-btn-out ds-btn-xs">Detail</a>
              @if($article->status === 'revision_required')
                <a href="{{ route('author.articles.revision',$article) }}" class="ds-btn ds-btn-xs" style="background:var(--warning-bg);color:var(--warning);border-color:var(--warning);">
                  <i class="bi bi-pencil"></i> Revisi
                </a>
              @endif
              @if($article->needsPayment())
                <a href="{{ route('author.payments.show',$article) }}" class="ds-btn ds-btn-xs" style="background:#FAF5FF;color:#6B46C1;border-color:#6B46C1;">
                  <i class="bi bi-credit-card"></i> Bayar
                </a>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <x-ui.empty-state icon="bi-file-earmark-text" title="Belum ada kiriman naskah" description="Mulailah perjalanan penerbitan Anda dengan mengirimkan manuskrip pertama Anda.">
              <a href="{{ route('author.articles.create') }}" class="ds-btn ds-btn-pri" style="display:inline-flex;">
                <i class="bi bi-plus-lg"></i> Kirim Manuskrip Pertama
              </a>
            </x-ui.empty-state>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($articles->hasPages())
  <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--bg-app);">
    {{ $articles->links() }}
  </div>
  @endif
</div>

@endsection
