@extends('layouts.app')
@section('content')

{{-- PAGE HEADER --}}
<section style="background:linear-gradient(135deg, var(--bg-app) 0%, #fff 100%); padding:60px 0; border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1000px; text-align:center;">
    <div class="section-tag" data-aos="fade-up">Tentang Jurnal</div>
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" style="font-size:36px; margin-bottom:16px;">{{ $page['title'] ?? 'Fokus &amp; Ruang Lingkup' }}</h1>
    <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin:0 auto;">{{ $page['meta_description'] ?? 'Bidang minat dan domain ilmiah yang dicakup oleh jurnal.' }}</p>
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
        <p>Jurnal kami menerbitkan penelitian berkualitas tinggi yang ditelaah sejawat di berbagai spektrum disiplin ilmu ilmiah. Kami bertujuan untuk menyebarluaskan temuan berdampak yang memajukan pengetahuan, menginformasikan kebijakan, dan mendorong inovasi secara global.</p>
        
        <h4 style="margin:32px 0 16px; font-weight:700;">Bidang Subjek Inti</h4>
        <div class="row g-4 mt-2">
          
          <div class="col-md-6">
            <div style="display:flex; gap:16px; align-items:flex-start; padding:20px; background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-md);">
              <div style="width:48px; height:48px; border-radius:12px; background:var(--primary-light); color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                <i class="bi bi-laptop"></i>
              </div>
              <div>
                <h5 style="font-weight:700; margin-bottom:8px;">Ilmu Komputer & TI</h5>
                <p style="font-size:14px; color:var(--text-muted); margin:0;">Kecerdasan Buatan, Pembelajaran Mesin, Ilmu Data, Rekayasa Perangkat Lunak, dan Keamanan Siber.</p>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div style="display:flex; gap:16px; align-items:flex-start; padding:20px; background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-md);">
              <div style="width:48px; height:48px; border-radius:12px; background:var(--success-bg); color:var(--success); display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                <i class="bi bi-heart-pulse"></i>
              </div>
              <div>
                <h5 style="font-weight:700; margin-bottom:8px;">Kesehatan & Kedokteran</h5>
                <p style="font-size:14px; color:var(--text-muted); margin:0;">Kesehatan Masyarakat, Penelitian Klinis, Teknik Biomedis, dan Farmakologi.</p>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div style="display:flex; gap:16px; align-items:flex-start; padding:20px; background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-md);">
              <div style="width:48px; height:48px; border-radius:12px; background:var(--warning-bg); color:#d97706; display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                <i class="bi bi-globe"></i>
              </div>
              <div>
                <h5 style="font-weight:700; margin-bottom:8px;">Ilmu Sosial</h5>
                <p style="font-size:14px; color:var(--text-muted); margin:0;">Ilmu Ekonomi, Sosiologi, Psikologi, Pendidikan, dan Studi Kebijakan.</p>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div style="display:flex; gap:16px; align-items:flex-start; padding:20px; background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-md);">
              <div style="width:48px; height:48px; border-radius:12px; background:#e0e7ff; color:#4f46e5; display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                <i class="bi bi-lightning-charge"></i>
              </div>
              <div>
                <h5 style="font-weight:700; margin-bottom:8px;">Teknik & Teknologi</h5>
                <p style="font-size:14px; color:var(--text-muted); margin:0;">Teknik Mesin, Sipil, Elektro, Ilmu Material, dan Energi Terbarukan.</p>
              </div>
            </div>
          </div>
          
        </div>

        <h4 style="margin:40px 0 16px; font-weight:700;">Jenis Artikel yang Diterima</h4>
        <ul style="list-style-type:square; padding-left:20px; color:var(--text-muted);">
          <li style="margin-bottom:8px;"><strong style="color:var(--text-main);">Artikel Penelitian Asli:</strong> Laporan komprehensif tentang penemuan ilmiah asli.</li>
          <li style="margin-bottom:8px;"><strong style="color:var(--text-main);">Artikel Ulasan:</strong> Sintesis mendalam dari literatur yang ada pada topik tertentu.</li>
          <li style="margin-bottom:8px;"><strong style="color:var(--text-main);">Studi Kasus:</strong> Analisis mendetail tentang contoh atau fenomena tertentu dalam konteks dunia nyata.</li>
          <li style="margin-bottom:8px;"><strong style="color:var(--text-main);">Komunikasi Singkat:</strong> Laporan singkat tentang temuan baru yang signifikan.</li>
        </ul>
      </div>
    </div>
    @endif

  </div>
</section>

@endsection
