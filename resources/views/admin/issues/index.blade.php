{{-- admin/issues/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Terbitan']]"/>
    <h1 class="ds-page-title">Manajemen Terbitan</h1>
    <p class="ds-page-subtitle">Kelola volume dan terbitan jurnal</p>
  </div>
  <a href="{{ route('admin.issues.create') }}" class="ds-btn ds-btn-pri">
    <i class="bi bi-plus-lg"></i> Tambah Terbitan
  </a>
</div>

<div class="ds-card" data-aos="fade-up" data-aos-delay="200">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Jurnal</th>
          <th>Volume / Nomor</th>
          <th>Tahun</th>
          <th>Status</th>
          <th>Tanggal Terbit</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($issues as $issue)
        <tr>
          <td>
            <div style="font-weight:600;color:var(--text-main);">{{ $issue->journal->abbreviation ?? $issue->journal->title }}</div>
            <div style="font-size:12px;color:var(--text-muted);">{{ $issue->journal->title }}</div>
          </td>
          <td style="font-weight:600;color:var(--text-main);">Vol. {{ $issue->volume }} No. {{ $issue->number }}</td>
          <td style="font-size:14px;color:var(--text-muted);">{{ $issue->year }}</td>
          <td>
            @if($issue->status === 'published')
              <x-status-badge status="published" label="Diterbitkan"/>
            @else
              <x-status-badge status="draft" label="Draf"/>
            @endif
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $issue->published_date?->format('d M Y') ?? '—' }}</td>
          <td>
            @if($issue->status === 'draft')
              <form method="POST" action="{{ route('admin.issues.publish',$issue) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="ds-btn ds-btn-suc ds-btn-sm"
                        onclick="return confirm('Terbitkan nomor terbitan ini? Ini akan membuatnya terlihat secara publik.')">
                  <i class="bi bi-send-check"></i> Terbitkan
                </button>
              </form>
            @else
              <span style="font-size:12px;color:var(--success);font-weight:600;"><i class="bi bi-check-circle me-1"></i>Diterbitkan</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <x-ui.empty-state icon="bi-collection" title="Belum ada terbitan" description="Buat terbitan pertama untuk mulai menempatkan artikel.">
              <a href="{{ route('admin.issues.create') }}" class="ds-btn ds-btn-pri" style="display:inline-flex;">
                <i class="bi bi-plus-lg"></i> Tambah Terbitan
              </a>
            </x-ui.empty-state>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($issues->hasPages())
  <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--bg-app);">
    {{ $issues->links() }}
  </div>
  @endif
</div>

@endsection
