@extends('layouts.app')
@section('title', $page['title'] ?? 'Panggilan untuk Makalah')
@section('meta_description', $page['meta_description'] ?? '')
@section('content')

<section style="background:linear-gradient(135deg, var(--bg-app) 0%, #fff 100%); padding:60px 0; border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1000px; text-align:center;">
    <div class="section-tag" data-aos="fade-up">Informasi</div>
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" style="font-size:36px; margin-bottom:16px;">
      {{ $page['title'] ?? 'Panggilan untuk Makalah' }}
    </h1>
    <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin:0 auto;">
      {{ $page['meta_description'] ?? 'Kami mengundang para peneliti, akademisi, dan praktisi untuk mengirimkan penelitian asli mereka.' }}
    </p>
  </div>
</section>

<section class="section" style="background:var(--bg-app);">
  <div class="container" style="max-width:1000px;">

    {{-- Deadline / Volume / Issue info from extra --}}
    @php
    $deadline = $page['extra']['deadline'] ?? '';
    $volume   = $page['extra']['volume']   ?? '';
    $issue    = $page['extra']['issue']    ?? '';
    $theme    = $page['extra']['theme']    ?? '';
    @endphp

    @if($deadline || $volume || $issue || $theme)
    <div class="row g-3 mb-4" data-aos="fade-up">
      @if($deadline)
      <div class="col-md-3">
        <div class="pub-card text-center" style="padding:20px;">
          <i class="bi bi-calendar-event" style="font-size:28px;color:var(--danger);"></i>
          <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-top:8px;">Batas Waktu</div>
          <div style="font-size:16px;font-weight:700;color:var(--text-main);">{{ \Carbon\Carbon::parse($deadline)->locale('id')->translatedFormat('d M Y') }}</div>
        </div>
      </div>
      @endif
      @if($volume)
      <div class="col-md-3">
        <div class="pub-card text-center" style="padding:20px;">
          <i class="bi bi-journals" style="font-size:28px;color:var(--primary);"></i>
          <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-top:8px;">Volume</div>
          <div style="font-size:16px;font-weight:700;color:var(--text-main);">{{ $volume }}</div>
        </div>
      </div>
      @endif
      @if($issue)
      <div class="col-md-3">
        <div class="pub-card text-center" style="padding:20px;">
          <i class="bi bi-file-earmark-text" style="font-size:28px;color:var(--primary);"></i>
          <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-top:8px;">Terbitan</div>
          <div style="font-size:16px;font-weight:700;color:var(--text-main);">{{ $issue }}</div>
        </div>
      </div>
      @endif
      @if($theme)
      <div class="col-md-3">
        <div class="pub-card text-center" style="padding:20px;">
          <i class="bi bi-lightbulb" style="font-size:28px;color:var(--warning);"></i>
          <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-top:8px;">Tema</div>
          <div style="font-size:14px;font-weight:700;color:var(--text-main);">{{ $theme }}</div>
        </div>
      </div>
      @endif
    </div>
    @endif

    {{-- Dynamic body or default CTA --}}
    @if(!empty($page['body']))
    <div class="pub-card mb-4" data-aos="fade-up">
      <div style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
    </div>
    @endif

    <div class="pub-card text-center py-5" data-aos="zoom-in">
      <div style="width:80px; height:80px; background:var(--primary-light); color:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:36px; margin:0 auto 24px;">
        <i class="bi bi-send-check"></i>
      </div>
      <h3 style="font-weight:700; margin-bottom:16px;">Pengajuan Naskah Kini Terbuka!</h3>
      <p style="color:var(--text-muted); font-size:16px; line-height:1.7; max-width:600px; margin:0 auto 32px;">
        Kami saat ini menerima pengajuan naskah untuk terbitan kami yang akan datang. Pastikan naskah Anda mematuhi Panduan Penulis kami.
      </p>
      <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('public.author-guidelines') }}" class="btn btn-outline-primary px-4 py-2" style="font-weight:600; border-radius:8px;">Baca Panduan</a>
        <a href="{{ route('register') }}" class="btn btn-primary px-4 py-2" style="font-weight:600; border-radius:8px;">
          Ajukan Naskah Sekarang <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </div>

  </div>
</section>

@endsection
