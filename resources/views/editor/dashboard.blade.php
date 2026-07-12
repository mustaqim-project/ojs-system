{{-- editor/dashboard.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
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
<div class="row g-4 mb-4">
  @foreach($cards as $c)
  <div class="col-12 col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ 100 + ($loop->index * 50) }}">
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

{{-- Editorial Pipeline Chart --}}
<div class="row mb-4">
  <div class="col-12" data-aos="fade-up" data-aos-delay="250">
    <div class="ds-card">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Editorial Pipeline</span>
      </div>
      <div class="card-body">
        <div id="pipelineChart" class="chart-container chart-container-sm"></div>
      </div>
    </div>
  </div>
</div>

{{-- Manuscripts Needing Attention --}}
<div class="ds-card" data-aos="fade-up" data-aos-delay="300">
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

    // ApexCharts - Editorial Pipeline
    const pipelineOptions = {
        series: [{
            name: 'Manuscripts',
            data: [
                {{ $stats['submitted'] ?? 0 }}, 
                {{ $stats['under_review'] ?? 0 }}, 
                {{ $stats['revision_required'] ?? 0 }}, 
                {{ $stats['accepted'] ?? 0 }}, 
                {{ $stats['published'] ?? 0 }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 250,
            fontFamily: 'Plus Jakarta Sans, sans-serif',
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: true,
                distributed: true,
                dataLabels: { position: 'bottom' }
            }
        },
        colors: ['#2B6CB0', '#D69E2E', '#ED8936', '#38A169', '#319795'],
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: { colors: ['#fff'] },
            formatter: function (val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val;
            },
            offsetX: 0,
            dropShadow: { enabled: false }
        },
        stroke: { width: 0 },
        xaxis: {
            categories: ['New Submissions', 'Under Review', 'Needs Revision', 'Accepted', 'Published'],
            labels: { show: false },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { show: false }
        },
        grid: { show: false },
        tooltip: {
            theme: 'dark',
            x: { show: false },
            y: {
                title: {
                    formatter: function () {
                        return '';
                    }
                }
            }
        },
        legend: { show: false }
    };

    new ApexCharts(document.querySelector("#pipelineChart"), pipelineOptions).render();
});
</script>
@endpush
@endsection
