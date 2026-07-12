@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'API Integrations']]"/>
    <h1 class="ds-page-title">API Integrations</h1>
    <p class="ds-page-subtitle">Manage connections to external services: ORCID, Crossref DOI, Google OAuth, and more.</p>
  </div>
</div>

{{-- Security Notice --}}
<div class="ds-alert ds-alert-info" data-aos="fade-up" style="margin-bottom:24px;max-width:900px;">
  <i class="bi bi-shield-lock-fill"></i>
  <div style="font-size:13px;">
    <strong>Security Notice:</strong>
    All credentials marked <span style="background:#FEF2F2;color:#C53030;border-radius:4px;padding:1px 6px;font-size:11px;font-weight:700;font-family:monospace;">SECRET</span>
    are stored encrypted (AES-256) in the database — never in <code>.env</code> files. Raw values are never displayed after saving.
  </div>
</div>

{{-- Provider Grid --}}
<div class="row g-3">
  @foreach($providerMeta as $providerKey => $meta)
  @php
    $providerFields = $providers->get($providerKey, collect());
    $isActive       = $providerFields->where('status','active')->count() > 0;
    $filledCount    = $providerFields->filter(fn($f) => !empty($f->getRawOriginal('value')))->count();
    $totalCount     = $providerFields->count();
  @endphp
  <div class="col-12 col-md-6 col-xl-4 fd{{ $loop->index + 1 }}" data-aos="fade-up">
    <div style="background:var(--bg-surface);border:1px solid var(--border);border-top:3px solid {{ $meta['color'] }};border-radius:10px;height:100%;display:flex;flex-direction:column;transition:all 0.2s;overflow:hidden;"
         onmouseover="this.style.boxShadow='var(--shadow-lg)';this.style.borderColor='{{ $meta['color'] }}'"
         onmouseout="this.style.boxShadow='none';this.style.borderTopColor='{{ $meta['color'] }}';this.style.borderRightColor='var(--border)';this.style.borderBottomColor='var(--border)';this.style.borderLeftColor='var(--border)'">
      {{-- Card Header --}}
      <div style="padding:20px 20px 14px;display:flex;align-items:flex-start;gap:14px;">
        <div style="width:42px;height:42px;border-radius:10px;background:{{ $meta['color'] }}18;color:{{ $meta['color'] }};display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
          <i class="{{ $meta['icon'] }}"></i>
        </div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:15px;font-weight:700;color:var(--text-main);">{{ $meta['label'] }}</div>
          <span style="font-size:11px;font-weight:700;background:{{ $meta['color'] }}18;color:{{ $meta['color'] }};padding:1px 8px;border-radius:10px;display:inline-block;margin-top:3px;">{{ $meta['badge'] }}</span>
        </div>
        @if($isActive)
          <span style="font-size:11px;font-weight:600;color:var(--success);background:var(--success-bg);padding:3px 10px;border-radius:20px;flex-shrink:0;">● Active</span>
        @else
          <span style="font-size:11px;font-weight:600;color:var(--text-muted);background:var(--bg-app);padding:3px 10px;border-radius:20px;flex-shrink:0;">○ Inactive</span>
        @endif
      </div>

      {{-- Description + Progress --}}
      <div style="padding:0 20px 16px;flex:1;">
        <p style="font-size:13px;color:var(--text-muted);line-height:1.6;margin:0 0 14px;">{{ $meta['description'] }}</p>
        @if($totalCount > 0)
        <div>
          <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:11px;color:var(--text-muted);">Configuration</span>
            <span style="font-size:11px;font-weight:600;color:var(--text-main);">{{ $filledCount }}/{{ $totalCount }} fields</span>
          </div>
          <div style="height:4px;background:var(--bg-app);border-radius:4px;overflow:hidden;">
            <div style="height:100%;width:{{ $totalCount > 0 ? round($filledCount/$totalCount*100) : 0 }}%;background:{{ $meta['color'] }};border-radius:4px;transition:width 0.4s;"></div>
          </div>
        </div>
        @endif
      </div>

      {{-- Footer Actions --}}
      <div style="padding:12px 16px;border-top:1px solid var(--border);background:var(--bg-app);display:flex;gap:8px;">
        <a href="{{ route('admin.integrations.show', $providerKey) }}" class="ds-btn ds-btn-pri ds-btn-sm" style="flex:1;justify-content:center;">
          <i class="bi bi-sliders"></i> Configure
        </a>
        @if($meta['docs_url'])
        <a href="{{ $meta['docs_url'] }}" target="_blank" class="ds-btn ds-btn-out ds-btn-sm" title="Documentation">
          <i class="bi bi-box-arrow-up-right"></i>
        </a>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Info Footer --}}
<div style="margin-top:32px;padding:20px 24px;background:var(--bg-surface);border:1px solid var(--border);border-radius:10px;max-width:900px;" class="" data-aos="fade-up">
  <div style="font-size:14px;font-weight:700;color:var(--text-main);margin-bottom:10px;">
    <i class="bi bi-info-circle me-2" style="color:var(--info);"></i>About API Integrations
  </div>
  <ul style="font-size:13px;color:var(--text-muted);margin:0;padding-left:18px;line-height:2;">
    <li>All credentials are stored encrypted (AES-256) in the database, <strong>not</strong> in <code>.env</code> files</li>
    <li>Changes take effect immediately — no server restart required</li>
    <li>Each provider can be enabled or disabled independently</li>
    <li>Use the <strong>Test Connection</strong> button on the configuration page to verify credentials</li>
  </ul>
</div>

@endsection
