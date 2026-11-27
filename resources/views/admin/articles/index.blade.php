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
  <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px solid var(--border);background:var(--bg-app);font-size:12px;color:var(--text-muted);">
    <span><i class="bi bi-arrows-move me-1 text-primary"></i> Seret baris (<i class="bi bi-grip-vertical"></i>) untuk mengubah urutan tampil artikel</span>
    <span id="reorderStatus" style="font-weight:600;display:none;"></span>
  </div>
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th style="width:36px;"></th>
          <th>Judul</th>
          <th>Jurnal</th>
          <th>Halaman</th>
          <th>Penulis</th>
          <th>Status</th>
          <th>Pembayaran</th>
          <th>Dikirim</th>
          <th></th>
        </tr>
      </thead>
      <tbody id="sortableArticles">
        @forelse($articles as $a)
        <tr data-id="{{ $a->id }}" draggable="true" class="article-sort-row">
          <td class="drag-handle" style="cursor:grab;color:var(--text-muted);text-align:center;" title="Seret untuk ubah urutan">
            <i class="bi bi-grip-vertical"></i>
          </td>
          <td>
            <a href="{{ route('admin.articles.show',$a) }}"
               style="font-weight:600;color:var(--text-main);text-decoration:none;max-width:240px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               title="{{ $a->title }}">
              {{ $a->title }}
            </a>
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $a->journal->abbreviation ?? '—' }}</td>
          <td style="font-size:13px;color:var(--text-muted);white-space:nowrap;">{{ $a->pages_range ?? '—' }}</td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $a->author?->name ?? 'User Terhapus' }}</td>
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
          <td colspan="9">
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

@push('scripts')
<style>
  .article-sort-row.dragging {
    opacity: 0.45;
    background: var(--primary-light, #eff6ff);
  }
  .article-sort-row.drag-over {
    border-top: 2px solid var(--primary, #2563eb);
  }
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.getElementById('sortableArticles');
  const statusEl = document.getElementById('reorderStatus');
  if (!tbody) return;

  let draggedRow = null;

  const getRows = () => Array.from(tbody.querySelectorAll('tr.article-sort-row'));

  const showStatus = (text, color) => {
    if (!statusEl) return;
    statusEl.textContent = text;
    statusEl.style.color = color;
    statusEl.style.display = 'inline';
  };

  const saveOrder = async () => {
    const ids = getRows().map(r => r.dataset.id);
    if (!ids.length) return;

    showStatus('Menyimpan urutan...', 'var(--text-muted)');
    try {
      const res = await fetch(@json(route('admin.articles.reorder')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': @json(csrf_token()),
        },
        body: JSON.stringify({ ids }),
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      showStatus('Urutan disimpan', 'var(--success, #16a34a)');
      setTimeout(() => { statusEl.style.display = 'none'; }, 2000);
    } catch (e) {
      showStatus('Gagal menyimpan urutan', 'var(--danger, #dc2626)');
    }
  };

  tbody.addEventListener('dragstart', (e) => {
    const row = e.target.closest('tr.article-sort-row');
    if (!row) return;
    draggedRow = row;
    row.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', row.dataset.id);
  });

  tbody.addEventListener('dragover', (e) => {
    e.preventDefault();
    const targetRow = e.target.closest('tr.article-sort-row');
    if (!targetRow || targetRow === draggedRow) return;

    getRows().forEach(r => r.classList.remove('drag-over'));
    const rect = targetRow.getBoundingClientRect();
    const after = (e.clientY - rect.top) > (rect.height / 2);
    if (after) {
      targetRow.after(draggedRow);
    } else {
      targetRow.before(draggedRow);
    }
  });

  tbody.addEventListener('dragend', () => {
    if (draggedRow) {
      draggedRow.classList.remove('dragging');
      draggedRow = null;
    }
    getRows().forEach(r => r.classList.remove('drag-over'));
    saveOrder();
  });
});
</script>
@endpush
