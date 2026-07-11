{{-- editor/dashboard.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr fu">
  <div>
    <div class="ds-breadcrumb">
      <span>Editorial</span>
      <span class="ds-breadcrumb-sep">›</span>
      <span style="color:var(--text-main);">Dashboard</span>
    </div>
    <h1 class="ds-page-title">Editorial Workspace</h1>
    <p class="ds-page-subtitle">Manage manuscripts and coordinate the review process efficiently.</p>
  </div>
  <a href="{{ route('editor.articles.index') }}" class="ds-btn ds-btn-out">
    <i class="bi bi-list-ul"></i> All Manuscripts
  </a>
</div>

{{-- KPI Stats --}}
@php
$cards = [
  ['label'=>'New Submissions', 'val'=>$stats['submitted'],         'icon'=>'bi-inbox',                    'color'=>'var(--info)',    'bg'=>'var(--info-bg)'],
  ['label'=>'Under Review',    'val'=>$stats['under_review'],      'icon'=>'bi-hourglass-split',          'color'=>'var(--warning)', 'bg'=>'var(--warning-bg)'],
  ['label'=>'Needs Revision',  'val'=>$stats['revision_required'], 'icon'=>'bi-arrow-counterclockwise',   'color'=>'var(--warning)', 'bg'=>'var(--warning-bg)'],
  ['label'=>'Accepted',        'val'=>$stats['accepted'],          'icon'=>'bi-check-circle',             'color'=>'var(--success)', 'bg'=>'var(--success-bg)'],
  ['label'=>'Published',       'val'=>$stats['published'],         'icon'=>'bi-globe2',                   'color'=>'#285E61',       'bg'=>'#F0FDFA'],
];
@endphp
<div class="row g-3 mb-4">
  @foreach($cards as $c)
  <div class="col-6 col-lg fu fd{{ $loop->index+1 }}">
    <div class="ds-card ds-stat" style="margin-bottom:0;">
      <div class="ds-stat-icon" style="background:{{ $c['bg'] }};color:{{ $c['color'] }};">
        <i class="{{ $c['icon'] }}"></i>
      </div>
      <div class="ds-stat-val">{{ $c['val'] }}</div>
      <div class="ds-stat-lbl">{{ $c['label'] }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- Manuscripts Needing Attention --}}
<div class="ds-card fu fd3">
  <div class="ds-card-hdr">
    <span class="ds-card-title">Manuscripts Needing Attention</span>
    <a href="{{ route('editor.articles.index') }}" class="ds-btn ds-btn-ghost ds-btn-sm">
      View All <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Author</th>
          <th>Status</th>
          <th>Submitted</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentArticles as $a)
        <tr>
          <td>
            <a href="{{ route('editor.articles.show',$a) }}"
               style="font-weight:600;color:var(--text-main);text-decoration:none;max-width:320px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               title="{{ $a->title }}">
              {{ $a->title }}
            </a>
          </td>
          <td style="color:var(--text-muted);font-size:13px;">{{ $a->author->name }}</td>
          <td>
            <span class="ds-badge ds-badge-{{ $a->status }}" style="font-size:11px;">{{ $a->status_label }}</span>
          </td>
          <td style="color:var(--text-muted);font-size:13px;">{{ $a->submitted_at?->format('d M Y') }}</td>
          <td>
            <a href="{{ route('editor.articles.show',$a) }}" class="ds-btn ds-btn-pri ds-btn-sm">
              <i class="bi bi-arrow-right"></i> Process
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5">
            <div class="ds-empty">
              <div class="ds-empty-icon"><i class="bi bi-inbox"></i></div>
              <div class="ds-empty-title">Queue is clear</div>
              <div class="ds-empty-desc">No manuscripts pending editorial action.</div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
