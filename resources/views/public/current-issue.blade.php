@extends('layouts.app')
@section('content')

<section style="background:linear-gradient(135deg, var(--bg-app) 0%, #fff 100%); padding:60px 0; border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1200px; text-align:center;">
    <div class="section-tag" data-aos="fade-up">Browse</div>
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" style="font-size:36px; margin-bottom:16px;">{{ $page['title'] ?? 'Current Issue' }}</h1>
    <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin:0 auto;">{{ $page['meta_description'] ?? 'Explore the latest peer-reviewed articles published in our journal.' }}</p>
  </div>
</section>

<section class="section" style="background:var(--bg-app);">
  <div class="container" style="max-width:1200px;">
    
    <div class="pub-card" data-aos="fade-up">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:24px;">
        <h4 style="font-weight:700; margin:0;">Volume 14, No. 2 (2026)</h4>
        <span class="badge bg-primary text-white px-3 py-2" style="border-radius:20px; font-weight:600;">Published: July 2026</span>
      </div>

      <div class="alert alert-info d-flex align-items-center" role="alert" style="background:var(--primary-light); border-color:var(--primary-light); color:var(--primary); border-radius:var(--radius-md);">
        <i class="bi bi-info-circle-fill me-3" style="font-size:24px;"></i>
        <div>
          This section will dynamically load the articles assigned to the latest published issue. Please check back soon or browse <a href="{{ route('public.articles.index') }}" style="color:var(--primary); font-weight:700;">all articles</a>.
        </div>
      </div>

    </div>
  </div>
</section>

@endsection
