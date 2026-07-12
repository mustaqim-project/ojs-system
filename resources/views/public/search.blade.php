@extends('layouts.app')
@section('content')

{{-- Search Header --}}
<div style="background:var(--bg-surface);border-bottom:1px solid var(--border);padding:60px 0 40px;">
  <div class="container" style="max-width:900px;">
    <h1 style="font-size:32px;font-weight:800;color:var(--text-main);letter-spacing:-0.03em;margin-bottom:24px;text-align:center;">
      Cari Artikel
    </h1>
    <form action="{{ route('public.search') }}" method="GET" style="max-width:700px;margin:0 auto;">
      <div style="display:flex;gap:12px;background:var(--bg-surface);padding:8px;border-radius:16px;border:1px solid var(--border);box-shadow:0 10px 30px -10px rgba(0,0,0,0.08);">
        <div style="flex:1;position:relative;">
          <i class="bi bi-search" style="position:absolute;left:20px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:18px;"></i>
          <input type="text" name="q" value="{{ $query }}"
                 placeholder="Cari berdasarkan judul, abstrak, kata kunci, atau nama penulis..."
                 style="width:100%;height:52px;padding:0 20px 0 54px;border:none;background:transparent;font-size:16px;color:var(--text-main);outline:none;"/>
        </div>
        <button type="submit" class="ds-btn ds-btn-pri" style="height:52px;padding:0 32px;font-size:15px;border-radius:10px;">
          Cari
        </button>
      </div>
    </form>
  </div>
</div>

<div class="container" style="max-width:900px;padding:48px 24px;">

  @if($query)
    {{-- Results Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;padding-bottom:16px;border-bottom:1px solid var(--border);">
      <div>
        @if($articles->total() > 0)
          <h2 style="font-size:16px;color:var(--text-main);margin:0;font-weight:600;">
            Ditemukan {{ $articles->total() }} hasil untuk "<span style="color:var(--primary);">{{ $query }}</span>"
          </h2>
        @else
          <h2 style="font-size:16px;color:var(--text-muted);margin:0;font-weight:500;">
            Tidak ditemukan hasil untuk "{{ $query }}"
          </h2>
        @endif
      </div>
      @if($articles->total() > 0)
      <span style="font-size:13px;color:var(--text-muted);font-weight:500;">Halaman {{ $articles->currentPage() }} dari {{ $articles->lastPage() }}</span>
      @endif
    </div>

    {{-- Results List --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
      @forelse($articles as $article)
      <div style="background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;transition:all 0.2s;"
           onmouseover="this.style.boxShadow='var(--shadow-md)';this.style.borderColor='rgba(37,99,235,0.4)';"
           onmouseout="this.style.boxShadow='none';this.style.borderColor='var(--border)';">
        
        <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px;flex-wrap:wrap;">
          <a href="{{ route('public.journals.show', $article->journal->slug) }}"
             style="font-size:11px;font-weight:700;color:var(--primary);background:var(--primary-light);padding:4px 12px;border-radius:20px;text-decoration:none;">
            {{ $article->journal->abbreviation ?? $article->journal->title }}
          </a>
          @if($article->issue)
            <span style="font-size:11px;font-weight:600;color:var(--text-muted);background:var(--bg-app);border:1px solid var(--border);padding:3px 10px;border-radius:20px;">
              {{ $article->issue->display_title }}
            </span>
          @endif
          @if($article->published_at)
            <span style="font-size:12px;color:var(--text-muted);font-weight:500;">
              <i class="bi bi-calendar3 me-1"></i>{{ $article->published_at->format('M d, Y') }}
            </span>
          @endif
        </div>

        <h3 style="font-size:18px;font-weight:700;color:var(--text-main);margin-bottom:12px;line-height:1.4;">
          <a href="{{ route('public.articles.show', $article->slug) }}"
             style="color:inherit;text-decoration:none;"
             onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'">
            {!! preg_replace('/('.preg_quote($query,'/').')/iu', '<mark style="background:var(--warning-bg);color:#B45309;padding:2px 4px;border-radius:4px;font-weight:inherit;">$1</mark>', e($article->title)) !!}
          </a>
        </h3>

        <p style="font-size:14px;color:var(--text-muted);margin-bottom:16px;line-height:1.7;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
          {!! preg_replace('/('.preg_quote($query,'/').')/iu', '<mark style="background:var(--warning-bg);color:#B45309;padding:2px 4px;border-radius:4px;">$1</mark>', e(Str::limit($article->abstract, 300))) !!}
        </p>

        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-top:1px solid var(--border);padding-top:16px;">
          <div style="display:flex;align-items:center;gap:8px;">
            <div style="width:26px;height:26px;border-radius:6px;background:var(--bg-app);border:1px solid var(--border);color:var(--text-muted);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">
              {{ strtoupper(substr($article->author->name, 0, 1)) }}
            </div>
            <span style="font-size:13px;color:var(--text-main);font-weight:600;">{{ $article->author->name }}</span>
          </div>

          <div style="display:flex;align-items:center;gap:16px;">
            @if($article->doi)
            <div style="font-size:12px;color:var(--text-muted);font-family:monospace;background:var(--bg-app);padding:4px 8px;border-radius:4px;">
              DOI: {{ $article->doi }}
            </div>
            @endif
          </div>
        </div>
      </div>
      @empty
      <div style="padding:60px 24px;">
        <x-ui.empty-state icon="bi-search" title="Tidak Ditemukan Hasil" description="Coba sesuaikan kata kunci Anda atau gunakan istilah pencarian yang lebih luas."/>
        
        <div style="display:flex;gap:12px;justify-content:center;margin-top:24px;">
          <a href="{{ route('public.articles.index') }}" class="ds-btn ds-btn-out">
            <i class="bi bi-file-earmark-text"></i> Telusuri Semua Artikel
          </a>
          <a href="{{ route('public.journals.index') }}" class="ds-btn ds-btn-out">
            <i class="bi bi-journals"></i> Jelajahi Jurnal
          </a>
        </div>
      </div>
      @endforelse
    </div>

    {{-- Pagination --}}
    @if($articles->hasPages())
    <div style="margin-top:32px;">{{ $articles->appends(['q' => $query])->links() }}</div>
    @endif

  @else
    {{-- Empty State - No Query --}}
    <div style="text-align:center;padding:80px 24px;">
      <div style="width:80px;height:80px;border-radius:24px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:36px;color:var(--primary);">
        <i class="bi bi-search"></i>
      </div>
      <h2 style="font-size:24px;font-weight:800;color:var(--text-main);margin-bottom:12px;">Temukan Penelitian Akademik</h2>
      <p style="font-size:15px;color:var(--text-muted);margin-bottom:40px;max-width:480px;margin-left:auto;margin-right:auto;line-height:1.7;">
        Gunakan bilah pencarian di atas untuk mencari di basis data artikel tinjauan sejawat kami yang ekstensif berdasarkan judul, abstrak, atau nama penulis.
      </p>
      
      {{-- Popular Keywords --}}
      <div style="margin-bottom:40px;">
        <div style="font-size:12px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:16px;">Topik Tren</div>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
          @foreach(['Machine Learning','Artificial Intelligence','IoT','Cybersecurity','Data Science','Cloud Computing'] as $kw)
          <a href="{{ route('public.search', ['q' => $kw]) }}"
             style="padding:8px 16px;border-radius:20px;font-size:13px;font-weight:600;background:var(--bg-surface);color:var(--text-main);border:1px solid var(--border);text-decoration:none;transition:all 0.2s;"
             onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)';"
             onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-main)';">
            {{ $kw }}
          </a>
          @endforeach
        </div>
      </div>
      
      <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ route('public.articles.index') }}" class="ds-btn ds-btn-pri" style="height:44px;padding:0 24px;">
          <i class="bi bi-file-text"></i> Lihat Semua Artikel
        </a>
        <a href="{{ route('public.journals.index') }}" class="ds-btn ds-btn-out" style="height:44px;padding:0 24px;">
          <i class="bi bi-journals"></i> Jelajahi Jurnal
        </a>
      </div>
    </div>
  @endif
</div>
@endsection
