{{-- admin/payments/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr"><div><h2 class="pg-title">Verifikasi Pembayaran</h2><p class="pg-desc">{{ $payments->total() }} total transaksi</p></div></div>
<div class="ftabs mb-4 fu">
  <a href="{{ route('admin.payments.index') }}" class="ftab {{ !$status?'active':'' }}">Semua</a>
  @foreach($statuses as $k=>$l)<a href="{{ route('admin.payments.index',['status'=>$k]) }}" class="ftab {{ $status===$k?'active':'' }}">{{ $l }}</a>@endforeach
</div>
<div class="card-ojs fu fd2">
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>Invoice</th><th>Artikel</th><th>Author</th><th>Nominal</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($payments as $p)
        <tr>
          <td><span style="font-size:12px;font-family:'Courier New',monospace;font-weight:700;color:var(--txt);">{{ $p->invoice_code }}</span></td>
          <td><div class="cell-pri" style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $p->article->title }}</div><div class="cell-sub">{{ $p->article->journal->abbreviation }}</div></td>
          <td><span class="cell-mute">{{ $p->author->name }}</span></td>
          <td><span style="font-size:13px;font-weight:700;color:var(--txt);">Rp {{ number_format($p->amount,0,',','.') }}</span></td>
          <td><span class="bx bx-{{ $p->status }}" style="font-size:10px;">{{ $p->status_label }}</span></td>
          <td><span class="cell-mute">{{ $p->created_at->format('d M Y') }}</span></td>
          <td><a href="{{ route('admin.payments.show',$p) }}" class="btn-o btn-out btn-sm">Detail</a></td>
        </tr>
        @empty
        <tr><td colspan="7"><div class="empty-st"><div class="empty-icon"><i class="bi bi-credit-card"></i></div><div class="empty-title">Tidak ada data</div></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-ftr">{{ $payments->withQueryString()->links() }}</div>
</div>
@endsection
