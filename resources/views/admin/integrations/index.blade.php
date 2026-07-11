@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <h2 class="pg-title">Integrasi API</h2>
    <p class="pg-desc">Kelola koneksi ke layanan eksternal: ORCID, Crossref DOI, OAI-PMH, dan lainnya.</p>
  </div>
</div>

{{-- Info Box --}}
<div class="alert-o a-info fu mb-3" style="max-width:820px;">
  <i class="bi bi-shield-lock-fill"></i>
  <div style="font-size:13px;">
    <strong>Keamanan Credentials:</strong>
    Semua nilai bertanda <span style="background:#fef2f2;color:#dc2626;border-radius:4px;padding:1px 6px;font-size:11px;font-weight:700;">SECRET</span>
    disimpan terenkripsi (AES-256) di database. Nilai asli tidak pernah ditampilkan di UI setelah disimpan.
  </div>
</div>

{{-- Provider Grid --}}
<div class="row g-3">
  @foreach($providerMeta as $providerKey => $meta)
  @php
    $providerFields = $providers->get($providerKey, collect());
    $activeCount    = $providerFields->where('status','active')->count();
    $totalCount     = $providerFields->count();
    $isActive       = $activeCount > 0;
    $filledCount    = $providerFields->filter(fn($f) => !empty($f->getRawOriginal('value')))->count();
  @endphp
  <div class="col-12 col-md-6 col-xl-4 fu fd{{ $loop->index + 1 }}">
    <div class="card-ojs h-100" style="border-top: 3px solid {{ $meta['color'] }};">
      <div class="card-hdr" style="padding:16px 18px 12px;">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="width:38px;height:38px;border-radius:9px;background:{{ $meta['color'] }}18;color:{{ $meta['color'] }};display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
            <i class="{{ $meta['icon'] }}"></i>
          </div>
          <div>
            <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $meta['label'] }}</div>
            <span style="font-size:10px;font-weight:700;background:{{ $meta['color'] }}18;color:{{ $meta['color'] }};padding:1px 7px;border-radius:10px;">{{ $meta['badge'] }}</span>
          </div>
          <div style="margin-left:auto;">
            @if($isActive)
              <span class="bx bx-published" style="font-size:10px;">Active</span>
            @else
              <span class="bx bx-gray" style="font-size:10px;">Inactive</span>
            @endif
          </div>
        </div>
      </div>

      <div style="padding:0 18px 12px;">
        <p style="font-size:12px;color:#64748b;line-height:1.5;margin:0 0 12px;">{{ $meta['description'] }}</p>

        {{-- Progress bar konfigurasi --}}
        @if($totalCount > 0)
        <div style="margin-bottom:10px;">
          <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
            <span style="font-size:11px;color:#94a3b8;">Konfigurasi</span>
            <span style="font-size:11px;font-weight:600;color:#0f172a;">{{ $filledCount }}/{{ $totalCount }} field</span>
          </div>
          <div style="height:4px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
            <div style="height:100%;width:{{ $totalCount > 0 ? round($filledCount/$totalCount*100) : 0 }}%;background:{{ $meta['color'] }};border-radius:4px;transition:width .3s;"></div>
          </div>
        </div>
        @endif
      </div>

      <div class="card-ftr" style="display:flex;gap:8px;padding:10px 14px;">
        <a href="{{ route('admin.integrations.show', $providerKey) }}" class="btn-o btn-pri btn-sm" style="flex:1;justify-content:center;">
          <i class="bi bi-sliders"></i> Konfigurasi
        </a>
        @if($meta['docs_url'])
        <a href="{{ $meta['docs_url'] }}" target="_blank" class="btn-o btn-out btn-sm" title="Dokumentasi">
          <i class="bi bi-box-arrow-up-right"></i>
        </a>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Bottom info --}}
<div style="margin-top:32px;padding:16px 20px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;max-width:820px;">
  <div style="font-size:13px;font-weight:600;color:#0f172a;margin-bottom:8px;"><i class="bi bi-info-circle me-2" style="color:#2563eb;"></i>Tentang Integrasi API</div>
  <ul style="font-size:12px;color:#64748b;margin:0;padding-left:16px;line-height:1.8;">
    <li>Semua credentials disimpan di database terenkripsi, <strong>bukan</strong> di file <code>.env</code></li>
    <li>Perubahan berlaku langsung tanpa restart server</li>
    <li>Setiap provider dapat diaktifkan/nonaktifkan secara independen</li>
    <li>Gunakan tombol <strong>Test Koneksi</strong> di halaman konfigurasi untuk verifikasi</li>
  </ul>
</div>
@endsection
