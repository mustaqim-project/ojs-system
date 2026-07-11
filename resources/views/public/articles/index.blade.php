{{-- public/articles/index.blade.php --}}
@extends('layouts.app')
@section('content')
<div style="background:#fff;border-bottom:1px solid #e2e8f0;padding:40px 0 32px;">
  <div class="container" style="max-width:1200px;">
    <div style="font-size:12px;color:#94a3b8;margin-bottom:6px;"><a href="{{ route('public.home') }}" style="color:#64748b;text-decoration:none;">Beranda</a> › Artikel</div>
    <h1 style="font-size:28px;font-weight:800;color:#0f172a;letter-spacing:-.04em;margin-bottom:4px;">Semua Artikel</h1>
    <p style="font-size:14px;color:#64748b;margin:0;">{{ $articles->total() }} artikel ilmiah terpublish</p>
  </div>
</div>
<div class="container" style="max-width:1200px;padding:32px 24px;">
  <div class="row g-4">
    <div class="col-12 col-lg-8">
      <div style="display:flex;flex-direction:column;gap:12px;">
        @forelse($articles as $article)
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;transition:all .2s;"
             onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.07)';this.style.borderColor='#bfdbfe';"
             onmouseout="this.style.boxShadow='';this.style.borderColor='#e2e8f0';">
          <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;flex-wrap:wrap;">
            <a href="{{ route('public.journals.show', $article->journal->slug) }}"
               style="font-size:11px;font-weight:600;color:#2563eb;background:#eff6ff;padding:3px 10px;border-radius:20px;text-decoration:none;">
              {{ $article->journal->abbreviation ?? $article->journal->title }}
            </a>
            @if($article->issue)<span style="font-size:11px;color:#94a3b8;background:#f8fafc;padding:3px 10px;border-radius:20px;">{{ $article->issue->display_title }}</span>@endif
            @if($article->published_at)<span style="font-size:11px;color:#94a3b8;">{{ $article->published_at->format('d M Y') }}</span>@endif
          </div>
          <h2 style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:8px;line-height:1.4;">
            <a href="{{ route('public.articles.show', $article->slug) }}" style="color:inherit;text-decoration:none;">{{ $article->title }}</a>
          </h2>
          <p style="font-size:12px;color:#64748b;margin-bottom:12px;line-height:1.7;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $article->abstract }}</p>
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <div style="width:22px;height:22px;border-radius:6px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">
              {{ strtoupper(substr($article->author->name,0,1)) }}
            </div>
            <span style="font-size:12px;color:#475569;font-weight:500;">{{ $article->author->name }}</span>
            @if($article->author->affiliation)<span style="font-size:12px;color:#94a3b8;">· {{ $article->author->affiliation }}</span>@endif
            @if($article->keywords)
            <div style="margin-left:auto;display:flex;gap:4px;flex-wrap:wrap;">
              @foreach(array_slice($article->keywords_array,0,3) as $kw)
              <span style="font-size:10px;background:#f1f5f9;color:#64748b;padding:2px 7px;border-radius:4px;">{{ $kw }}</span>
              @endforeach
            </div>
            @endif
          </div>
        </div>
        @empty
        <div style="text-align:center;padding:60px;color:#94a3b8;">
          <i class="bi bi-file-earmark-text" style="font-size:40px;display:block;margin-bottom:12px;"></i>
          Belum ada artikel terpublish.
        </div>
        @endforelse
      </div>
      <div class="mt-4">{{ $articles->links() }}</div>
    </div>
    <div class="col-12 col-lg-4">
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:16px;position:sticky;top:80px;">
        <h3 style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px;">Cari Artikel</h3>
        <form action="{{ route('public.search') }}" method="GET">
          <div style="display:flex;gap:8px;">
            <input type="text" name="q" placeholder="Judul, abstrak, keyword..."
                   style="flex:1;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;"/>
            <button type="submit" style="padding:8px 14px;background:#2563eb;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-family:inherit;">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
