@extends('layouts.dashboard')
@section('content')

{{-- Page Header --}}
<div class="ds-page-hdr" data-aos="fade-up">
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
<div class="ds-alert ds-alert-warn" data-aos="fade-up">
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
  ['label'=>'Total Users',      'val'=>number_format($stats['total_users']),  'icon'=>'bi-people',               'color'=>'#3b82f6', 'bg'=>'#eff6ff', 'trend'=>'+12% this month', 'up'=>true],
  ['label'=>'Total Journals',   'val'=>$stats['total_journals'],              'icon'=>'bi-journals',             'color'=>'#8b5cf6', 'bg'=>'#f5f3ff', 'trend'=>'Stable'],
  ['label'=>'Total Articles',   'val'=>$stats['total_articles'],              'icon'=>'bi-file-earmark-richtext','color'=>'#f59e0b', 'bg'=>'#fffbeb', 'trend'=>'+8% this week', 'up'=>true],
  ['label'=>'Published',        'val'=>$stats['published'],                   'icon'=>'bi-check2-circle',        'color'=>'#10b981', 'bg'=>'#ecfdf5', 'trend'=>'+5% this month', 'up'=>true],
  ['label'=>'Pending Payment',  'val'=>$stats['pending_payment'],             'icon'=>'bi-hourglass-split',      'color'=>'#ef4444', 'bg'=>'#fef2f2', 'trend'=>'Action needed', 'up'=>false],
  ['label'=>'Total Revenue',    'val'=>'Rp '.number_format($stats['total_revenue'],0,',','.'), 'icon'=>'bi-cash-coin', 'color'=>'#0f4c81', 'bg'=>'#e6f0f9', 'trend'=>'+15% growth', 'up'=>true],
];
@endphp
<div class="row g-4 mb-5">
  @foreach($cards as $c)
  <div class="col-12 col-md-6 col-xl-4 fu fd{{ $loop->index+1 }}">
    <div style="position:relative; overflow:hidden; background:var(--bg-surface); border:1px solid var(--border); border-radius:20px; padding:24px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.02), 0 4px 6px -4px rgba(0,0,0,0.02); transition:all 0.4s cubic-bezier(0.16, 1, 0.3, 1); display:flex; flex-direction:column; justify-content:space-between; height:100%; cursor:default;" 
         onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 20px 40px -10px rgba(0,0,0,0.08)';" 
         onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.02), 0 4px 6px -4px rgba(0,0,0,0.02)';">
      <!-- Background subtle icon -->
      <i class="{{ $c['icon'] }}" style="position:absolute; right:-15px; bottom:-25px; font-size:140px; opacity:0.04; transform:rotate(-15deg); z-index:0; color:{{ $c['color'] }}; pointer-events:none;"></i>
      
      <div style="position:relative; z-index:1; display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px;">
        <div style="width:52px; height:52px; border-radius:14px; background:{{ $c['bg'] }}; color:{{ $c['color'] }}; display:flex; align-items:center; justify-content:center; font-size:24px; box-shadow:0 4px 6px rgba(0,0,0,0.04);">
          <i class="{{ $c['icon'] }}"></i>
        </div>
        @if(isset($c['trend']))
        <div style="display:flex; align-items:center; gap:4px; font-size:12px; font-weight:600; padding:4px 10px; border-radius:20px; background:{{ isset($c['up']) ? ($c['up'] ? '#ecfdf5' : '#fef2f2') : '#f8fafc' }}; color:{{ isset($c['up']) ? ($c['up'] ? '#10b981' : '#ef4444') : '#64748b' }};">
          @if(isset($c['up']) && $c['up'])<i class="bi bi-arrow-up-short" style="font-size:16px; margin-left:-4px;"></i>@endif
          @if(isset($c['up']) && !$c['up'] && $c['trend'] != 'Action needed')<i class="bi bi-arrow-down-short" style="font-size:16px; margin-left:-4px;"></i>@endif
          {{ $c['trend'] }}
        </div>
        @endif
      </div>
      
      <div style="position:relative; z-index:1;">
        <div style="font-size:13px; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">{{ $c['label'] }}</div>
        <div style="font-size:32px; font-weight:800; color:var(--text-main); line-height:1; letter-spacing:-0.03em;" id="stat-{{ $loop->index }}">{{ $c['val'] }}</div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Quick Actions --}}
<div class="row g-3 mb-5" data-aos="fade-up" data-aos-delay="300">
  @php
  $qas = [
    ['href'=>route('admin.users.create'),                                     'icon'=>'bi-person-plus',         'color'=>'#3b82f6', 'bg'=>'#eff6ff', 'label'=>'Add User'],
    ['href'=>route('admin.journals.create'),                                  'icon'=>'bi-journal-plus',        'color'=>'#10b981', 'bg'=>'#ecfdf5', 'label'=>'Create Journal'],
    ['href'=>route('admin.issues.create'),                                    'icon'=>'bi-collection',          'color'=>'#f59e0b', 'bg'=>'#fffbeb', 'label'=>'Add Issue'],
    ['href'=>route('admin.payments.index',['status'=>'uploaded']),            'icon'=>'bi-credit-card',         'color'=>'#ef4444', 'bg'=>'#fef2f2', 'label'=>'Verify Payments'],
    ['href'=>route('admin.articles.index'),                                   'icon'=>'bi-file-earmark-text',   'color'=>'#8b5cf6', 'bg'=>'#f5f3ff', 'label'=>'All Articles'],
    ['href'=>route('admin.settings.index'),                                   'icon'=>'bi-sliders',             'color'=>'#0f4c81', 'bg'=>'#e6f0f9', 'label'=>'Settings'],
  ];
  @endphp
  <div class="col-12">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:12px;">
      <h3 style="font-size:18px; font-weight:700; color:var(--text-main); margin:0;">Quick Actions</h3>
    </div>
  </div>
  @foreach($qas as $qa)
  <div class="col-6 col-md-4 col-xl-2">
    <a href="{{ $qa['href'] }}" style="display:flex; flex-direction:column; align-items:center; gap:12px; padding:24px 16px; text-decoration:none; color:var(--text-main); font-size:14px; font-weight:600; text-align:center; height:100%; background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1);"
       onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 15px 25px -5px rgba(0,0,0,0.05)';this.style.borderColor='{{ $qa['color'] }}';this.querySelector('.qa-icon').style.transform='scale(1.1)';"
       onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none';this.style.borderColor='var(--border)';this.querySelector('.qa-icon').style.transform='scale(1)';">
      <div class="qa-icon" style="width:52px; height:52px; border-radius:16px; background:{{ $qa['bg'] }}; color:{{ $qa['color'] }}; display:flex; align-items:center; justify-content:center; font-size:24px; transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
        <i class="{{ $qa['icon'] }}"></i>
      </div>
      <span>{{ $qa['label'] }}</span>
    </a>
  </div>
  @endforeach
</div>

{{-- Charts Section --}}
<div class="row g-4 mb-4">
  <div class="col-12 col-xl-8" data-aos="fade-up" data-aos-delay="400">
    <div class="ds-card h-100">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Revenue & Submissions Trend</span>
      </div>
      <div class="card-body">
        <div id="revenueChart" class="chart-container"></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-4" data-aos="fade-up" data-aos-delay="500">
    <div class="ds-card h-100">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Submissions Overview</span>
      </div>
      <div class="card-body d-flex align-items-center justify-content-center">
        <div id="statusChart" class="chart-container chart-container-sm w-100"></div>
      </div>
    </div>
  </div>
</div>

{{-- Two Column --}}
<div class="row g-4">
  {{-- Recent Articles --}}
  <div class="col-12 col-xl-7" data-aos="fade-up" data-aos-delay="600">
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
  <div class="col-12 col-xl-5" data-aos="fade-up" data-aos-delay="700">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stats = @json(array_map(function($c) { return $c['val']; }, $cards));
    stats.forEach((val, idx) => {
        let numeric = String(val).replace(/[^0-9.-]+/g, "");
        if (numeric) {
            let options = { duration: 2.5, separator: '.' };
            if (String(val).includes('Rp')) {
                options.prefix = 'Rp ';
            }
            let countUp = new countUp.CountUp('stat-' + idx, numeric, options);
            if (!countUp.error) {
                countUp.start();
            }
        }
    });

    // ApexCharts - Revenue & Submissions Trend
    const revenueOptions = {
        series: [{
            name: 'Revenue (Rp)',
            type: 'area',
            data: [4000000, 5500000, 4800000, 8000000, 7500000, 10500000]
        }, {
            name: 'Submissions',
            type: 'line',
            data: [20, 25, 22, 35, 30, 45]
        }],
        chart: {
            height: 300,
            type: 'line',
            fontFamily: 'Plus Jakarta Sans, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        stroke: {
            curve: 'smooth',
            width: [0, 3],
        },
        fill: {
            type: ['gradient', 'solid'],
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        colors: ['#0F4C81', '#38B2AC'],
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        markers: { size: 4 },
        yaxis: [{
            title: { text: 'Revenue', style: { color: '#64748b', fontWeight: 500 } },
            labels: {
                formatter: function (y) { return (y / 1000000).toFixed(1) + "M"; },
                style: { colors: '#64748b' }
            }
        }, {
            opposite: true,
            title: { text: 'Submissions', style: { color: '#64748b', fontWeight: 500 } },
            labels: { style: { colors: '#64748b' } }
        }],
        xaxis: {
            labels: { style: { colors: '#64748b' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        grid: {
            borderColor: 'rgba(226, 232, 240, 0.5)',
            strokeDashArray: 4,
        },
        legend: { position: 'top', horizontalAlign: 'right' }
    };
    new ApexCharts(document.querySelector("#revenueChart"), revenueOptions).render();

    // ApexCharts - Status Overview
    const statusOptions = {
        series: [{{ $stats['published'] ?? 10 }}, {{ $stats['pending_payment'] ?? 5 }}, {{ $stats['total_articles'] - ($stats['published'] ?? 10) - ($stats['pending_payment'] ?? 5) }}],
        chart: {
            height: 280,
            type: 'donut',
            fontFamily: 'Plus Jakarta Sans, sans-serif',
        },
        labels: ['Published', 'Pending Payment', 'In Review'],
        colors: ['#2F855A', '#C05621', '#2B6CB0'],
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        name: { fontSize: '12px', color: '#64748b' },
                        value: { fontSize: '24px', fontWeight: 700, color: '#1e293b' },
                        total: { show: true, label: 'Total', color: '#64748b' }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: false },
        legend: { position: 'bottom' }
    };
    new ApexCharts(document.querySelector("#statusChart"), statusOptions).render();
});
</script>
@endpush
@endsection
