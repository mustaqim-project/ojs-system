@extends('layouts.dashboard')
@section('content')

{{-- Page Header --}}
<div class="ds-page-hdr fu">
  <div>
    <div class="ds-breadcrumb">
      <span>System</span>
      <span class="ds-breadcrumb-sep">›</span>
      <span>Administration</span>
      <span class="ds-breadcrumb-sep">›</span>
      <span style="color:var(--text-main);">Dashboard</span>
    </div>
    <h1 class="ds-page-title">Overview</h1>
    <p class="ds-page-subtitle">System summary as of {{ now()->format('d F Y') }}</p>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="{{ route('admin.payments.index',['status'=>'uploaded']) }}" class="ds-btn ds-btn-out">
      <i class="bi bi-credit-card"></i> Verify Payments
    </a>
    <a href="{{ route('admin.users.create') }}" class="ds-btn ds-btn-pri">
      <i class="bi bi-plus-lg"></i> Add User
    </a>
  </div>
</div>

{{-- Pending Payments Alert --}}
@if($stats['pending_payment'] > 0)
<div class="ds-alert ds-alert-warn fu">
  <i class="bi bi-exclamation-triangle-fill"></i>
  <div>
    <strong>{{ $stats['pending_payment'] }} payment(s)</strong> awaiting verification.
    <a href="{{ route('admin.payments.index',['status'=>'uploaded']) }}"
       style="color:inherit;font-weight:700;margin-left:8px;text-decoration:underline;">
      Review now →
    </a>
  </div>
  <button class="ds-alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif

{{-- KPI Stats --}}
@php
$cards = [
  ['label'=>'Total Users',      'val'=>number_format($stats['total_users']),  'icon'=>'bi-people-fill',          'color'=>'var(--info)',    'bg'=>'var(--info-bg)',    'delta'=>'+12 this month', 'up'=>true],
  ['label'=>'Total Journals',   'val'=>$stats['total_journals'],              'icon'=>'bi-journals',             'color'=>'#6B46C1',       'bg'=>'#FAF5FF',          'delta'=>''],
  ['label'=>'Total Articles',   'val'=>$stats['total_articles'],              'icon'=>'bi-file-earmark-text-fill','color'=>'var(--warning)', 'bg'=>'var(--warning-bg)', 'delta'=>'+8 this week', 'up'=>true],
  ['label'=>'Published',        'val'=>$stats['published'],                   'icon'=>'bi-check-circle-fill',    'color'=>'var(--success)', 'bg'=>'var(--success-bg)', 'delta'=>'+5 this month', 'up'=>true],
  ['label'=>'Pending Payment',  'val'=>$stats['pending_payment'],             'icon'=>'bi-hourglass-split',      'color'=>'var(--warning)', 'bg'=>'var(--warning-bg)', 'delta'=>'Action required'],
  ['label'=>'Total Revenue',    'val'=>'Rp '.number_format($stats['total_revenue'],0,',','.'), 'icon'=>'bi-cash-coin','color'=>'#285E61','bg'=>'#F0FDFA','delta'=>'+2.1M this month','up'=>true],
];
@endphp
<div class="row g-3 mb-4">
  @foreach($cards as $c)
  <div class="col-6 col-lg-2 fu fd{{ $loop->index+1 }}">
    <div class="ds-card ds-stat" style="margin-bottom:0;">
      <div class="ds-stat-icon" style="background:{{ $c['bg'] }};color:{{ $c['color'] }};">
        <i class="{{ $c['icon'] }}"></i>
      </div>
      <div class="ds-stat-val" style="{{ strlen((string)$c['val'])>8?'font-size:20px;':'' }}">{{ $c['val'] }}</div>
      <div class="ds-stat-lbl">{{ $c['label'] }}</div>
      @if($c['delta'])
      <div style="font-size:12px;font-weight:600;margin-top:8px;color:{{ ($c['up']??false) ? 'var(--success)' : 'var(--text-muted)' }};display:flex;align-items:center;gap:3px;">
        @if($c['up']??false)<i class="bi bi-arrow-up-short"></i>@endif
        {{ $c['delta'] }}
      </div>
      @endif
    </div>
  </div>
  @endforeach
</div>

{{-- Quick Actions --}}
<div class="ds-card fu fd3">
  <div class="ds-card-hdr">
    <span class="ds-card-title">Quick Actions</span>
  </div>
  <div class="ds-card-body">
    <div class="row g-2">
      @php
      $qas = [
        ['href'=>route('admin.users.create'),                                     'icon'=>'bi-person-plus-fill',         'color'=>'var(--info)',    'bg'=>'var(--info-bg)',    'label'=>'Add User'],
        ['href'=>route('admin.journals.create'),                                  'icon'=>'bi-journal-plus',             'color'=>'var(--success)', 'bg'=>'var(--success-bg)', 'label'=>'Create Journal'],
        ['href'=>route('admin.issues.create'),                                    'icon'=>'bi-collection-fill',          'color'=>'var(--warning)', 'bg'=>'var(--warning-bg)', 'label'=>'Add Issue'],
        ['href'=>route('admin.payments.index',['status'=>'uploaded']),            'icon'=>'bi-credit-card-2-front-fill', 'color'=>'var(--danger)',  'bg'=>'var(--danger-bg)',  'label'=>'Verify Payments'],
        ['href'=>route('admin.articles.index'),                                   'icon'=>'bi-file-earmark-text-fill',   'color'=>'#6B46C1',       'bg'=>'#FAF5FF',          'label'=>'All Articles'],
        ['href'=>route('admin.settings.index'),                                   'icon'=>'bi-sliders',                  'color'=>'#285E61',       'bg'=>'#F0FDFA',          'label'=>'Settings'],
      ];
      @endphp
      @foreach($qas as $qa)
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ $qa['href'] }}" style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:20px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-surface);text-decoration:none;color:var(--text-muted);font-size:13px;font-weight:500;text-align:center;transition:all 0.18s;"
           onmouseover="this.style.borderColor='{{ $qa['color'] }}';this.style.background='{{ $qa['bg'] }}';this.style.color='{{ $qa['color'] }}';"
           onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--bg-surface)';this.style.color='var(--text-muted)';">
          <div style="width:40px;height:40px;border-radius:10px;background:{{ $qa['bg'] }};color:{{ $qa['color'] }};display:flex;align-items:center;justify-content:center;font-size:20px;">
            <i class="{{ $qa['icon'] }}"></i>
          </div>
          {{ $qa['label'] }}
        </a>
      </div>
      @endforeach
    </div>
  </div>
</div>

{{-- Two Column --}}
<div class="row g-3">
  {{-- Recent Articles --}}
  <div class="col-12 col-xl-7 fu fd4">
    <div class="ds-card h-100" style="margin-bottom:0;">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Recent Submissions</span>
        <a href="{{ route('admin.articles.index') }}" class="ds-btn ds-btn-ghost ds-btn-sm">
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
              <th>Date</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentArticles as $a)
            <tr>
              <td>
                <div style="font-weight:600;color:var(--text-main);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $a->title }}</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $a->journal->abbreviation }}</div>
              </td>
              <td style="color:var(--text-muted);font-size:14px;">{{ $a->author->name }}</td>
              <td>
                <span class="ds-badge ds-badge-{{ $a->status }}" style="font-size:11px;">{{ $a->status_label }}</span>
              </td>
              <td style="color:var(--text-muted);font-size:13px;">{{ $a->submitted_at?->format('d M') }}</td>
              <td>
                <a href="{{ route('admin.articles.show',$a) }}" class="ds-btn ds-btn-ghost ds-btn-xs">
                  <i class="bi bi-arrow-right"></i>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Pending Payments --}}
  <div class="col-12 col-xl-5 fu fd5">
    <div class="ds-card h-100" style="margin-bottom:0;">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Pending Payments</span>
        @if($pendingPayments->count())
          <span class="ds-badge ds-badge-warning" style="font-size:11px;">{{ $pendingPayments->count() }} pending</span>
        @endif
      </div>
      @forelse($pendingPayments as $payment)
      <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid #f1f5f9;">
        <div style="width:8px;height:8px;border-radius:50%;background:var(--warning);flex-shrink:0;"></div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:14px;font-weight:600;color:var(--text-main);">{{ $payment->invoice_code }}</div>
          <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
            {{ $payment->author->name }} · Rp {{ number_format($payment->amount,0,',','.') }}
          </div>
        </div>
        <a href="{{ route('admin.payments.show',$payment) }}" class="ds-btn ds-btn-suc ds-btn-sm" style="flex-shrink:0;">
          Verify
        </a>
      </div>
      @empty
      <div class="ds-empty" style="padding:40px;">
        <div class="ds-empty-icon"><i class="bi bi-check-circle"></i></div>
        <div class="ds-empty-title">All cleared</div>
        <div class="ds-empty-desc">No pending payments to verify.</div>
      </div>
      @endforelse
      @if($pendingPayments->count())
      <div style="padding:12px 20px;border-top:1px solid var(--border);background:var(--bg-app);">
        <a href="{{ route('admin.payments.index') }}" class="ds-btn ds-btn-ghost ds-btn-sm w-100 justify-content-center">
          View all payments <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
      @endif
    </div>
  </div>
</div>

@endsection
