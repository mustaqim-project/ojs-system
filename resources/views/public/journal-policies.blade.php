@extends('layouts.app')
@section('content')

{{-- PAGE HEADER --}}
<section style="background:linear-gradient(135deg, var(--bg-app) 0%, #fff 100%); padding:60px 0; border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1000px; text-align:center;">
    <div class="section-tag" data-aos="fade-up">Tentang Jurnal</div>
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" style="font-size:36px; margin-bottom:16px;">{{ $page['title'] ?? 'Kebijakan Jurnal' }}</h1>
    <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin:0 auto;">{{ $page['meta_description'] ?? 'Panduan komprehensif tentang kebijakan akses terbuka, hak cipta, pengarsipan, dan Crossmark.' }}</p>
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
            <h5 style="font-weight:700; margin-bottom:16px;">Direktori Kebijakan</h5>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <a href="#open-access" style="text-decoration:none; color:var(--text-main); font-weight:500;"><i class="bi bi-chevron-right text-primary me-2"></i>Akses Terbuka</a>
              <a href="#copyright" style="text-decoration:none; color:var(--text-main); font-weight:500;"><i class="bi bi-chevron-right text-primary me-2"></i>Hak Cipta & Lisensi</a>
              <a href="#archiving" style="text-decoration:none; color:var(--text-main); font-weight:500;"><i class="bi bi-chevron-right text-primary me-2"></i>Pengarsipan</a>
              <a href="#plagiarism" style="text-decoration:none; color:var(--text-main); font-weight:500;"><i class="bi bi-chevron-right text-primary me-2"></i>Kebijakan Plagiarisme</a>
              <a href="#apc" style="text-decoration:none; color:var(--text-main); font-weight:500;"><i class="bi bi-chevron-right text-primary me-2"></i>Biaya Pemrosesan Artikel</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        
        <div class="pub-card mb-4" id="open-access" data-aos="fade-up">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <i class="bi bi-unlock text-primary" style="font-size:24px;"></i>
            <h4 style="font-weight:700; margin:0;">Kebijakan Akses Terbuka</h4>
          </div>
          <p style="color:var(--text-muted); line-height:1.7;">Jurnal ini menyediakan akses terbuka langsung ke kontennya dengan prinsip bahwa membuat penelitian tersedia secara gratis untuk umum mendukung pertukaran pengetahuan global yang lebih besar. Semua artikel yang diterbitkan dapat diakses secara permanen secara online tanpa biaya.</p>
        </div>

        <div class="pub-card mb-4" id="copyright" data-aos="fade-up">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <i class="bi bi-c-circle text-primary" style="font-size:24px;"></i>
            <h4 style="font-weight:700; margin:0;">Hak Cipta & Lisensi</h4>
          </div>
          <p style="color:var(--text-muted); line-height:1.7;">Penulis mempertahankan hak cipta naskah mereka. Artikel diterbitkan di bawah lisensi <strong>Creative Commons Attribution-ShareAlike 4.0 International (CC BY-SA 4.0)</strong>, yang mengizinkan penggunaan, distribusi, dan reproduksi yang tidak dibatasi dalam media apa pun, asalkan karya asli dikutip dengan benar.</p>
        </div>

        <div class="pub-card mb-4" id="archiving" data-aos="fade-up">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <i class="bi bi-archive text-primary" style="font-size:24px;"></i>
            <h4 style="font-weight:700; margin:0;">Pengarsipan Digital</h4>
          </div>
          <p style="color:var(--text-muted); line-height:1.7;">Untuk memastikan pelestarian permanen konten terbitan kami, jurnal ini menggunakan sistem LOCKSS dan CLOCKSS. Selain itu, semua artikel disetorkan ke repositori nasional dan internasional segera setelah diterbitkan.</p>
        </div>

        <div class="pub-card mb-4" id="plagiarism" data-aos="fade-up">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <i class="bi bi-shield-exclamation text-primary" style="font-size:24px;"></i>
            <h4 style="font-weight:700; margin:0;">Kebijakan Plagiarisme</h4>
          </div>
          <p style="color:var(--text-muted); line-height:1.7;">Dewan redaksi sangat menentang segala bentuk plagiarisme. Semua kiriman disaring menggunakan Turnitin / iThenticate sebelum ditelaah. Naskah dengan indeks kesamaan melebihi 20% akan ditolak tanpa syarat.</p>
        </div>

      </div>
    </div>

  </div>
</section>

@endsection
