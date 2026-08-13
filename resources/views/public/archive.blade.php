@extends('layouts.app')
@section('content')

<section style="background:linear-gradient(135deg, var(--bg-app) 0%, #fff 100%); padding:60px 0; border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1200px; text-align:center;">
    <div class="section-tag" data-aos="fade-up">Jelajahi</div>
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" style="font-size:36px; margin-bottom:16px;">Arsip</h1>
    <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin:0 auto;">Jelajahi semua terbitan dan volume jurnal kami yang lalu.</p>
  </div>
</section>

<section class="section" style="background:var(--bg-app);">
  <div class="container" style="max-width:1200px;">
    
    <div class="pub-card" data-aos="fade-up">
      <div class="row g-4">
        @forelse($volumes as $volume)
        <div class="col-md-6 col-lg-4">
          <div style="border:1px solid var(--border); border-radius:var(--radius-md); padding:20px; text-align:center; transition:all 0.3s;" class="hover-shadow">
            <h3 style="font-weight:700; color:var(--text-main); margin-bottom:8px;">{{ $volume->year }}</h3>
            <p style="color:var(--text-muted); font-size:14px; margin-bottom:16px;">Volume {{ $volume->number }}</p>
            <div style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap;">
              @forelse($volume->issues as $issue)
                <a href="{{ route('public.current-issue', ['issue' => $issue->id]) }}" class="btn btn-sm btn-light border">Terbitan {{ $issue->number }}</a>
              @empty
                <span class="text-muted small">Belum ada terbitan</span>
              @endforelse
            </div>
          </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-5">
          <i class="bi bi-journal-x" style="font-size:48px;"></i>
          <p class="mt-3">Belum ada volume atau terbitan yang diarsipkan.</p>
        </div>
        @endforelse
      </div>
    </div>

  </div>
</section>

@endsection
