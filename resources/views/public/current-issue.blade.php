@extends('layouts.app')
@section('content')

<section style="background:linear-gradient(135deg, var(--bg-app) 0%, #fff 100%); padding:60px 0; border-bottom:1px solid var(--border);">
  <div class="container" style="max-width:1200px; text-align:center;">
    <div class="section-tag" data-aos="fade-up">Jelajahi</div>
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" style="font-size:36px; margin-bottom:16px;">{{ $page['title'] ?? 'Terbitan Saat Ini' }}</h1>
    <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin:0 auto;">{{ $page['meta_description'] ?? 'Jelajahi artikel-artikel terbaru yang telah ditelaah sejawat dan diterbitkan dalam jurnal kami.' }}</p>
  </div>
</section>

<section class="section" style="background:var(--bg-app);">
  <div class="container" style="max-width:1200px;">
    
    <div class="pub-card" data-aos="fade-up">
      @if($currentIssue)
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:24px;">
          <h4 style="font-weight:700; margin:0;">
            Volume {{ $currentIssue->volume->number ?? '' }}, No. {{ $currentIssue->number }} ({{ $currentIssue->volume->year ?? '' }})
          </h4>
          @if($currentIssue->published_date)
            <span class="badge bg-primary text-white px-3 py-2" style="border-radius:20px; font-weight:600;">
              Diterbitkan: {{ $currentIssue->published_date->locale('id')->translatedFormat('F Y') }}
            </span>
          @endif
        </div>

        <div style="display:flex; flex-direction:column; gap:20px;">
          @forelse($currentIssue->articles as $article)
            <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:flex-start; justify-content:space-between; gap:20px; transition:background 0.2s;" onmouseover="this.style.background='var(--bg-app)'" onmouseout="this.style.background='transparent';">
              <div style="flex:1; min-width:0;">
                <h3 style="font-size:18px; font-weight:700; color:var(--text-main); margin-bottom:8px; line-height:1.4;">
                  <a href="{{ route('public.articles.show', $article->slug) }}" style="color:inherit; text-decoration:none;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'">
                    {{ $article->title }}
                  </a>
                </h3>
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                  <span style="font-size:12px; color:var(--text-muted); font-weight:500;">
                    Oleh <strong style="color:var(--text-main);">{{ $article->author->name ?? 'Penulis Tidak Diketahui' }}</strong>
                  </span>
                </div>
                <div style="display:flex; gap:16px; font-size:12px; color:var(--text-muted);">
                  <span title="Dilihat"><i class="bi bi-eye me-1"></i> {{ $article->views_count }}</span>
                  <span title="Diunduh"><i class="bi bi-download me-1"></i> {{ $article->downloads_count }}</span>
                </div>
              </div>
              <div style="display:flex; flex-direction:column; gap:8px; align-items:flex-end;">
                <a href="{{ route('public.articles.show', $article->slug) }}" class="btn btn-sm btn-outline-primary" style="font-weight:600; border-radius:6px;">Detail</a>
              </div>
            </div>
          @empty
            <p class="text-muted">Tidak ada artikel di terbitan ini.</p>
          @endforelse
        </div>
      @else
        <div class="alert alert-info d-flex align-items-center" role="alert" style="background:var(--primary-light); border-color:var(--primary-light); color:var(--primary); border-radius:var(--radius-md);">
          <i class="bi bi-info-circle-fill me-3" style="font-size:24px;"></i>
          <div>
            Bagian ini akan memuat secara dinamis artikel-artikel yang diterbitkan dalam terbitan terbaru. Silakan periksa kembali nanti atau jelajahi <a href="{{ route('public.articles.index') }}" style="color:var(--primary); font-weight:700;">semua artikel</a>.
          </div>
        </div>
      @endif
    </div>
  </div>
</section>

@endsection
