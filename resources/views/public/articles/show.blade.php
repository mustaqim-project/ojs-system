{{-- public/articles/show.blade.php --}}
@extends('layouts.app')
@section('content')
<div class="container" style="max-width:1000px;padding:40px 24px;">
  {{-- Breadcrumb --}}
  <div style="font-size:12px;color:#94a3b8;margin-bottom:24px;">
    <a href="{{ route('public.home') }}" style="color:#64748b;text-decoration:none;">Beranda</a> ›
    <a href="{{ route('public.articles.index') }}" style="color:#64748b;text-decoration:none;"> Artikel</a> ›
    <span>{{ Str::limit($article->title,50) }}</span>
  </div>
  <div class="row g-4">
    <div class="col-12 col-lg-8">
      {{-- Article --}}
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">
        <div style="padding:32px 32px 24px;">
          <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
            <a href="{{ route('public.journals.show', $article->journal->slug) }}"
               style="font-size:11px;font-weight:600;color:#2563eb;background:#eff6ff;padding:4px 12px;border-radius:20px;text-decoration:none;">
              {{ $article->journal->title }}
            </a>
            @if($article->issue)<span style="font-size:11px;color:#94a3b8;background:#f8fafc;padding:4px 12px;border-radius:20px;">{{ $article->issue->display_title }}</span>@endif
          </div>
          <h1 style="font-size:22px;font-weight:800;color:#0f172a;letter-spacing:-.03em;line-height:1.3;margin-bottom:20px;">{{ $article->title }}</h1>
          {{-- Author --}}
          <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#f8f9fb;border-radius:10px;margin-bottom:24px;">
            <div style="width:38px;height:38px;border-radius:10px;background:#2563eb;color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex-shrink:0;">
              {{ strtoupper(substr($article->author->name,0,1)) }}
            </div>
            <div style="flex:1;">
              <div style="font-size:13px;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                {{ $article->author->name }}
                @if($article->author->orcid)
                  <x-orcid-badge :orcid="$article->author->orcid"/>
                @endif
              </div>
              @if($article->author->affiliation)<div style="font-size:12px;color:#64748b;">{{ $article->author->affiliation }}</div>@endif
            </div>
            @if($article->published_at)<span style="font-size:12px;color:#94a3b8;margin-left:auto;white-space:nowrap;">{{ $article->published_at->format('d M Y') }}</span>@endif
          </div>
          {{-- Abstract --}}
          <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:10px;">Abstrak</div>
            <p style="font-size:13px;color:#475569;line-height:1.8;margin:0;">{{ $article->abstract }}</p>
          </div>
          {{-- Keywords --}}
          @if($article->keywords)
          <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:10px;">Kata Kunci</div>
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
              @foreach($article->keywords_array as $kw)
              <span style="font-size:12px;background:#f1f5f9;color:#475569;padding:4px 10px;border-radius:6px;border:1px solid #e2e8f0;">{{ $kw }}</span>
              @endforeach
            </div>
          </div>
          @endif
          {{-- DOI --}}
          @if($article->doi)
          <div style="padding:12px 14px;background:#f8f9fb;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;">
            <span style="font-weight:600;color:#475569;">DOI:</span>
            <span style="color:#2563eb;font-family:'Courier New',monospace;margin-left:6px;">{{ $article->doi }}</span>
          </div>
          @endif
        </div>
        {{-- Footer --}}
        <div style="padding:14px 32px;background:#f8f9fb;border-top:1px solid #e2e8f0;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
          <span style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:5px;"><i class="bi bi-translate"></i> {{ strtoupper($article->language) }}</span>
          @if($article->pages_start)<span style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:5px;"><i class="bi bi-file-text"></i> Hal. {{ $article->pages_start }}–{{ $article->pages_end }}</span>@endif
          <div style="margin-left:auto;">
            <span style="font-size:11px;background:#ecfdf5;color:#047857;padding:3px 10px;border-radius:20px;font-weight:600;"><i class="bi bi-check-circle-fill me-1" style="font-size:9px;"></i>Published</span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      {{-- Article info --}}
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:16px;">
        <div style="padding:14px 20px;border-bottom:1px solid #e2e8f0;font-size:12px;font-weight:700;color:#0f172a;">Informasi Artikel</div>
        <div>
          @if($article->published_at)<div style="padding:10px 20px;border-bottom:1px solid #f8f9fb;display:flex;justify-content:space-between;font-size:12px;"><span style="color:#94a3b8;">Dipublikasi</span><span style="font-weight:600;color:#0f172a;">{{ $article->published_at->format('d M Y') }}</span></div>@endif
          <div style="padding:10px 20px;border-bottom:1px solid #f8f9fb;display:flex;justify-content:space-between;font-size:12px;"><span style="color:#94a3b8;">Bahasa</span><span style="font-weight:600;color:#0f172a;">{{ strtoupper($article->language) }}</span></div>
          @if($article->pages_start)<div style="padding:10px 20px;display:flex;justify-content:space-between;font-size:12px;"><span style="color:#94a3b8;">Halaman</span><span style="font-weight:600;color:#0f172a;">{{ $article->pages_start }}–{{ $article->pages_end }}</span></div>@endif
        </div>
      </div>
      {{-- Related --}}
      @if($related->count())
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        <div style="padding:14px 20px;border-bottom:1px solid #e2e8f0;font-size:12px;font-weight:700;color:#0f172a;">Artikel Terkait</div>
        <div>
          @foreach($related as $rel)
          <div style="padding:12px 20px;border-bottom:1px solid #f8f9fb;">
            <a href="{{ route('public.articles.show', $rel->slug) }}" style="font-size:12px;font-weight:600;color:#2563eb;text-decoration:none;line-height:1.4;display:block;">{{ Str::limit($rel->title,60) }}</a>
            <span style="font-size:11px;color:#94a3b8;">{{ $rel->author->name }}</span>
          </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
