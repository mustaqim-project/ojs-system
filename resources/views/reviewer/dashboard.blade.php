{{-- reviewer/dashboard.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
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
<div class="ds-alert ds-alert-info" data-aos="fade-up" data-aos-delay="100">
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
<div class="row g-4 mb-4">
  @foreach($cards as $c)
  <div class="col-12 col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="{{ 100 + ($loop->index * 50) }}">
    <div style="position:relative; overflow:hidden; background:var(--bg-surface); border:1px solid var(--border); border-radius:20px; padding:24px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.02), 0 4px 6px -4px rgba(0,0,0,0.02); transition:all 0.4s cubic-bezier(0.16, 1, 0.3, 1); display:flex; flex-direction:column; justify-content:space-between; height:100%; cursor:default;"
         onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 20px 40px -10px rgba(0,0,0,0.08)';"
         onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.02), 0 4px 6px -4px rgba(0,0,0,0.02)';">
      <i class="{{ $c['icon'] }}" style="position:absolute; right:-15px; bottom:-25px; font-size:140px; opacity:0.04; transform:rotate(-15deg); z-index:0; color:{{ $c['color'] }}; pointer-events:none;"></i>
      <div style="position:relative; z-index:1; margin-bottom:24px;">
        <div style="width:52px; height:52px; border-radius:14px; background:{{ $c['bg'] }}; color:{{ $c['color'] }}; display:flex; align-items:center; justify-content:center; font-size:24px; box-shadow:0 4px 6px rgba(0,0,0,0.04);">
          <i class="{{ $c['icon'] }}"></i>
        </div>
      </div>
      <div style="position:relative; z-index:1;">
        <div style="font-size:13px; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">{{ $c['label'] }}</div>
        <div style="font-size:32px; font-weight:800; color:var(--text-main); line-height:1; letter-spacing:-0.03em;" id="stat-{{ $loop->index }}">{{ $c['val'] }}</div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Active Review Assignments --}}
<div class="ds-card" data-aos="fade-up" data-aos-delay="300">
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
<div class="row g-4 mt-1 mb-4">
  <div class="col-12 col-xl-6" data-aos="fade-up" data-aos-delay="400">
    <div class="ds-card h-100">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Task Completion</span>
      </div>
      <div class="card-body d-flex align-items-center justify-content-center">
        <div id="completionChart" class="chart-container chart-container-sm w-100"></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-6" data-aos="fade-up" data-aos-delay="500">
    <div class="ds-card h-100">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Performance Metrics</span>
      </div>
      <div class="card-body d-flex align-items-center justify-content-center">
        <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:24px;width:100%;text-align:center;">
          @php
          $perf = [
            ['val'=>$stats['completed'],  'label'=>'Reviews Completed', 'color'=>'var(--success)'],
            ['val'=>($stats['total'] > 0 ? round(($stats['completed']/$stats['total'])*100) : 0).'%', 'label'=>'Completion Rate', 'color'=>'var(--primary)'],
            ['val'=>$stats['pending'],    'label'=>'Awaiting Action',   'color'=>'var(--warning)'],
            ['val'=>$stats['in_progress'],'label'=>'In Progress',       'color'=>'var(--info)'],
          ];
          @endphp
          @foreach($perf as $i => $p)
          <div style="padding:16px;background:var(--bg-app);border-radius:var(--radius-md);border:1px solid var(--border);">
            <div style="font-size:28px;font-weight:700;color:{{ $p['color'] }};">{{ $p['val'] }}</div>
            <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">{{ $p['label'] }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stats = @json(array_map(function($c) { return $c['val']; }, $cards));
    stats.forEach((val, idx) => {
        let numeric = String(val).replace(/[^0-9.-]+/g, "");
        if (numeric) {
            let options = { duration: 2.5, separator: '.' };
            let countUp = new countUp.CountUp('stat-' + idx, numeric, options);
            if (!countUp.error) {
                countUp.start();
            }
        }
    });

    @if($stats['completed'] > 0)
    // ApexCharts - Task Completion Radial
    const completionOptions = {
        series: [{{ $stats['total'] > 0 ? round(($stats['completed']/$stats['total'])*100) : 0 }}],
        chart: {
            height: 280,
            type: 'radialBar',
            fontFamily: 'Plus Jakarta Sans, sans-serif',
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '65%',
                },
                dataLabels: {
                    name: {
                        fontSize: '14px',
                        color: '#64748b',
                        offsetY: -10
                    },
                    value: {
                        fontSize: '32px',
                        fontWeight: 700,
                        color: '#1e293b',
                        formatter: function (val) {
                            return val + "%"
                        }
                    }
                }
            }
        },
        colors: ['#2F855A'],
        labels: ['Completion Rate'],
        stroke: {
            lineCap: 'round'
        },
    };
    new ApexCharts(document.querySelector("#completionChart"), completionOptions).render();
    @endif
});
</script>
@endpush
@endsection
