{{-- reviewer/dashboard.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr fu">
  <div>
    <div class="ds-breadcrumb">
      <span>Reviewer Portal</span>
      <span class="ds-breadcrumb-sep">›</span>
      <span style="color:var(--text-main);">Dashboard</span>
    </div>
    <h1 class="ds-page-title">Review Queue</h1>
    <p class="ds-page-subtitle">Welcome, {{ auth()->user()->name }}. Here are your active review assignments.</p>
  </div>
  <a href="{{ route('reviewer.reviews.index') }}" class="ds-btn ds-btn-out">
    <i class="bi bi-list-check"></i> All Assignments
  </a>
</div>

{{-- Pending Alert --}}
@if($pendingReviews->count())
<div class="ds-alert ds-alert-info fu">
  <i class="bi bi-bell-fill"></i>
  <div><strong>{{ $pendingReviews->count() }} review assignment(s)</strong> awaiting your confirmation or action.</div>
  <button class="ds-alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif

{{-- KPI Stats --}}
@php
$cards = [
  ['label'=>'Total Assigned', 'val'=>$stats['total'],       'icon'=>'bi-clipboard-data',  'color'=>'var(--info)',    'bg'=>'var(--info-bg)'],
  ['label'=>'Pending',        'val'=>$stats['pending'],     'icon'=>'bi-inbox',           'color'=>'var(--warning)', 'bg'=>'var(--warning-bg)'],
  ['label'=>'In Progress',    'val'=>$stats['in_progress'], 'icon'=>'bi-pencil-square',   'color'=>'var(--warning)', 'bg'=>'var(--warning-bg)'],
  ['label'=>'Completed',      'val'=>$stats['completed'],   'icon'=>'bi-check-circle',    'color'=>'var(--success)', 'bg'=>'var(--success-bg)'],
];
@endphp
<div class="row g-3 mb-4">
  @foreach($cards as $c)
  <div class="col-6 col-lg-3 fu fd{{ $loop->index+1 }}">
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

{{-- Active Review Assignments --}}
<div class="ds-card fu fd3">
  <div class="ds-card-hdr">
    <span class="ds-card-title">Active Assignments</span>
    <a href="{{ route('reviewer.reviews.index') }}" class="ds-btn ds-btn-ghost ds-btn-sm">
      View All <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Manuscript</th>
          <th>Journal</th>
          <th>Status</th>
          <th>Deadline</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pendingReviews as $review)
        <tr>
          <td>
            <div style="font-weight:600;color:var(--text-main);max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $review->article->title }}">
              {{ $review->article->title }}
            </div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $review->article->author->name }}</div>
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $review->article->journal->abbreviation }}</td>
          <td>
            @php
              $statusMap = [
                'pending'     => 'ds-badge-warning',
                'in_progress' => 'ds-badge-info',
                'completed'   => 'ds-badge-success',
                'accepted'    => 'ds-badge-success',
                'declined'    => 'ds-badge-danger',
              ];
              $bCls = $statusMap[$review->status] ?? 'ds-badge-neutral';
            @endphp
            <span class="ds-badge {{ $bCls }}" style="font-size:11px;">
              {{ ucfirst(str_replace('_', ' ', $review->status)) }}
            </span>
          </td>
          <td>
            @if($review->due_date)
              @php $overdue = $review->due_date->isPast() && $review->status !== 'completed'; @endphp
              <span style="font-size:13px;font-weight:{{ $overdue?'700':'400' }};color:{{ $overdue?'var(--danger)':'var(--text-muted)' }};">
                @if($overdue)<i class="bi bi-exclamation-triangle-fill me-1" style="font-size:12px;"></i>@endif
                {{ $review->due_date->format('d M Y') }}
              </span>
            @else
              <span style="color:var(--text-muted);">—</span>
            @endif
          </td>
          <td>
            <a href="{{ route('reviewer.reviews.show',$review) }}" class="ds-btn ds-btn-pri ds-btn-sm">
              <i class="bi bi-arrow-right"></i> Process
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5">
            <div class="ds-empty">
              <div class="ds-empty-icon"><i class="bi bi-clipboard-check"></i></div>
              <div class="ds-empty-title">Queue is clear</div>
              <div class="ds-empty-desc">All reviews have been completed.</div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Review Performance Summary --}}
@if($stats['completed'] > 0)
<div class="ds-card fu fd5" style="margin-top:24px;">
  <div class="ds-card-hdr">
    <span class="ds-card-title">Review Performance</span>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:0;">
    @php
    $perf = [
      ['val'=>$stats['completed'],  'label'=>'Reviews Completed', 'color'=>'var(--success)'],
      ['val'=>($stats['total'] > 0 ? round(($stats['completed']/$stats['total'])*100) : 0).'%', 'label'=>'Completion Rate', 'color'=>'var(--primary)'],
      ['val'=>$stats['pending'],    'label'=>'Awaiting Action',   'color'=>'var(--warning)'],
      ['val'=>$stats['in_progress'],'label'=>'In Progress',       'color'=>'var(--info)'],
    ];
    @endphp
    @foreach($perf as $i => $p)
    <div style="text-align:center;padding:24px 16px;{{ $i < count($perf)-1 ? 'border-right:1px solid var(--border);' : '' }}">
      <div style="font-size:28px;font-weight:700;color:{{ $p['color'] }};">{{ $p['val'] }}</div>
      <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">{{ $p['label'] }}</div>
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection
