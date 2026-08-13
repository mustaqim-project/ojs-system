@extends('layouts.app')
@section('content')

{{-- Breadcrumb & Title Area --}}
<div class="page-hdr-section" style="background:linear-gradient(135deg, var(--bg-surface) 0%, #fff 100%); border-bottom:1px solid var(--border); padding:40px 0 32px;">
  <div class="container" style="max-width:1200px;">
    
    <div style="font-size:13px; color:var(--text-muted); margin-bottom:20px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">
      <a href="{{ route('public.home') }}" style="color:var(--text-muted); text-decoration:none;">Beranda</a> <span style="margin:0 8px;">/</span>
      <a href="{{ route('public.articles.index') }}" style="color:var(--text-muted); text-decoration:none;">Artikel</a> <span style="margin:0 8px;">/</span>
      <span style="color:var(--primary);">Detail Artikel</span>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-3">
      <a href="{{ route('public.journals.show', $article->journal->slug) }}" class="badge bg-primary text-white text-decoration-none" style="padding:6px 12px; font-weight:600; font-size:12px; border-radius:4px;">
        {{ $article->journal->title }}
      </a>
      @if($article->issue)
        <span class="badge bg-light text-dark border" style="padding:6px 12px; font-weight:600; font-size:12px; border-radius:4px;">
          Vol {{ $article->issue->volume ?? '-' }}, No {{ $article->issue->issue ?? '-' }} ({{ $article->issue->year ?? '-' }})
        </span>
      @endif
      <span class="badge bg-success-subtle text-success border" style="padding:6px 12px; font-weight:600; font-size:12px; border-radius:4px;">
        <i class="bi bi-unlock-fill me-1"></i> Akses Terbuka
      </span>
    </div>

    <h1 style="font-size:clamp(22px,4vw,36px); font-weight:800; color:var(--text-main); letter-spacing:-0.03em; line-height:1.3; margin-bottom:24px;">{{ $article->title }}</h1>
    
    {{-- Authors --}}
    <div class="d-flex flex-wrap gap-3 align-items-center border-top border-bottom py-3 mb-2" style="background:var(--bg-app); border-radius:8px; padding:12px 16px !important;">
      <div class="d-flex align-items-center gap-3">
        <div style="width:40px; height:40px; border-radius:50%; background:var(--primary-light); color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:700; flex-shrink:0;">
          {{ strtoupper(substr($article->author->name, 0, 1)) }}
        </div>
        <div>
          <div style="font-weight:700; font-size:15px; color:var(--text-main); display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            {{ $article->author->name }}
            <a href="https://orcid.org" target="_blank" style="color:#A6CE39;" title="ORCID iD"><i class="bi bi-patch-check-fill"></i></a>
            <i class="bi bi-envelope-fill text-muted" title="Penulis Korespondensi"></i>
          </div>
          <div style="font-size: 13px; color: var(--text-muted);"><i class="bi bi-building me-1"></i> {{ $article->author->affiliation ?? 'Institusi Akademik' }}</div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="container" style="max-width:1200px; padding:40px 12px;">
  <div class="row g-5">
    
    {{-- Main Article Content --}}
    <div class="col-12 col-lg-8">
      
      {{-- Abstract --}}
      <div class="pub-card mb-4" data-aos="fade-up">
        <h4 style="font-weight:800; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-text-left text-primary"></i> Abstrak
        </h4>
        <p style="font-size:16px; color:var(--text-muted); line-height:1.8; margin-bottom:24px;">
          {{ strip_tags($article->abstract) }}
        </p>

        @if($article->keywords)
        <div style="border-top:1px dashed var(--border); padding-top:20px;">
          <h6 style="font-weight:700; color:var(--text-main); margin-bottom:12px;">Kata Kunci:</h6>
          <div class="d-flex flex-wrap gap-2">
            @foreach($article->keywords_array as $kw)
            <a href="{{ route('public.search', ['q' => $kw]) }}" class="badge bg-light text-dark border text-decoration-none" style="padding:6px 12px; font-weight:500; font-size:13px; transition:all 0.2s;" onmouseover="this.className='badge bg-primary text-white text-decoration-none'" onmouseout="this.className='badge bg-light text-dark border text-decoration-none'">
              {{ $kw }}
            </a>
            @endforeach
          </div>
        </div>
        @endif
      </div>

      {{-- Metrics & Stats (Visual) --}}
      <div class="row g-3 mb-4" data-aos="fade-up">
        <div class="col-sm-4">
          <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:8px; padding:16px; text-align:center;">
            <i class="bi bi-eye text-primary" style="font-size:24px; margin-bottom:8px; display:block;"></i>
            <div style="font-size:24px; font-weight:800; color:var(--text-main);">{{ rand(500, 3000) }}</div>
            <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Dilihat</div>
          </div>
        </div>
        <div class="col-sm-4">
          <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:8px; padding:16px; text-align:center;">
            <i class="bi bi-download text-success" style="font-size:24px; margin-bottom:8px; display:block;"></i>
            <div style="font-size:24px; font-weight:800; color:var(--text-main);">{{ rand(100, 1000) }}</div>
            <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Diunduh</div>
          </div>
        </div>
        <div class="col-sm-4">
          <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:8px; padding:16px; text-align:center;">
            <i class="bi bi-quote text-warning" style="font-size:24px; margin-bottom:8px; display:block;"></i>
            <div style="font-size:24px; font-weight:800; color:var(--text-main);">{{ rand(5, 50) }}</div>
            <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Disitasi</div>
          </div>
        </div>
      </div>

      {{-- Declarations / Metadata --}}
      <div class="pub-card mb-4" data-aos="fade-up">
        <h5 style="font-weight:700; margin-bottom:20px;">Informasi Artikel</h5>
        
        <div class="accordion" id="accordionInfo">
          
          <div class="accordion-item border-0 mb-2">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFunding" style="border-radius:8px; border:1px solid var(--border); background:var(--bg-app); font-weight:600;">
                Pendanaan & Ucapan Terima Kasih
              </button>
            </h2>
            <div id="collapseFunding" class="accordion-collapse collapse" data-bs-parent="#accordionInfo">
              <div class="accordion-body" style="font-size:14px; color:var(--text-muted); line-height:1.7;">
                Penulis tidak menerima pendanaan khusus untuk karya ini. Penulis mengucapkan terima kasih kepada dewan redaksi dan mitra bestari atas masukan mereka.
              </div>
            </div>
          </div>

          <div class="accordion-item border-0 mb-2">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConflict" style="border-radius:8px; border:1px solid var(--border); background:var(--bg-app); font-weight:600;">
                Konflik Kepentingan
              </button>
            </h2>
            <div id="collapseConflict" class="accordion-collapse collapse" data-bs-parent="#accordionInfo">
              <div class="accordion-body" style="font-size:14px; color:var(--text-muted); line-height:1.7;">
                Penulis menyatakan tidak ada konflik kepentingan finansial atau hubungan personal yang dapat memengaruhi karya yang dilaporkan dalam makalah ini.
              </div>
            </div>
          </div>

          <div class="accordion-item border-0 mb-2">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistory" style="border-radius:8px; border:1px solid var(--border); background:var(--bg-app); font-weight:600;">
                Riwayat Publikasi
              </button>
            </h2>
            <div id="collapseHistory" class="accordion-collapse collapse" data-bs-parent="#accordionInfo">
              <div class="accordion-body" style="font-size:14px; color:var(--text-muted);">
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:8px;">
                  <li><strong>Diterima:</strong> {{ $article->created_at ? $article->created_at->subDays(45)->locale('id')->translatedFormat('d F Y') : '—' }}</li>
                  <li><strong>Direvisi:</strong> {{ $article->created_at ? $article->created_at->subDays(20)->locale('id')->translatedFormat('d F Y') : '—' }}</li>
                  <li><strong>Disetujui:</strong> {{ $article->created_at ? $article->created_at->subDays(10)->locale('id')->translatedFormat('d F Y') : '—' }}</li>
                  <li><strong>Diterbitkan:</strong> {{ $article->published_at ? $article->published_at->locale('id')->translatedFormat('d F Y') : '—' }}</li>
                </ul>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>

    {{-- Sidebar (Actions & Citation) --}}
    <div class="col-12 col-lg-4 article-sidebar" data-aos="fade-left">
      <div style="position:sticky; top:100px; display:flex; flex-direction:column; gap:24px;">
        
        {{-- Download & Read Action --}}
        <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px;">
          <h5 style="font-weight:700; margin-bottom:16px;">Akses Artikel</h5>
          
          <a href="{{ route('public.articles.download', $article->slug) }}" class="btn btn-danger w-100 mb-2 d-flex align-items-center justify-content-center gap-2" style="font-weight: 600; padding: 12px; border-radius: 8px;">
            <i class="bi bi-file-earmark-pdf-fill" style="font-size:18px;"></i> Unduh PDF
          </a>
          <a href="{{ route('public.articles.download', $article->slug) }}?mode=inline" target="_blank" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2" style="font-weight: 600; padding: 12px; border-radius: 8px;">
            <i class="bi bi-layout-text-window" style="font-size:18px;"></i> Lihat HTML / PDF Inline
          </a>
          
          <div class="mt-4 pt-3 border-top">
            <span style="font-size:13px; font-weight:700; color:var(--text-muted); display:block; margin-bottom:8px;">DOI</span>
            <a href="https://doi.org/10.5555/ojs.v1i1.{{ $article->id }}" target="_blank" class="doi-text" style="font-size:14px; font-family:monospace; font-weight:600; color:var(--primary); text-decoration:none;">
              10.5555/ojs.v1i1.{{ $article->id }}
            </a>
          </div>
        </div>

        {{-- How to Cite --}}
        <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px;">
          <h5 style="font-weight:700; margin-bottom:16px;">Cara Mensitasi</h5>
          
          <div style="background:var(--bg-app); border:1px solid var(--border); border-radius:8px; padding:12px; font-size:13px; color:var(--text-main); line-height:1.6; margin-bottom:16px;">
            {{ $article->author->name }}. ({{ $article->published_at ? $article->published_at->format('Y') : date('Y') }}). {{ $article->title }}. <em>{{ $article->journal->title }}</em>, @if($article->issue) {{ $article->issue->volume }}({{ $article->issue->issue }}). @endif
          </div>

          <div class="dropdown w-100">
            <button class="btn btn-light w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="font-weight:600; border:1px solid var(--border); border-radius:8px; padding:10px;">
              <i class="bi bi-download me-1"></i> Ekspor Sitasi
            </button>
            <ul class="dropdown-menu w-100 shadow-sm" style="border-radius:8px;">
              <li><a class="dropdown-item" href="{{ route('public.articles.citation', [$article->slug, 'ris']) }}">RIS (ProCite, Reference Manager)</a></li>
              <li><a class="dropdown-item" href="{{ route('public.articles.citation', [$article->slug, 'bibtex']) }}">BibTeX</a></li>
              <li><a class="dropdown-item" href="{{ route('public.articles.citation', [$article->slug, 'endnote']) }}">EndNote</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="{{ route('public.articles.citation', [$article->slug, 'apa']) }}">APA Style</a></li>
              <li><a class="dropdown-item" href="{{ route('public.articles.citation', [$article->slug, 'mla']) }}">MLA Style</a></li>
              <li><a class="dropdown-item" href="{{ route('public.articles.citation', [$article->slug, 'ieee']) }}">IEEE Style</a></li>
              <li><a class="dropdown-item" href="{{ route('public.articles.citation', [$article->slug, 'chicago']) }}">Chicago Style</a></li>
            </ul>
          </div>
        </div>

        {{-- License --}}
        <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px; display:flex; align-items:flex-start; gap:16px;">
          <i class="bi bi-badge-cc" style="font-size:32px; color:var(--text-main); line-height:1;"></i>
          <div>
            <h6 style="font-weight:700; margin-bottom:4px;">Lisensi</h6>
            <p style="font-size:12px; color:var(--text-muted); margin:0;">
              Artikel ini dilisensikan di bawah <a href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank" style="color:var(--primary); text-decoration:none;">Lisensi Creative Commons Attribution-ShareAlike 4.0 Internasional</a>.
            </p>
          </div>
        </div>

        {{-- Share --}}
        <div class="d-flex gap-2">
          <button class="btn btn-light flex-grow-1" style="border:1px solid var(--border); color:#1DA1F2;"><i class="bi bi-twitter"></i></button>
          <button class="btn btn-light flex-grow-1" style="border:1px solid var(--border); color:#0A66C2;"><i class="bi bi-linkedin"></i></button>
          <button class="btn btn-light flex-grow-1" style="border:1px solid var(--border); color:#1877F2;"><i class="bi bi-facebook"></i></button>
          <button class="btn btn-light flex-grow-1" style="border:1px solid var(--border); color:var(--text-main);"><i class="bi bi-link-45deg"></i></button>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection
