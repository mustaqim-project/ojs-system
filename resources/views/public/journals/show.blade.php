@extends('layouts.app')
@section('content')
{{-- Header --}}
<div style="background:#fff;border-bottom:1px solid #e2e8f0;padding:40px 0 32px;">
  <div class="container" style="max-width:1200px;">
    <div style="font-size:12px;color:#94a3b8;margin-bottom:12px;">
      <a href="{{ route('public.home') }}" style="color:#64748b;text-decoration:none;">Beranda</a> ›
      <a href="{{ route('public.journals.index') }}" style="color:#64748b;text-decoration:none;"> Jurnal</a> ›
      <span>{{ $journal->abbreviation ?? $journal->title }}</span>
    </div>
    <div class="d-flex align-items-start gap-4 flex-wrap">
      <div style="width:60px;height:60px;border-radius:14px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0;">
        <i class="bi bi-journal-text"></i>
      </div>
      <div class="flex-1">
        <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;flex-wrap:wrap;">
          @if($journal->abbreviation)
          <span style="font-size:11px;font-family:'Courier New',monospace;background:#f1f5f9;color:#64748b;padding:3px 9px;border-radius:5px;font-weight:600;">{{ $journal->abbreviation }}</span>
          @endif
          <span style="font-size:11px;background:#f0fdf4;color:#15803d;padding:3px 9px;border-radius:20px;font-weight:600;text-transform:capitalize;">{{ $journal->frequency }}</span>
          <span style="font-size:11px;background:#f0fdf4;color:#047857;padding:3px 9px;border-radius:20px;font-weight:600;">Aktif</span>
        </div>
        <h1 style="font-size:24px;font-weight:800;color:#0f172a;letter-spacing:-.04em;margin-bottom:6px;">{{ $journal->title }}</h1>
        @if($journal->description)
        <p style="font-size:13px;color:#64748b;margin-bottom:10px;line-height:1.7;max-width:600px;">{{ $journal->description }}</p>
        @endif
        <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:#94a3b8;">
          @if($journal->issn_print)<span><strong style="color:#475569;">ISSN Print:</strong> {{ $journal->issn_print }}</span>@endif
          @if($journal->issn_online)<span><strong style="color:#475569;">ISSN Online:</strong> {{ $journal->issn_online }}</span>@endif
          @if($journal->publisher)<span><strong style="color:#475569;">Penerbit:</strong> {{ $journal->publisher }}</span>@endif
          @if($journal->editor)<span><strong style="color:#475569;">Editor:</strong> {{ $journal->editor->name }}</span>@endif
        </div>
      </div>
    </div>
  </div>
</div>
{{-- Issues --}}
<div class="container" style="max-width:1200px;padding:40px 24px;">
  @forelse($journal->issues as $issue)
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:20px;">
    <div style="padding:16px 24px;background:#f8f9fb;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
      <div>
        <h2 style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">{{ $issue->title }}</h2>
        <span style="font-size:12px;color:#94a3b8;">{{ $issue->display_title }}</span>
      </div>
      <div style="display:flex;align-items:center;gap:10px;">
        @if($issue->published_date)<span style="font-size:12px;color:#94a3b8;">{{ $issue->published_date->format('d M Y') }}</span>@endif
        <span style="font-size:11px;background:#ecfdf5;color:#047857;padding:3px 10px;border-radius:20px;font-weight:600;">Published</span>
      </div>
    </div>
    <div>
      @forelse($issue->publishedArticles as $article)
      <div style="padding:14px 24px;border-bottom:1px solid #f8f9fb;display:flex;align-items:start;justify-content:space-between;gap:16px;transition:background .1s;" onmouseover="this.style.background='#fafbff'" onmouseout="this.style.background='';">
        <div style="flex:1;min-width:0;">
          <a href="{{ route('public.articles.show', $article->slug) }}" style="font-size:13px;font-weight:600;color:#2563eb;text-decoration:none;line-height:1.4;display:block;margin-bottom:3px;">{{ $article->title }}</a>
          <span style="font-size:12px;color:#94a3b8;">{{ $article->author->name }}
            @if($article->pages_start) · Hal. {{ $article->pages_start }}–{{ $article->pages_end }}@endif
          </span>
        </div>
        @if($article->doi)<span style="font-size:10px;font-family:'Courier New',monospace;color:#94a3b8;white-space:nowrap;">{{ $article->doi }}</span>@endif
      </div>
      @empty
      <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada artikel di issue ini.</div>
      @endforelse
    </div>
  </div>
  @empty
  <div style="text-align:center;padding:64px 24px;">
    <i class="bi bi-collection" style="font-size:40px;color:#e2e8f0;display:block;margin-bottom:16px;"></i>
    <p style="color:#94a3b8;font-size:13px;margin:0;">Belum ada issue yang dipublish.</p>
  </div>
  @endforelse
</div>
@endsection
