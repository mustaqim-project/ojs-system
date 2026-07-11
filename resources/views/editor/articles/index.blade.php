{{-- editor/articles/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb"><a href="{{ route('editor.dashboard') }}">Dashboard</a><span>›</span><span class="cur">Kelola Artikel</span></div>
    <h2 class="pg-title">Kelola Artikel</h2>
    <p class="pg-desc">{{ $articles->total() }} artikel di sistem</p>
  </div>
</div>

{{-- Filter tabs --}}
<div class="ftabs mb-4 fu">
  <a href="{{ route('editor.articles.index') }}" class="ftab {{ !$status ? 'active' : '' }}">Semua</a>
  @foreach($statuses as $key => $label)
  <a href="{{ route('editor.articles.index', ['status' => $key]) }}"
     class="ftab {{ $status === $key ? 'active' : '' }}">
    {{ $label }}
  </a>
  @endforeach
</div>

<div class="card-ojs fu fd2">
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead>
        <tr>
          <th>Artikel</th>
          <th>Author</th>
          <th>Reviewer</th>
          <th>Status</th>
          <th>Disubmit</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $article)
        <tr>
          <td>
            <div class="cell-pri" style="max-width:240px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              {{ $article->title }}
            </div>
            <div class="cell-sub">{{ $article->journal->abbreviation ?? $article->journal->title }}</div>
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:7px;">
              <div style="width:24px;height:24px;border-radius:6px;background:#f0fdf4;color:#15803d;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">
                {{ strtoupper(substr($article->author->name, 0, 1)) }}
              </div>
              <span class="cell-mute">{{ $article->author->name }}</span>
            </div>
          </td>
          <td>
            @php $doneReviews = $article->reviews->where('status', 'completed')->count();
                 $totalReviews = $article->reviews->count(); @endphp
            @if($totalReviews > 0)
              <div style="display:flex;align-items:center;gap:6px;">
                <div class="prog-bar-wrap" style="width:50px;">
                  <div class="prog-bar" style="width:{{ $totalReviews > 0 ? ($doneReviews/$totalReviews)*100 : 0 }}%;background:{{ $doneReviews===$totalReviews?'var(--green)':'var(--acc)' }};"></div>
                </div>
                <span style="font-size:11px;color:var(--txt2);font-weight:600;">{{ $doneReviews }}/{{ $totalReviews }}</span>
              </div>
            @else
              <span style="font-size:12px;color:var(--txt4);">Belum diassign</span>
            @endif
          </td>
          <td>
            <span class="bx bx-{{ $article->status }}" style="font-size:10.5px;padding:3px 9px;">
              {{ $article->status_label }}
            </span>
          </td>
          <td>
            <span class="cell-mute">{{ $article->submitted_at?->format('d M Y') }}</span>
          </td>
          <td>
            <a href="{{ route('editor.articles.show', $article) }}" class="btn-o btn-pri btn-sm">
              <i class="bi bi-arrow-right"></i> Proses
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <div class="empty-st">
              <div class="empty-icon"><i class="bi bi-file-earmark-text"></i></div>
              <div class="empty-title">Tidak ada artikel</div>
              <div class="empty-desc">{{ $status ? 'Tidak ada artikel dengan status ini.' : 'Belum ada artikel masuk.' }}</div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-ftr">{{ $articles->withQueryString()->links() }}</div>
</div>
@endsection
