{{-- admin/journals/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr"><div><h2 class="pg-title">Kelola Jurnal</h2></div>
  <a href="{{ route('admin.journals.create') }}" class="btn-o btn-pri"><i class="bi bi-plus-lg"></i> Tambah Jurnal</a>
</div>
<div class="card-ojs fu fd2">
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>Jurnal</th><th>ISSN</th><th>Editor</th><th>Artikel</th><th>Frekuensi</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($journals as $j)
        <tr class="{{ $j->trashed()?'opacity-50':'' }}">
          <td><div class="cell-pri">{{ $j->title }}</div>@if($j->abbreviation)<span style="font-size:10px;font-family:'Courier New',monospace;background:#f1f5f9;color:#64748b;padding:2px 7px;border-radius:4px;">{{ $j->abbreviation }}</span>@endif</td>
          <td><span class="cell-mute" style="font-family:'Courier New',monospace;">{{ $j->issn_print??$j->issn_online??'—' }}</span></td>
          <td><span class="cell-mute">{{ $j->editor?->name??'—' }}</span></td>
          <td><span class="cell-mute">{{ $j->articles_count }}</span></td>
          <td><span class="cell-mute" style="text-transform:capitalize;">{{ $j->frequency }}</span></td>
          <td>@if($j->trashed())<span style="font-size:11px;color:var(--red);">Dihapus</span>@elseif($j->is_active)<span style="font-size:11px;color:var(--green);font-weight:600;">Aktif</span>@else<span style="font-size:11px;color:var(--txt3);">Nonaktif</span>@endif</td>
          <td>@if(!$j->trashed())<a href="{{ route('admin.journals.edit',$j) }}" class="btn-o btn-out btn-sm"><i class="bi bi-pencil"></i></a>@endif</td>
        </tr>
        @empty
        <tr><td colspan="7"><div class="empty-st"><div class="empty-icon"><i class="bi bi-journals"></i></div><div class="empty-title">Belum ada jurnal</div></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-ftr">{{ $journals->links() }}</div>
</div>
@endsection
