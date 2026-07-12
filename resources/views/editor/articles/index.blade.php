{{-- editor/articles/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Editor'],['label'=>'Dashboard','href'=>route('editor.dashboard')],['label'=>'Manage Articles']]"/>
    <h1 class="ds-page-title">Manage Articles</h1>
    <p class="ds-page-subtitle">{{ $articles->total() }} manuscripts in the editorial queue</p>
  </div>
</div>

<div class="ds-ftabs mb-4" data-aos="fade-up" style="flex-wrap:wrap;">
  <a href="{{ route('editor.articles.index') }}" class="ds-ftab {{ !$status ? 'active' : '' }}">All</a>
  @foreach($statuses as $key => $label)
    <a href="{{ route('editor.articles.index',['status'=>$key]) }}" class="ds-ftab {{ $status === $key ? 'active' : '' }}">{{ $label }}</a>
  @endforeach
</div>

<div class="ds-card" data-aos="fade-up" data-aos-delay="200">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Article</th>
          <th>Author</th>
          <th>Reviews</th>
          <th>Status</th>
          <th>Submitted</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $article)
        <tr>
          <td>
            <a href="{{ route('editor.articles.show',$article) }}"
               style="font-weight:600;color:var(--text-main);text-decoration:none;max-width:240px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               title="{{ $article->title }}">{{ $article->title }}</a>
            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $article->journal->abbreviation ?? $article->journal->title }}</div>
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div style="width:26px;height:26px;border-radius:6px;background:#F0FDF4;color:#15803D;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">
                {{ strtoupper(substr($article->author->name,0,1)) }}
              </div>
              <span style="font-size:13px;color:var(--text-muted);">{{ $article->author->name }}</span>
            </div>
          </td>
          <td>
            @php $done = $article->reviews->where('status','completed')->count(); $total = $article->reviews->count(); @endphp
            @if($total > 0)
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:50px;height:4px;background:var(--bg-app);border-radius:4px;overflow:hidden;">
                  <div style="height:100%;width:{{ $total > 0 ? round($done/$total*100) : 0 }}%;background:{{ $done===$total ? 'var(--success)' : 'var(--primary)' }};border-radius:4px;"></div>
                </div>
                <span style="font-size:12px;color:var(--text-muted);font-weight:600;">{{ $done }}/{{ $total }}</span>
              </div>
            @else
              <span style="font-size:12px;color:var(--text-muted);">Not assigned</span>
            @endif
          </td>
          <td><x-status-badge :status="$article->status" :label="$article->status_label"/></td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $article->submitted_at?->format('d M Y') }}</td>
          <td>
            <a href="{{ route('editor.articles.show',$article) }}" class="ds-btn ds-btn-pri ds-btn-sm">
              Process <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <x-ui.empty-state icon="bi-file-earmark-text" title="No articles found"
              :description="$status ? 'No articles with this status.' : 'No manuscripts in the editorial queue.'"/>
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
