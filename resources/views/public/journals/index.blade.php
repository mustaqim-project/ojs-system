@extends('layouts.app')
@section('content')

{{-- Header --}}
<div style="background:var(--bg-surface);border-bottom:1px solid var(--border);padding:48px 0 32px;">
  <div class="container" style="max-width:1200px;">
    <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;font-weight:500;">
      <a href="{{ route('public.home') }}" style="color:var(--text-muted);text-decoration:none;">Home</a> <span style="margin:0 6px;">›</span> <span style="color:var(--text-main);">Journals</span>
    </div>
    <h1 style="font-size:32px;font-weight:800;color:var(--text-main);letter-spacing:-0.03em;margin-bottom:8px;">Academic Journals</h1>
    <p style="font-size:15px;color:var(--text-muted);margin:0;">Browse through {{ $journals->total() }} active scientific journals managed on our platform.</p>
  </div>
</div>

<div class="container" style="max-width:1200px;padding:48px 24px;">
  <div class="row g-4">
    @forelse($journals as $journal)
    <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
      <a href="{{ route('public.journals.show', $journal->slug) }}"
         style="display:flex;flex-direction:column;background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px;text-decoration:none;transition:all 0.2s;height:100%;"
         onmouseover="this.style.boxShadow='var(--shadow-md)';this.style.borderColor='rgba(37,99,235,0.4)';this.style.transform='translateY(-4px)';"
         onmouseout="this.style.boxShadow='none';this.style.borderColor='var(--border)';this.style.transform='none';">
        
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;">
          <div style="width:52px;height:52px;border-radius:14px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 2px 8px rgba(37,99,235,0.1);">
            <i class="bi bi-journal-bookmark-fill"></i>
          </div>
          @if($journal->abbreviation)
            <span style="font-size:11px;font-family:monospace;background:var(--bg-app);color:var(--text-muted);padding:4px 10px;border-radius:6px;font-weight:700;border:1px solid var(--border);">
              {{ $journal->abbreviation }}
            </span>
          @endif
        </div>

        <h2 style="font-size:17px;font-weight:700;color:var(--text-main);margin-bottom:12px;line-height:1.4;">{{ $journal->title }}</h2>
        
        @if($journal->description)
        <p style="font-size:14px;color:var(--text-muted);margin-bottom:24px;line-height:1.6;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;flex:1;">
          {{ $journal->description }}
        </p>
        @else
        <div style="flex:1;"></div>
        @endif

        <div style="border-top:1px solid var(--border);padding-top:16px;display:flex;align-items:center;justify-content:space-between;font-size:13px;margin-top:auto;">
          <div style="color:var(--text-muted);display:flex;flex-direction:column;gap:2px;">
            @if($journal->issn_online)
              <span style="font-family:monospace;font-size:12px;">ISSN: {{ $journal->issn_online }}</span>
            @endif
            <span style="text-transform:capitalize;font-weight:500;"><i class="bi bi-arrow-repeat me-1"></i>{{ $journal->frequency }} Issue</span>
          </div>
          <div style="background:var(--bg-app);color:var(--primary);padding:6px 12px;border-radius:20px;font-weight:700;font-size:12px;border:1px solid rgba(37,99,235,0.1);">
            {{ $journal->published_articles_count }} Articles
          </div>
        </div>
      </a>
    </div>
    @empty
    <div class="col-12" style="padding:40px 0;">
      <x-ui.empty-state icon="bi-journals" title="No journals found" description="We are currently setting up our journal collections. Please check back soon."/>
    </div>
    @endforelse
  </div>

  <div style="margin-top:40px;">
    {{ $journals->links() }}
  </div>
</div>

@endsection
