{{-- admin/issues/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr"><div><h2 class="pg-title">Kelola Issue</h2></div>
  <a href="{{ route('admin.issues.create') }}" class="btn-o btn-pri"><i class="bi bi-plus-lg"></i> Tambah Issue</a>
</div>
<div class="card-ojs fu fd2">
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>Jurnal</th><th>Volume / Nomor</th><th>Tahun</th><th>Status</th><th>Tgl Publish</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($issues as $issue)
        <tr>
          <td><div class="cell-pri">{{ $issue->journal->abbreviation ?? $issue->journal->title }}</div></td>
          <td><span class="cell-mute">Vol. {{ $issue->volume }} No. {{ $issue->number }}</span></td>
          <td><span class="cell-mute">{{ $issue->year }}</span></td>
          <td>
            @if($issue->status==='published')
              <span class="bx bx-published-issue">Published</span>
            @else
              <span class="bx bx-draft">Draft</span>
            @endif
          </td>
          <td><span class="cell-mute">{{ $issue->published_date?->format('d M Y') ?? '—' }}</span></td>
          <td>
            @if($issue->status==='draft')
            <form method="POST" action="{{ route('admin.issues.publish',$issue) }}" style="display:inline;">
              @csrf @method('PATCH')
              <button type="submit" onclick="return confirm('Publish issue ini?')" class="btn-o btn-suc btn-sm"><i class="bi bi-send-check"></i> Publish</button>
            </form>
            @else
              <span style="font-size:12px;color:var(--txt4);">Sudah publish</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-st"><div class="empty-icon"><i class="bi bi-collection"></i></div><div class="empty-title">Belum ada issue</div></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-ftr">{{ $issues->links() }}</div>
</div>
@endsection
