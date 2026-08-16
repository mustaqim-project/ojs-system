@extends('layouts.app')
@section('content')

{{-- Header --}}
<div style="background:var(--bg-surface);border-bottom:1px solid var(--border);padding:48px 0 32px;">
  <div class="container" style="max-width:1200px;">
    <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;font-weight:500;">
      <a href="{{ route('public.home') }}" style="color:var(--text-muted);text-decoration:none;">Beranda</a> <span style="margin:0 6px;">›</span> <span style="color:var(--text-main);">Jurnal</span>
    </div>
    <h1 style="font-size:32px;font-weight:800;color:var(--text-main);letter-spacing:-0.03em;margin-bottom:8px;">Jurnal Akademik</h1>
    <p style="font-size:15px;color:var(--text-muted);margin:0;">Telusuri {{ $journals->total() }} jurnal ilmiah aktif yang dikelola di platform kami.</p>
  </div>
</div>

<div class="container" style="max-width:1200px;padding:48px 24px;">
  <div class="row g-4">
    @forelse($journals as $journal)
    <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
      <div style="display:flex;flex-direction:column;background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all 0.25s;height:100%;box-shadow:var(--shadow-sm);"
           onmouseover="this.style.boxShadow='var(--shadow-md)';this.style.borderColor='rgba(37,99,235,0.4)';this.style.transform='translateY(-6px)';"
           onmouseout="this.style.boxShadow='var(--shadow-sm)';this.style.borderColor='var(--border)';this.style.transform='none';">
        
        {{-- Cover Image --}}
        <div style="position:relative;height:220px;background:var(--bg-app);overflow:hidden;border-bottom:1px solid var(--border);">
          @if($journal->cover_image)
            <img src="{{ asset($journal->cover_image) }}" alt="{{ $journal->title }}" style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s;"
                 onmouseover="this.style.transform='scale(1.05)';"
                 onmouseout="this.style.transform='scale(1)';"
                 >
          @else
            <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(135deg, var(--primary-light), rgba(37,99,235,0.05));color:var(--primary);gap:12px;">
              <i class="bi bi-journal-bookmark" style="font-size:48px;"></i>
              <span style="font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;opacity:0.8;">No Cover</span>
            </div>
          @endif

          {{-- Abbreviation Badge --}}
          @if($journal->abbreviation)
            <span style="position:absolute;top:16px;right:16px;font-size:11px;font-family:monospace;background:rgba(255,255,255,0.95);color:var(--text-main);padding:6px 12px;border-radius:20px;font-weight:700;box-shadow:0 2px 8px rgba(0,0,0,0.1);border:1px solid rgba(0,0,0,0.05);backdrop-filter:blur(4px);">
              {{ $journal->abbreviation }}
            </span>
          @endif
        </div>

        {{-- Content Info --}}
        <div style="padding:24px;display:flex;flex-direction:column;flex:1;">
          <h2 style="font-size:17px;font-weight:800;color:var(--text-main);margin-bottom:10px;line-height:1.4;">
            <a href="{{ route('public.journals.show', $journal->slug) }}" style="color:inherit;text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'">
              {{ $journal->title }}
            </a>
          </h2>
          
          @if($journal->description)
            <p style="font-size:14px;color:var(--text-muted);margin-bottom:20px;line-height:1.6;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;flex:1;">
              {{ strip_tags($journal->description) }}
            </p>
          @else
            <div style="flex:1;"></div>
          @endif

          <div style="border-top:1px solid var(--border);padding-top:16px;display:flex;align-items:center;justify-content:space-between;font-size:13px;margin-top:auto;">
            <div style="color:var(--text-muted);display:flex;flex-direction:column;gap:2px;">
              @if($journal->issn_online)
                <span style="font-family:monospace;font-size:12px;font-weight:600;"><i class="bi bi-upc-scan me-1 text-primary"></i>E-ISSN: {{ $journal->issn_online }}</span>
              @endif
              <span style="text-transform:capitalize;font-weight:600;color:var(--text-main);"><i class="bi bi-arrow-repeat me-1 text-primary"></i>{{ $journal->frequency }} Edisi</span>
            </div>
            <a href="{{ route('public.journals.show', $journal->slug) }}" class="btn btn-primary btn-sm" style="font-weight:700;border-radius:20px;padding:6px 16px;font-size:12px;box-shadow:0 2px 8px rgba(37,99,235,0.25);">
              Buka Jurnal
            </a>
          </div>
        </div>

      </div>
    </div>
    @empty
    <div class="col-12" style="padding:40px 0;">
      <x-ui.empty-state icon="bi-journals" title="Tidak ada jurnal ditemukan" description="Kami sedang menyiapkan koleksi jurnal kami. Silakan periksa kembali nanti."/>
    </div>
    @endforelse
  </div>

  <div style="margin-top:40px;">
    {{ $journals->links() }}
  </div>
</div>

@endsection
