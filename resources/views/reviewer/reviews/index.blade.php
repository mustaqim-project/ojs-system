@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb"><a href="{{ route('reviewer.dashboard') }}">Dashboard</a><span>›</span><span class="cur">Tugas Review</span></div>
    <h2 class="pg-title">Tugas Review Saya</h2>
    <p class="pg-desc">{{ $reviews->total() }} total tugas review</p>
  </div>
</div>

{{-- Filter --}}
<div class="ftabs mb-4 fu">
  @php $cur=request('status',''); @endphp
  <a href="{{ route('reviewer.reviews.index') }}" class="ftab {{ !$cur?'active':'' }}">Semua</a>
  <a href="{{ route('reviewer.reviews.index',['status'=>'pending']) }}" class="ftab {{ $cur==='pending'?'active':'' }}">Menunggu</a>
  <a href="{{ route('reviewer.reviews.index',['status'=>'in_progress']) }}" class="ftab {{ $cur==='in_progress'?'active':'' }}">Dalam Proses</a>
  <a href="{{ route('reviewer.reviews.index',['status'=>'completed']) }}" class="ftab {{ $cur==='completed'?'active':'' }}">Selesai</a>
  <a href="{{ route('reviewer.reviews.index',['status'=>'declined']) }}" class="ftab {{ $cur==='declined'?'active':'' }}">Ditolak</a>
</div>

<div class="card-ojs fu fd2">
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead>
        <tr>
          <th>Artikel</th>
          <th>Jurnal</th>
          <th>Status</th>
          <th>Rekomendasi</th>
          <th>Batas Waktu</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reviews as $review)
        <tr>
          <td>
            <div class="cell-pri" style="max-width:280px;">{{ Str::limit($review->article->title,55) }}</div>
            <div class="cell-sub">{{ $review->article->author->name }}</div>
          </td>
          <td><span class="cell-mute">{{ $review->article->journal->abbreviation }}</span></td>
          <td>
            @php $sc=['pending'=>'bx-yellow','in_progress'=>'bx-blue','completed'=>'bx-green','accepted'=>'bx-green','declined'=>'bx-red'];
            $cl=$sc[$review->status]??'bx-gray'; @endphp
            <span class="bx {{ $cl }}">{{ ucfirst(str_replace('_',' ',$review->status)) }}</span>
          </td>
          <td>
            @if($review->recommendation)
              @php $rc=['accept'=>'bx-green','minor'=>'bx-yellow','major'=>'bx-orange','reject'=>'bx-red'][$review->recommendation]??'bx-gray'; @endphp
              <span class="bx {{ $rc }}">{{ $review->recommendation_label }}</span>
            @else
              <span style="font-size:12px;color:var(--txt4);">—</span>
            @endif
          </td>
          <td>
            @if($review->due_date)
              @php $ov=$review->due_date->isPast()&&$review->status!=='completed'; @endphp
              <span style="font-size:12px;color:{{ $ov?'var(--red)':'var(--txt2)' }};font-weight:{{ $ov?'700':'400' }};">
                {{ $review->due_date->format('d M Y') }}
              </span>
            @else <span class="cell-mute">—</span> @endif
          </td>
          <td>
            <a href="{{ route('reviewer.reviews.show',$review) }}" class="btn-o btn-out btn-sm">Detail</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="6">
          <div class="empty-st">
            <div class="empty-icon"><i class="bi bi-clipboard"></i></div>
            <div class="empty-title">Tidak ada tugas review</div>
          </div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-ftr">{{ $reviews->withQueryString()->links() }}</div>
</div>
@endsection
