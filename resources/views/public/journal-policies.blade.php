@extends('layouts.app')
@section('content')

{{-- PAGE HEADER --}}
<section style="background:linear-gradient(135deg, var(--bg-app) 0%, #fff 100%); padding:60px 0; border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1000px; text-align:center;">
    <div class="section-tag" data-aos="fade-up">About Journal</div>
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" style="font-size:36px; margin-bottom:16px;">{{ $page['title'] ?? 'Journal Policies' }}</h1>
    <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin:0 auto;">{{ $page['meta_description'] ?? 'Comprehensive guidelines on open access, copyright, archiving, and crossmark policies.' }}</p>
  </div>
</section>

{{-- MAIN CONTENT --}}
<section class="section" style="background:var(--bg-app);">
  <div class="container" style="max-width:1000px;">
    
    @if(!empty($page['body']))
    <div class="pub-card mb-4" data-aos="fade-up">
      <div style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
    </div>
    @endif

    <div class="row g-4">
      <div class="col-md-4">
        <div style="position:sticky; top:100px;">
          <div class="pub-card" data-aos="fade-right">
            <h5 style="font-weight:700; margin-bottom:16px;">Policy Directory</h5>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <a href="#open-access" style="text-decoration:none; color:var(--text-main); font-weight:500;"><i class="bi bi-chevron-right text-primary me-2"></i>Open Access</a>
              <a href="#copyright" style="text-decoration:none; color:var(--text-main); font-weight:500;"><i class="bi bi-chevron-right text-primary me-2"></i>Copyright & License</a>
              <a href="#archiving" style="text-decoration:none; color:var(--text-main); font-weight:500;"><i class="bi bi-chevron-right text-primary me-2"></i>Archiving</a>
              <a href="#plagiarism" style="text-decoration:none; color:var(--text-main); font-weight:500;"><i class="bi bi-chevron-right text-primary me-2"></i>Plagiarism Policy</a>
              <a href="#apc" style="text-decoration:none; color:var(--text-main); font-weight:500;"><i class="bi bi-chevron-right text-primary me-2"></i>Article Processing Charges</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        
        <div class="pub-card mb-4" id="open-access" data-aos="fade-up">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <i class="bi bi-unlock text-primary" style="font-size:24px;"></i>
            <h4 style="font-weight:700; margin:0;">Open Access Policy</h4>
          </div>
          <p style="color:var(--text-muted); line-height:1.7;">This journal provides immediate open access to its content on the principle that making research freely available to the public supports a greater global exchange of knowledge. All published articles are permanently accessible online free of charge.</p>
        </div>

        <div class="pub-card mb-4" id="copyright" data-aos="fade-up">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <i class="bi bi-c-circle text-primary" style="font-size:24px;"></i>
            <h4 style="font-weight:700; margin:0;">Copyright & Licensing</h4>
          </div>
          <p style="color:var(--text-muted); line-height:1.7;">Authors retain the copyright of their manuscripts. Articles are published under the <strong>Creative Commons Attribution-ShareAlike 4.0 International (CC BY-SA 4.0)</strong> license, which permits unrestricted use, distribution, and reproduction in any medium, provided the original work is properly cited.</p>
        </div>

        <div class="pub-card mb-4" id="archiving" data-aos="fade-up">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <i class="bi bi-archive text-primary" style="font-size:24px;"></i>
            <h4 style="font-weight:700; margin:0;">Digital Archiving</h4>
          </div>
          <p style="color:var(--text-muted); line-height:1.7;">To ensure permanent preservation of our published content, this journal utilizes LOCKSS and CLOCKSS systems. Furthermore, all articles are deposited into national and international repositories immediately upon publication.</p>
        </div>

        <div class="pub-card mb-4" id="plagiarism" data-aos="fade-up">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <i class="bi bi-shield-exclamation text-primary" style="font-size:24px;"></i>
            <h4 style="font-weight:700; margin:0;">Plagiarism Policy</h4>
          </div>
          <p style="color:var(--text-muted); line-height:1.7;">The editorial board strictly opposes any form of plagiarism. All submissions are screened using Turnitin / iThenticate prior to review. Manuscripts with a similarity index exceeding 20% will be rejected unconditionally.</p>
        </div>

      </div>
    </div>

  </div>
</section>

@endsection
