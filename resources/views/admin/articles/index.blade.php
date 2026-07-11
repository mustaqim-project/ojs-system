@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div><h2 class="pg-title">Semua Artikel</h2><p class="pg-desc">{{ $articles->total() }} artikel di sistem</p></div>
</div>

<div class="ftabs mb-4 fu">
  <a href="{{ route('admin.articles.index') }}" class="ftab {{ !$status?'active':'' }}">Semua</a>
  @foreach(['submitted'=>'Baru','under_review'=>'Review','revision_required'=>'Revisi','accepted'=>'Diterima','waiting_payment'=>'Tunggu Bayar','payment_uploaded'=>'Bukti Terkirim','paid'=>'Lunas','published'=>'Published','rejected'=>'Ditolak'] as $k=>$l)
  <a href="{{ route('admin.articles.index',['status'=>$k]) }}" class="ftab {{ $status===$k?'active':'' }}">{{ $l }}</a>
  @endforeach
</div>

<div class="card-ojs fu fd2">
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>Artikel</th><th>Jurnal</th><th>Author</th><th>Status</th><th>Pembayaran</th><th>Disubmit</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($articles as $a)
        <tr>
          <td><div class="cell-pri" style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $a->title }}</div></td>
          <td><span class="cell-mute">{{ $a->journal->abbreviation??'-' }}</span></td>
          <td><span class="cell-mute">{{ $a->author->name }}</span></td>
          <td><span class="bx bx-{{ $a->status }}" style="font-size:10px;padding:2px 8px;">{{ $a->status_label }}</span></td>
          <td>
            @if($a->payment)
              <span class="bx bx-{{ $a->payment->status }}" style="font-size:10px;padding:2px 8px;">{{ $a->payment->status_label }}</span>
            @else<span style="font-size:12px;color:var(--txt4);">—</span>@endif
          </td>
          <td><span class="cell-mute">{{ $a->submitted_at?->format('d M Y') }}</span></td>
          <td><a href="{{ route('admin.articles.show',$a) }}" class="btn-o btn-out btn-sm">Detail</a></td>
        </tr>
        @empty
        <tr><td colspan="7"><div class="empty-st"><div class="empty-icon"><i class="bi bi-file-earmark-text"></i></div><div class="empty-title">Tidak ada artikel</div></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-ftr">{{ $articles->withQueryString()->links() }}</div>
</div>
@endsection
