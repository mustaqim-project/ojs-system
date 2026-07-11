{{-- author/dashboard.blade.php --}}
@extends('layouts.dashboard')
@section('content')

{{-- Page Header --}}
<div class="ds-page-hdr fu">
  <div>
    <div class="ds-breadcrumb">
      <span>Author Portal</span>
      <span class="ds-breadcrumb-sep">›</span>
      <span style="color:var(--text-main);">Dashboard</span>
    </div>
    <h1 class="ds-page-title">My Submissions</h1>
    <p class="ds-page-subtitle">Welcome back, {{ auth()->user()->name }}. Here's your publication overview.</p>
  </div>
  <a href="{{ route('author.articles.create') }}" class="ds-btn ds-btn-pri">
    <i class="bi bi-plus-lg"></i> Submit Manuscript
  </a>
</div>

{{-- ORCID Connect Banner --}}
@if(\App\Models\ApiIntegration::isEnabled('orcid'))
<div class="ds-card fu" style="padding:16px 24px;margin-bottom:24px;">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;align-items:center;gap:14px;">
      <div style="width:40px;height:40px;border-radius:10px;background:#F0FDF4;color:#A6CE39;display:flex;align-items:center;justify-content:center;font-size:20px;border:1px solid #C6F6D5;">
        <i class="bi bi-person-badge"></i>
      </div>
      <div>
        <div style="font-size:14px;font-weight:600;color:var(--text-main);">ORCID iD Integration</div>
        @if(auth()->user()->orcid)
          <div style="font-size:13px;color:var(--text-muted);margin-top:2px;">
            Connected as: <x-orcid-badge :orcid="auth()->user()->orcid"/>
          </div>
        @else
          <div style="font-size:13px;color:var(--text-muted);margin-top:2px;">
            Connect your ORCID iD to sync publication metadata automatically.
          </div>
        @endif
      </div>
    </div>
    <div>
      @if(auth()->user()->orcid)
        <form method="POST" action="{{ route('author.orcid.sync') }}" style="display:inline;">
          @csrf
          <button type="submit" class="ds-btn ds-btn-out ds-btn-sm">
            <i class="bi bi-arrow-repeat"></i> Sync Profile
          </button>
        </form>
      @else
        <a href="{{ route('auth.orcid.redirect') }}" class="ds-btn ds-btn-sm"
           style="background:#A6CE39;border-color:#A6CE39;color:#fff;text-decoration:none;">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 512 512" style="fill:currentColor;flex-shrink:0;">
            <path d="M512 256c0 141.4-114.6 256-256 256S0 397.4 0 256 114.6 0 256 0s256 114.6 256 256z"/>
            <path fill="#fff" d="M178.8 286.2h-21.3v-78.4h21.3v78.4zm-10.7-90.2c-7.3 0-13.2-5.9-13.2-13.2s5.9-13.2 13.2-13.2 13.2 5.9 13.2 13.2-5.9 13.2-13.2-13.2z"/>
          </svg>
          Connect ORCID iD
        </a>
      @endif
    </div>
  </div>
</div>
@endif

{{-- Action Alerts (Revision Required) --}}
@foreach($articles->whereIn('status',['revision_required']) as $a)
<div class="ds-alert ds-alert-warn fu">
  <i class="bi bi-exclamation-triangle-fill"></i>
  <div>
    <strong>Revision Required:</strong> "{{ Str::limit($a->title, 70) }}"
    <a href="{{ route('author.articles.revision',$a) }}"
       style="color:inherit;font-weight:700;margin-left:8px;text-decoration:underline;">
      Upload revision →
    </a>
  </div>
  <button class="ds-alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endforeach

@foreach($articles->whereIn('status',['waiting_payment','payment_uploaded']) as $a)
<div class="ds-alert ds-alert-info fu">
  <i class="bi bi-credit-card-fill"></i>
  <div>
    <strong>Payment Required:</strong> "{{ Str::limit($a->title, 70) }}"
    <a href="{{ route('author.payments.show',$a) }}"
       style="color:inherit;font-weight:700;margin-left:8px;text-decoration:underline;">
      View Invoice →
    </a>
  </div>
  <button class="ds-alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endforeach

{{-- KPI Stats --}}
@php
$cards = [
  ['label'=>'Total Submissions', 'val'=>$stats['total'],           'icon'=>'bi-file-earmark-text', 'color'=>'var(--info)',    'bg'=>'var(--info-bg)'],
  ['label'=>'Published',         'val'=>$stats['published'],       'icon'=>'bi-check-circle',      'color'=>'var(--success)', 'bg'=>'var(--success-bg)'],
  ['label'=>'Under Review',      'val'=>$stats['under_review'],    'icon'=>'bi-hourglass-split',   'color'=>'var(--warning)', 'bg'=>'var(--warning-bg)'],
  ['label'=>'Awaiting Payment',  'val'=>$stats['waiting_payment'], 'icon'=>'bi-credit-card',       'color'=>'#6B46C1',       'bg'=>'#FAF5FF'],
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

{{-- Submissions Table --}}
<div class="ds-card fu fd3">
  <div class="ds-card-hdr">
    <span class="ds-card-title">My Submissions</span>
    <a href="{{ route('author.articles.create') }}" class="ds-btn ds-btn-out ds-btn-sm">
      <i class="bi bi-plus-lg"></i> New
    </a>
  </div>
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Journal</th>
          <th>Status</th>
          <th>Submitted</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $article)
        <tr>
          <td>
            <a href="{{ route('author.articles.show',$article) }}"
               style="font-weight:600;color:var(--text-main);text-decoration:none;max-width:280px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               title="{{ $article->title }}">
              {{ Str::limit($article->title, 55) }}
            </a>
          </td>
          <td style="color:var(--text-muted);font-size:13px;">{{ $article->journal->abbreviation ?? '—' }}</td>
          <td>
            <span class="ds-badge ds-badge-{{ $article->status }}" style="font-size:11px;">
              {{ $article->status_label }}
            </span>
          </td>
          <td style="color:var(--text-muted);font-size:13px;">{{ $article->submitted_at?->format('d M Y') }}</td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
              <a href="{{ route('author.articles.show',$article) }}" class="ds-btn ds-btn-ghost ds-btn-xs">Detail</a>
              @if($article->status === 'revision_required')
                <a href="{{ route('author.articles.revision',$article) }}" class="ds-btn ds-btn-xs" style="background:var(--warning-bg);color:var(--warning);border-color:var(--warning);">
                  <i class="bi bi-pencil"></i> Revise
                </a>
              @endif
              @if($article->needsPayment())
                <a href="{{ route('author.payments.show',$article) }}" class="ds-btn ds-btn-xs" style="background:#FAF5FF;color:#6B46C1;border-color:#6B46C1;">
                  <i class="bi bi-credit-card"></i> Pay
                </a>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5">
            <div class="ds-empty">
              <div class="ds-empty-icon"><i class="bi bi-file-earmark-text"></i></div>
              <div class="ds-empty-title">No submissions yet</div>
              <div class="ds-empty-desc">Submit your first manuscript to get started.</div>
              <a href="{{ route('author.articles.create') }}" class="ds-btn ds-btn-pri" style="display:inline-flex;">
                <i class="bi bi-plus-lg"></i> Submit Manuscript
              </a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
