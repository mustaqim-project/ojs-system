@extends('layouts.app')
@section('content')

{{-- Header --}}
<div class="page-hdr-section" style="background:linear-gradient(135deg, var(--bg-surface) 0%, #fff 100%); border-bottom:1px solid var(--border); padding:60px 0 40px;">
  <div class="container" style="max-width:1400px;">
    <div style="font-size:13px; color:var(--text-muted); margin-bottom:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">
      <a href="{{ route('public.home') }}" style="color:var(--text-muted); text-decoration:none;">Beranda</a> <span style="margin:0 8px;">/</span> <span style="color:var(--primary);">Artikel</span>
    </div>
    <h1 style="font-size:clamp(24px,5vw,42px); font-weight:800; color:var(--text-main); letter-spacing:-0.03em; margin-bottom:12px;">Jelajahi Artikel</h1>
    <p style="font-size:16px; color:var(--text-muted); margin:0; max-width:600px;">Jelajahi {{ $articles->total() }} makalah penelitian ilmiah dari berbagai disiplin akademik.</p>
  </div>
</div>

<div class="container" style="max-width:1400px; padding:40px 12px;">
  <div class="row g-5">
    
    {{-- Advanced Sidebar Filter --}}
    <div class="col-12 col-lg-3" data-aos="fade-right">
      <div style="position:sticky; top:100px;">
        {{-- Mobile toggle button (hidden on desktop) --}}
        <button class="filter-toggle-btn" id="filterToggleBtn" onclick="toggleFilter()" type="button">
          <i class="bi bi-funnel-fill text-primary"></i>
          <span>Filter Artikel</span>
          <i class="bi bi-chevron-down chevron ms-auto"></i>
        </button>

        <div class="filter-sidebar-body" id="filterSidebarBody">
          <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px;">
            
            <h3 style="font-size:16px; font-weight:800; color:var(--text-main); margin-bottom:20px; display:flex; align-items:center; gap:8px; border-bottom:1px solid var(--border); padding-bottom:12px;">
              <i class="bi bi-funnel-fill text-primary"></i> Filter Lanjutan
            </h3>
            
            <form action="{{ route('public.articles.index') }}" method="GET">
              
              {{-- Search --}}
              <div class="mb-4">
                <label style="font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:8px; display:block;">Pencarian</label>
                <div style="position:relative;">
                  <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                  <input type="text" name="q" value="{{ request('q') }}" placeholder="Kata Kunci, DOI, Penulis..." class="form-control shadow-sm" style="padding-left:36px; border-radius:8px;">
                </div>
              </div>

              {{-- Category --}}
              <div class="mb-4">
                <label style="font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:8px; display:block;">Kategori</label>
                <select name="category" class="form-select shadow-sm" style="border-radius:8px;">
                  <option value="">Semua Kategori</option>
                  <option value="research" {{ request('category') === 'research' ? 'selected' : '' }}>Penelitian Asli</option>
                  <option value="review" {{ request('category') === 'review' ? 'selected' : '' }}>Artikel Ulasan</option>
                  <option value="case" {{ request('category') === 'case' ? 'selected' : '' }}>Studi Kasus</option>
                </select>
              </div>

              {{-- Year --}}
              <div class="mb-4">
                <label style="font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:8px; display:block;">Tahun Publikasi</label>
                <div class="d-flex gap-2">
                  <input type="number" name="year_from" value="{{ request('year_from') }}" placeholder="Dari" class="form-control shadow-sm" style="border-radius:8px;">
                  <input type="number" name="year_to" value="{{ request('year_to') }}" placeholder="Sampai" class="form-control shadow-sm" style="border-radius:8px;">
                </div>
              </div>

              {{-- Sort --}}
              <div class="mb-4">
                <label style="font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:8px; display:block;">Urutkan Berdasarkan</label>
                <select name="sort" class="form-select shadow-sm" style="border-radius:8px;">
                  <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru Diterbitkan</option>
                  <option value="views" {{ request('sort') === 'views' ? 'selected' : '' }}>Paling Banyak Dilihat</option>
                  <option value="citations" {{ request('sort') === 'citations' ? 'selected' : '' }}>Paling Banyak Disitasi</option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary w-100 shadow-sm" style="font-weight:600; border-radius:8px;">Terapkan Filter</button>
              <a href="{{ route('public.articles.index') }}" class="btn btn-light w-100 mt-2" style="font-weight:600; border-radius:8px; border:1px solid var(--border);">Atur Ulang</a>

            </form>

          </div>
        </div>
      </div>
    </div>

    {{-- Article List --}}
    <div class="col-12 col-lg-9">
      
      <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h4 style="font-weight:700; font-size:18px; margin:0;">{{ $articles->total() }} Hasil Ditemukan</h4>
        <div class="dropdown">
          <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" style="font-weight:600; border-radius:6px; border:1px solid var(--border);">
            Urutan: {{ request('sort') === 'views' ? 'Paling Banyak Dilihat' : (request('sort') === 'citations' ? 'Paling Banyak Disitasi' : 'Terbaru') }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:8px; font-size:14px;">
            <li><a class="dropdown-item {{ request('sort', 'latest') === 'latest' ? 'active' : '' }}" href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'latest'])) }}">Terbaru</a></li>
            <li><a class="dropdown-item {{ request('sort') === 'views' ? 'active' : '' }}" href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'views'])) }}">Paling Banyak Dilihat</a></li>
            <li><a class="dropdown-item {{ request('sort') === 'citations' ? 'active' : '' }}" href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'citations'])) }}">Paling Banyak Disitasi</a></li>
          </ul>
        </div>
      </div>

      <div style="display:flex; flex-direction:column; gap:24px;">
        @forelse($articles as $article)
        <div class="pub-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}" style="padding:28px;">
          
          <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
              <span class="badge bg-primary-subtle text-primary" style="padding:6px 12px; border-radius:20px; font-weight:700; font-size:11px; letter-spacing:0.5px; text-transform:uppercase;">
                {{ $article->section === 'research' ? 'Penelitian Asli' : ($article->section === 'review' ? 'Artikel Ulasan' : ($article->section === 'case' ? 'Studi Kasus' : 'Artikel Penelitian')) }}
              </span>
              <a href="{{ route('public.journals.show', $article->journal->slug) }}" class="text-decoration-none" style="font-size:13px; font-weight:600; color:var(--text-main);">
                <i class="bi bi-journal-bookmark text-muted me-1"></i> {{ $article->journal->abbreviation ?? $article->journal->title }}
              </a>
              @if($article->issue)
                <span style="font-size:13px; font-weight:500; color:var(--text-muted);">
                  Vol {{ $article->issue->volume ?? 'N/A' }}, No {{ $article->issue->number ?? 'N/A' }} ({{ $article->issue->year ?? 'Year' }})
                </span>
              @endif
            </div>
            
            @if($article->published_at)
              <span style="font-size:13px; color:var(--text-muted); font-weight:600; background:var(--bg-surface); padding:4px 10px; border-radius:6px; border:1px solid var(--border);">
                {{ $article->published_at->locale('id')->translatedFormat('d M Y') }}
              </span>
            @endif
          </div>

          <h2 style="font-size:22px; font-weight:800; color:var(--text-main); margin-bottom:16px; line-height:1.4;">
            <a href="{{ route('public.articles.show', $article->slug) }}" class="hover-primary" style="color:inherit; text-decoration:none;">{{ $article->title }}</a>
          </h2>
          
          <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:14px; color:var(--text-main); font-weight:600;">
            <span><i class="bi bi-person-circle text-muted me-1"></i> {{ $article->author->name }}</span>
            <span class="text-muted">|</span>
            <span style="color:var(--text-muted);"><i class="bi bi-building me-1"></i> {{ $article->author->affiliation ?? 'Institusi Akademik' }}</span>
            <span class="text-muted">|</span>
            <a href="https://orcid.org" target="_blank" style="color:#A6CE39; text-decoration:none;"><i class="bi bi-patch-check-fill"></i> ORCID</a>
          </div>

          <div class="mb-3" style="font-size:12px; color:var(--text-muted); font-family:monospace;">
            <strong>DOI:</strong> <a href="https://doi.org/10.5555/ojs.v1i1.{{ $article->id }}" target="_blank" class="doi-text" style="color:var(--primary); text-decoration:none;">10.5555/ojs.v1i1.{{ $article->id }}</a>
          </div>
          
          <p style="font-size:15px; color:var(--text-muted); margin-bottom:20px; line-height:1.7; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
            {{ strip_tags($article->abstract) }}
          </p>
          
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 border-top pt-3">
            <div class="d-flex gap-2 flex-wrap">
              @foreach(array_slice($article->keywords_array, 0, 4) as $kw)
                <span class="badge bg-light text-dark border" style="font-weight:500; font-size:12px;">{{ $kw }}</span>
              @endforeach
            </div>
            
            <div class="d-flex gap-4" style="font-size:13px; font-weight:600; color:var(--text-muted);">
              <span title="Sitasi" class="d-flex align-items-center gap-1"><i class="bi bi-quote" style="font-size:16px;"></i> {{ rand(0, 50) }} Sitasi</span>
              <span title="Dilihat" class="d-flex align-items-center gap-1"><i class="bi bi-eye" style="font-size:16px;"></i> {{ rand(100, 2000) }}</span>
              <span title="Unduhan" class="d-flex align-items-center gap-1"><i class="bi bi-download" style="font-size:16px;"></i> {{ rand(50, 1000) }}</span>
            </div>
          </div>
          
        </div>
        @empty
        <div class="pub-card text-center py-5">
          <i class="bi bi-journal-x text-muted" style="font-size:48px; margin-bottom:16px; display:block;"></i>
          <h4 style="font-weight:700;">Tidak ada artikel ditemukan</h4>
          <p class="text-muted">Coba sesuaikan filter pencarian Anda.</p>
        </div>
        @endforelse
      </div>
      
      <div class="mt-4">{{ $articles->links() }}</div>
    </div>
    
  </div>
</div>
@endsection

@push('scripts')
<script>
  function toggleFilter() {
    const body = document.getElementById('filterSidebarBody');
    const btn  = document.getElementById('filterToggleBtn');
    const isOpen = body.classList.toggle('open');
    btn.classList.toggle('open', isOpen);
  }
</script>
@endpush
