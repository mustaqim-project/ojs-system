@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Semua Artikel']]"/>
    <h1 class="ds-page-title">Manajemen Artikel</h1>
    <p class="ds-page-subtitle">{{ $articles->total() }} manuskrip di dalam sistem</p>
  </div>
</div>

{{-- Status Filter Tabs --}}
<div class="ds-ftabs mb-4" data-aos="fade-up" style="flex-wrap:wrap;">
  <a href="{{ route('admin.articles.index') }}" class="ds-ftab {{ !$status ? 'active' : '' }}">Semua</a>
  @foreach([
    'submitted'       => 'Baru',
    'under_review'    => 'Sedang Ditinjau',
    'revision_required'=> 'Butuh Revisi',
    'accepted'        => 'Diterima',
    'waiting_payment' => 'Menunggu Pembayaran',
    'payment_uploaded'=> 'Verifikasi Pembayaran',
    'paid'            => 'Lunas',
    'published'       => 'Diterbitkan',
    'rejected'        => 'Ditolak',
  ] as $k => $l)
  <a href="{{ route('admin.articles.index',['status'=>$k]) }}" class="ds-ftab {{ $status === $k ? 'active' : '' }}">{{ $l }}</a>
  @endforeach
</div>

<div class="ds-card" data-aos="fade-up" data-aos-delay="200">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Judul</th>
          <th>Jurnal</th>
          <th>Penulis</th>
          <th>Status</th>
          <th>Pembayaran</th>
          <th>Dikirim</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $a)
        <tr>
          <td>
            <a href="{{ route('admin.articles.show',$a) }}"
               style="font-weight:600;color:var(--text-main);text-decoration:none;max-width:240px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               title="{{ $a->title }}">
              {{ $a->title }}
            </a>
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $a->journal->abbreviation ?? '—' }}</td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $a->author->name }}</td>
          <td><x-status-badge :status="$a->status" :label="$a->status_label"/></td>
          <td>
            @if($a->payment)
              <x-status-badge :status="$a->payment->status" :label="$a->payment->status_label"/>
            @else
              <span style="font-size:12px;color:var(--text-muted);">—</span>
            @endif
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $a->submitted_at?->format('d M Y') }}</td>
          <td>
            <a href="{{ route('admin.articles.show',$a) }}" class="ds-btn ds-btn-out ds-btn-xs">
              Lihat <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <x-ui.empty-state icon="bi-file-earmark-text" title="Tidak ada artikel ditemukan" description="Tidak ada manuskrip yang cocok dengan filter saat ini."/>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($articles->hasPages())
  <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--bg-app);">
    {{ $articles->withQueryString()->links() }}
  </div>
  @endif
</div>

@endsection
