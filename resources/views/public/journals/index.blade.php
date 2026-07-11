{{-- ════════════════════════════════════════
   public/journals/index.blade.php
════════════════════════════════════════ --}}
@extends('layouts.app')
@section('content')
<div style="background:#fff;border-bottom:1px solid #e2e8f0;padding:40px 0 32px;">
  <div class="container" style="max-width:1200px;">
    <div style="font-size:12px;color:#94a3b8;margin-bottom:6px;"><a href="{{ route('public.home') }}" style="color:#64748b;text-decoration:none;">Beranda</a> › Jurnal</div>
    <h1 style="font-size:28px;font-weight:800;color:#0f172a;letter-spacing:-.04em;margin-bottom:6px;">Daftar Jurnal</h1>
    <p style="font-size:14px;color:#64748b;margin:0;">{{ $journals->total() }} jurnal ilmiah aktif yang dikelola platform kami</p>
  </div>
</div>
<div class="container" style="max-width:1200px;padding:40px 24px;">
  <div class="row g-3">
    @forelse($journals as $journal)
    <div class="col-12 col-md-6 col-lg-4">
      <a href="{{ route('public.journals.show', $journal->slug) }}"
         style="display:block;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;text-decoration:none;transition:all .2s;height:100%;"
         onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,.08)';this.style.borderColor='#bfdbfe';this.style.transform='translateY(-3px)';"
         onmouseout="this.style.boxShadow='';this.style.borderColor='#e2e8f0';this.style.transform='';">
        <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:14px;">
          <div style="width:44px;height:44px;border-radius:11px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:20px;"><i class="bi bi-journal-text"></i></div>
          @if($journal->abbreviation)
          <span style="font-size:10px;font-family:'Courier New',monospace;background:#f1f5f9;color:#64748b;padding:3px 8px;border-radius:5px;font-weight:600;">{{ $journal->abbreviation }}</span>
          @endif
        </div>
        <h2 style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:6px;line-height:1.4;">{{ $journal->title }}</h2>
        @if($journal->description)
        <p style="font-size:12px;color:#64748b;margin-bottom:14px;line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $journal->description }}</p>
        @endif
        <div style="border-top:1px solid #f1f5f9;padding-top:12px;display:flex;align-items:center;justify-content:space-between;font-size:12px;">
          <div style="color:#94a3b8;">
            @if($journal->issn_online)<span class="me-2">ISSN: {{ $journal->issn_online }}</span>@endif
            <span style="text-transform:capitalize;">{{ $journal->frequency }}</span>
          </div>
          <span style="color:#2563eb;font-weight:700;">{{ $journal->published_articles_count }} artikel</span>
        </div>
      </a>
    </div>
    @empty
    <div class="col-12 text-center py-5" style="color:#94a3b8;"><i class="bi bi-journals" style="font-size:40px;display:block;margin-bottom:12px;"></i>Belum ada jurnal aktif.</div>
    @endforelse
  </div>
  <div class="mt-4">{{ $journals->links() }}</div>
</div>
@endsection
