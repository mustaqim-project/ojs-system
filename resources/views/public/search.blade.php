{{-- public/search.blade.php --}}
@extends('layouts.app')
@section('content')

{{-- Search header --}}
<div style="background:#fff;border-bottom:1px solid #e2e8f0;padding:40px 0 32px;">
  <div class="container" style="max-width:900px;">
    <h1 style="font-size:24px;font-weight:800;color:#0f172a;letter-spacing:-.04em;margin-bottom:20px;">
      Cari Artikel
    </h1>
    <form action="{{ route('public.search') }}" method="GET">
      <div style="display:flex;gap:10px;">
        <div style="flex:1;position:relative;">
          <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px;"></i>
          <input type="text" name="q" value="{{ $query }}"
                 placeholder="Cari judul, abstrak, kata kunci, atau author..."
                 style="width:100%;padding:11px 14px 11px 42px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-family:'Plus Jakarta Sans',sans-serif;color:#0f172a;outline:none;transition:border-color .15s;"
                 onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,.12)'"
                 onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'"/>
        </div>
        <button type="submit"
                style="padding:11px 24px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;display:flex;align-items:center;gap:8px;white-space:nowrap;transition:background .15s;"
                onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
          <i class="bi bi-search"></i> Cari
        </button>
      </div>
    </form>
  </div>
</div>

<div class="container" style="max-width:900px;padding:32px 24px;">

  @if($query)
    {{-- Results header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:8px;">
      <div>
        @if($articles->total() > 0)
          <p style="font-size:14px;color:#475569;margin:0;">
            Menampilkan <strong style="color:#0f172a;">{{ $articles->total() }}</strong> hasil untuk
            "<strong style="color:#2563eb;">{{ $query }}</strong>"
          </p>
        @else
          <p style="font-size:14px;color:#64748b;margin:0;">
            Tidak ada hasil untuk "<strong>{{ $query }}</strong>"
          </p>
        @endif
      </div>
      @if($articles->total() > 0)
      <span style="font-size:12px;color:#94a3b8;">{{ $articles->currentPage() }} dari {{ $articles->lastPage() }} halaman</span>
      @endif
    </div>

    {{-- Results --}}
    @forelse($articles as $article)
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;margin-bottom:12px;transition:all .2s;"
         onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.07)';this.style.borderColor='#bfdbfe';"
         onmouseout="this.style.boxShadow='';this.style.borderColor='#e2e8f0';">
      <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;flex-wrap:wrap;">
        <a href="{{ route('public.journals.show', $article->journal->slug) }}"
           style="font-size:11px;font-weight:600;color:#2563eb;background:#eff6ff;padding:3px 10px;border-radius:20px;text-decoration:none;">
          {{ $article->journal->abbreviation ?? $article->journal->title }}
        </a>
        @if($article->issue)
          <span style="font-size:11px;color:#94a3b8;background:#f8fafc;padding:3px 10px;border-radius:20px;">
            {{ $article->issue->display_title }}
          </span>
        @endif
        @if($article->published_at)
          <span style="font-size:11px;color:#94a3b8;">{{ $article->published_at->format('d M Y') }}</span>
        @endif
        <span style="font-size:11px;font-weight:600;color:#047857;background:#ecfdf5;padding:3px 10px;border-radius:20px;margin-left:auto;">Published</span>
      </div>

      <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:8px;line-height:1.4;">
        <a href="{{ route('public.articles.show', $article->slug) }}"
           style="color:inherit;text-decoration:none;"
           onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='inherit'">
          {{-- Highlight search term in title --}}
          {!! preg_replace('/('.preg_quote($query,'/').')/iu', '<mark style="background:#fef9c3;padding:1px 2px;border-radius:2px;">$1</mark>', e($article->title)) !!}
        </a>
      </h2>

      <p style="font-size:13px;color:#64748b;margin-bottom:12px;line-height:1.7;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
        {{-- Highlight search term in abstract --}}
        {!! preg_replace('/('.preg_quote($query,'/').')/iu', '<mark style="background:#fef9c3;padding:1px 2px;border-radius:2px;">$1</mark>', e(Str::limit($article->abstract, 200))) !!}
      </p>

      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        {{-- Author --}}
        <div style="display:flex;align-items:center;gap:6px;">
          <div style="width:20px;height:20px;border-radius:5px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;flex-shrink:0;">
            {{ strtoupper(substr($article->author->name, 0, 1)) }}
          </div>
          <span style="font-size:12px;color:#475569;font-weight:500;">{{ $article->author->name }}</span>
          @if($article->author->affiliation)
            <span style="font-size:12px;color:#94a3b8;">· {{ Str::limit($article->author->affiliation, 30) }}</span>
          @endif
        </div>

        {{-- Keywords --}}
        @if($article->keywords)
        <div style="display:flex;gap:4px;flex-wrap:wrap;margin-left:auto;">
          @foreach(array_slice($article->keywords_array, 0, 3) as $kw)
          <span style="font-size:10px;background:#f1f5f9;color:#64748b;padding:2px 7px;border-radius:4px;">{{ $kw }}</span>
          @endforeach
        </div>
        @endif
      </div>

      {{-- DOI --}}
      @if($article->doi)
      <div style="margin-top:8px;font-size:11px;color:#94a3b8;font-family:'Courier New',monospace;">
        DOI: {{ $article->doi }}
      </div>
      @endif
    </div>
    @empty
    <div style="text-align:center;padding:60px 24px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;">
      <div style="width:52px;height:52px;border-radius:14px;background:#f1f5f9;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px;color:#94a3b8;">
        <i class="bi bi-search"></i>
      </div>
      <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:6px;">Tidak Ada Hasil</h3>
      <p style="font-size:13px;color:#64748b;margin-bottom:20px;line-height:1.7;">
        Tidak ditemukan artikel untuk "<strong>{{ $query }}</strong>".<br/>
        Coba kata kunci yang berbeda atau lebih umum.
      </p>
      {{-- Suggestions --}}
      <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ route('public.articles.index') }}"
           style="padding:8px 16px;border-radius:7px;font-size:13px;font-weight:600;background:#eff6ff;color:#2563eb;text-decoration:none;">
          <i class="bi bi-journal-text me-1"></i> Lihat Semua Artikel
        </a>
        <a href="{{ route('public.journals.index') }}"
           style="padding:8px 16px;border-radius:7px;font-size:13px;font-weight:600;background:#f1f5f9;color:#0f172a;text-decoration:none;">
          <i class="bi bi-journals me-1"></i> Jelajahi Jurnal
        </a>
      </div>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($articles->hasPages())
    <div style="margin-top:24px;">{{ $articles->appends(['q' => $query])->links() }}</div>
    @endif

  @else
    {{-- Empty state — no query yet --}}
    <div style="text-align:center;padding:64px 24px;">
      <div style="width:64px;height:64px;border-radius:16px;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:28px;color:#2563eb;">
        <i class="bi bi-search"></i>
      </div>
      <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px;">Cari Artikel Ilmiah</h2>
      <p style="font-size:14px;color:#64748b;margin-bottom:28px;max-width:400px;margin-left:auto;margin-right:auto;line-height:1.7;">
        Gunakan kotak pencarian di atas untuk menemukan artikel berdasarkan judul, abstrak, kata kunci, atau nama penulis.
      </p>
      {{-- Popular keywords --}}
      <div style="margin-bottom:28px;">
        <p style="font-size:12px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">Pencarian Populer</p>
        <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
          @foreach(['machine learning','deep learning','IoT','natural language processing','computer vision','data mining'] as $kw)
          <a href="{{ route('public.search', ['q' => $kw]) }}"
             style="padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;background:#fff;color:#0f172a;border:1px solid #e2e8f0;text-decoration:none;transition:all .15s;"
             onmouseover="this.style.borderColor='#2563eb';this.style.color='#2563eb';"
             onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#0f172a';">
            {{ $kw }}
          </a>
          @endforeach
        </div>
      </div>
      {{-- Quick links --}}
      <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ route('public.articles.index') }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;background:#2563eb;color:#fff;text-decoration:none;">
          <i class="bi bi-file-earmark-text"></i> Semua Artikel
        </a>
        <a href="{{ route('public.journals.index') }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;background:#fff;color:#0f172a;border:1px solid #e2e8f0;text-decoration:none;">
          <i class="bi bi-journals"></i> Jelajahi Jurnal
        </a>
      </div>
    </div>
  @endif
</div>
@endsection
