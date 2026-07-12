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
        
        <div class="col-md-6 col-lg-4">
          <div style="border:1px solid var(--border); border-radius:var(--radius-md); padding:20px; text-align:center; transition:all 0.3s;" class="hover-shadow">
            <h3 style="font-weight:700; color:var(--text-main); margin-bottom:8px;">2025</h3>
            <p style="color:var(--text-muted); font-size:14px; margin-bottom:16px;">Volume 13</p>
            <div style="display:flex; justify-content:center; gap:8px;">
              <a href="{{ route('public.current-issue') }}" class="btn btn-sm btn-light border">Terbitan 1</a>
              <a href="{{ route('public.current-issue') }}" class="btn btn-sm btn-light border">Terbitan 2</a>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div style="border:1px solid var(--border); border-radius:var(--radius-md); padding:20px; text-align:center; transition:all 0.3s;" class="hover-shadow">
            <h3 style="font-weight:700; color:var(--text-main); margin-bottom:8px;">2024</h3>
            <p style="color:var(--text-muted); font-size:14px; margin-bottom:16px;">Volume 12</p>
            <div style="display:flex; justify-content:center; gap:8px;">
              <a href="{{ route('public.current-issue') }}" class="btn btn-sm btn-light border">Terbitan 1</a>
              <a href="{{ route('public.current-issue') }}" class="btn btn-sm btn-light border">Terbitan 2</a>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div style="border:1px solid var(--border); border-radius:var(--radius-md); padding:20px; text-align:center; transition:all 0.3s;" class="hover-shadow">
            <h3 style="font-weight:700; color:var(--text-main); margin-bottom:8px;">2023</h3>
            <p style="color:var(--text-muted); font-size:14px; margin-bottom:16px;">Volume 11</p>
            <div style="display:flex; justify-content:center; gap:8px;">
              <a href="{{ route('public.current-issue') }}" class="btn btn-sm btn-light border">Terbitan 1</a>
              <a href="{{ route('public.current-issue') }}" class="btn btn-sm btn-light border">Terbitan 2</a>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</section>

@endsection
