@extends('layouts.app')
@section('content')

{{-- PAGE HEADER --}}
<section style="background:linear-gradient(135deg, var(--bg-app) 0%, #fff 100%); padding:60px 0; border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1000px; text-align:center;">
    <div class="section-tag" data-aos="fade-up">About Journal</div>
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" style="font-size:36px; margin-bottom:16px;">{{ $page['title'] ?? 'Focus &amp; Scope' }}</h1>
    <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin:0 auto;">{{ $page['meta_description'] ?? 'Areas of interest and scientific domains covered by the journal.' }}</p>
  </div>
</section>

{{-- MAIN CONTENT --}}
<section class="section" style="background:var(--bg-app);">
  <div class="container" style="max-width:1000px;">
    
    @if(!empty($page['body']))
    <div class="pub-card mb-4" data-aos="fade-up">
      <div style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
    </div>
    @else
    <div class="pub-card" data-aos="fade-up">
      <div style="font-size:16px; color:var(--text-main); line-height:1.7;">
        <p>Our journal publishes high-quality, peer-reviewed research across a broad spectrum of scientific disciplines. We aim to disseminate impactful findings that advance knowledge, inform policy, and drive innovation globally.</p>
        
        <h4 style="margin:32px 0 16px; font-weight:700;">Core Subject Areas</h4>
        <div class="row g-4 mt-2">
          
          <div class="col-md-6">
            <div style="display:flex; gap:16px; align-items:flex-start; padding:20px; background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-md);">
              <div style="width:48px; height:48px; border-radius:12px; background:var(--primary-light); color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                <i class="bi bi-laptop"></i>
              </div>
              <div>
                <h5 style="font-weight:700; margin-bottom:8px;">Computer Science & IT</h5>
                <p style="font-size:14px; color:var(--text-muted); margin:0;">Artificial Intelligence, Machine Learning, Data Science, Software Engineering, and Cybersecurity.</p>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div style="display:flex; gap:16px; align-items:flex-start; padding:20px; background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-md);">
              <div style="width:48px; height:48px; border-radius:12px; background:var(--success-bg); color:var(--success); display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                <i class="bi bi-heart-pulse"></i>
              </div>
              <div>
                <h5 style="font-weight:700; margin-bottom:8px;">Health & Medicine</h5>
                <p style="font-size:14px; color:var(--text-muted); margin:0;">Public Health, Clinical Research, Biomedical Engineering, and Pharmacology.</p>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div style="display:flex; gap:16px; align-items:flex-start; padding:20px; background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-md);">
              <div style="width:48px; height:48px; border-radius:12px; background:var(--warning-bg); color:#d97706; display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                <i class="bi bi-globe"></i>
              </div>
              <div>
                <h5 style="font-weight:700; margin-bottom:8px;">Social Sciences</h5>
                <p style="font-size:14px; color:var(--text-muted); margin:0;">Economics, Sociology, Psychology, Education, and Policy Studies.</p>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div style="display:flex; gap:16px; align-items:flex-start; padding:20px; background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-md);">
              <div style="width:48px; height:48px; border-radius:12px; background:#e0e7ff; color:#4f46e5; display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                <i class="bi bi-lightning-charge"></i>
              </div>
              <div>
                <h5 style="font-weight:700; margin-bottom:8px;">Engineering & Tech</h5>
                <p style="font-size:14px; color:var(--text-muted); margin:0;">Mechanical, Civil, Electrical, Materials Science, and Renewable Energy.</p>
              </div>
            </div>
          </div>
          
        </div>

        <h4 style="margin:40px 0 16px; font-weight:700;">Types of Articles Accepted</h4>
        <ul style="list-style-type:square; padding-left:20px; color:var(--text-muted);">
          <li style="margin-bottom:8px;"><strong style="color:var(--text-main);">Original Research Articles:</strong> Comprehensive reports of original scientific discoveries.</li>
          <li style="margin-bottom:8px;"><strong style="color:var(--text-main);">Review Articles:</strong> In-depth syntheses of existing literature on specific topics.</li>
          <li style="margin-bottom:8px;"><strong style="color:var(--text-main);">Case Studies:</strong> Detailed analyses of specific instances or phenomena in real-world contexts.</li>
          <li style="margin-bottom:8px;"><strong style="color:var(--text-main);">Short Communications:</strong> Brief reports of significant new findings.</li>
        </ul>
      </div>
    </div>
    @endif

  </div>
</section>

@endsection
