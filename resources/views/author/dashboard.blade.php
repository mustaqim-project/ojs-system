{{-- author/dashboard.blade.php --}}
@extends('layouts.dashboard')
@section('content')

{{-- Page Header --}}
<div class="ds-page-hdr" data-aos="fade-up">
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
<div class="ds-card" style="padding:16px 24px;margin-bottom:24px;" data-aos="fade-up" data-aos-delay="100">
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
<div class="ds-alert ds-alert-warn" data-aos="fade-up" data-aos-delay="200">
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
<div class="ds-alert ds-alert-info" data-aos="fade-up" data-aos-delay="200">
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
<div class="row g-4 mb-4">
  @foreach($cards as $c)
  <div class="col-12 col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="{{ 200 + ($loop->index * 50) }}">
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

{{-- Author Activity Timeline --}}
<div class="row mb-4">
  <div class="col-12" data-aos="fade-up" data-aos-delay="300">
    <div class="ds-card">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Publication Activity</span>
      </div>
      <div class="card-body">
        <div id="activityChart" class="chart-container chart-container-sm"></div>
      </div>
    </div>
  </div>
</div>

{{-- Submissions Table --}}
<div class="ds-card" data-aos="fade-up" data-aos-delay="400">
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

    // ApexCharts - Author Activity Timeline
    const activityOptions = {
        series: [{
            name: 'Submissions',
            data: [1, 0, 2, 1, 3, 2, 1] // Mock data
        }],
        chart: {
            type: 'area',
            height: 250,
            fontFamily: 'Plus Jakarta Sans, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        colors: ['#6B46C1'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'], // Mock categories
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: '#64748b' } }
        },
        yaxis: {
            labels: { style: { colors: '#64748b' } }
        },
        grid: {
            borderColor: 'rgba(226, 232, 240, 0.5)',
            strokeDashArray: 4,
        },
        tooltip: { theme: 'dark' }
    };
    new ApexCharts(document.querySelector("#activityChart"), activityOptions).render();
});
</script>
@endpush
@endsection
