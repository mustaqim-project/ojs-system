@extends('layouts.app')
@section('content')

<style>
/* Landing-specific styles */
.hero{
  background:
    radial-gradient(ellipse 70% 60% at 50% -10%,rgba(37,99,235,.12) 0%,transparent 70%),
    radial-gradient(ellipse 40% 40% at 80% 50%,rgba(99,102,241,.06) 0%,transparent 60%),
    #f8f9fb;
  padding:80px 0 100px;
  overflow:hidden;
  position:relative;
}
.hero::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(rgba(255,255,255,.02) 1px,transparent 1px),
             linear-gradient(90deg,rgba(255,255,255,.02) 1px,transparent 1px);
  background-size:60px 60px;
  pointer-events:none;
}
.hero-tag{
  display:inline-flex;align-items:center;gap:6px;
  background:#fff;border:1px solid #dbeafe;
  padding:5px 14px;border-radius:20px;
  font-size:12px;font-weight:600;color:#2563eb;
  margin-bottom:24px;
  box-shadow:0 1px 4px rgba(37,99,235,.08);
}
.hero-title{
  font-size:clamp(32px,5vw,54px);
  font-weight:800;color:#0f172a;
  letter-spacing:-.05em;line-height:1.1;
  margin-bottom:20px;
}
.hero-title .accent{color:#2563eb;}
.hero-desc{
  font-size:16px;color:#475569;line-height:1.75;
  max-width:520px;margin-bottom:36px;
}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:48px;}
.btn-hero-pri{
  padding:12px 28px;border-radius:8px;font-size:14px;font-weight:700;
  background:#2563eb;color:#fff;border:none;text-decoration:none;
  display:inline-flex;align-items:center;gap:8px;
  transition:all .2s;
}
.btn-hero-pri:hover{background:#1d4ed8;color:#fff;box-shadow:0 8px 24px rgba(37,99,235,.35);transform:translateY(-2px);}
.btn-hero-out{
  padding:12px 28px;border-radius:8px;font-size:14px;font-weight:700;
  background:#fff;color:#0f172a;border:1px solid #e2e8f0;text-decoration:none;
  display:inline-flex;align-items:center;gap:8px;
  transition:all .2s;
}
.btn-hero-out:hover{background:#f4f6f9;color:#0f172a;border-color:#cbd5e1;}

/* Stats bar */
.stats-bar{
  display:flex;gap:0;
  background:#fff;border:1px solid #e2e8f0;
  border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.06);
  overflow:hidden;max-width:480px;
}
.stat-item{
  flex:1;padding:16px 20px;text-align:center;
  border-right:1px solid #e2e8f0;
}
.stat-item:last-child{border-right:none;}
.stat-num{font-size:22px;font-weight:800;color:#0f172a;letter-spacing:-.04em;line-height:1;}
.stat-txt{font-size:11px;color:#94a3b8;font-weight:500;margin-top:3px;}

/* Hero illustration */
.hero-right{
  display:flex;align-items:center;justify-content:center;
  position:relative;
}
.hero-card-float{
  background:#fff;border:1px solid #e2e8f0;
  border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.08);
  padding:16px;width:280px;
}
.hero-card-sm{
  background:#fff;border:1px solid #e2e8f0;
  border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.06);
  padding:12px 14px;
  position:absolute;
}

/* Section */
.section{padding:80px 0;}
.section-tag{font-size:11px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#2563eb;margin-bottom:10px;}
.section-title{font-size:clamp(22px,3vw,32px);font-weight:800;color:#0f172a;letter-spacing:-.04em;line-height:1.2;margin-bottom:12px;}
.section-desc{font-size:15px;color:#475569;line-height:1.75;max-width:480px;}

/* Journal cards */
.journal-card{
  background:#fff;border:1px solid #e2e8f0;border-radius:12px;
  padding:24px;transition:all .2s;text-decoration:none;display:block;height:100%;
}
.journal-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.08);border-color:#bfdbfe;transform:translateY(-3px);}
.j-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:16px;}

/* Article card */
.article-card{
  background:#fff;border:1px solid #e2e8f0;border-radius:12px;
  padding:20px;transition:all .2s;
}
.article-card:hover{box-shadow:0 6px 20px rgba(0,0,0,.07);border-color:#bfdbfe;}

/* Feature */
.feature-item{display:flex;gap:16px;padding:20px;border-radius:12px;transition:background .2s;}
.feature-item:hover{background:#f8f9fb;}
.feature-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;margin-top:2px;}

/* CTA */
.cta-section{background:#0d1117;padding:80px 0;position:relative;overflow:hidden;}
.cta-section::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse 60% 80% at 50% 50%,rgba(37,99,235,.2) 0%,transparent 70%);
}
.cta-title{font-size:clamp(24px,4vw,42px);font-weight:800;color:#fff;letter-spacing:-.04em;line-height:1.15;margin-bottom:16px;}
.cta-desc{font-size:15px;color:#6e7681;line-height:1.75;margin-bottom:36px;}

@media(max-width:768px){
  .hero{padding:48px 0 60px;}
  .hero-right{display:none;}
  .stats-bar{max-width:100%;}
}
</style>

{{-- ── HERO ── --}}
<section class="hero">
  <div class="container" style="max-width:1200px;">
    <div class="row align-items-center g-4">
      <div class="col-12 col-lg-6">
        <div class="hero-tag fu fd1">
          <i class="bi bi-stars"></i> Platform Jurnal Ilmiah Terpercaya
        </div>
        <h1 class="hero-title fu fd2">
          Publikasikan<br>Riset Anda dengan<br><span class="accent">Mudah & Cepat</span>
        </h1>
        <p class="hero-desc fu fd3">{{ \App\Models\Setting::get('site_description','Platform publikasi jurnal ilmiah bereputasi dengan proses review transparan dan sistem pembayaran yang mudah.') }}</p>
        <div class="hero-actions fu fd4">
          @guest
          <a href="{{ route('register') }}" class="btn-hero-pri"><i class="bi bi-arrow-right"></i> Mulai Submit Artikel</a>
          <a href="{{ route('public.journals.index') }}" class="btn-hero-out"><i class="bi bi-journals"></i> Jelajahi Jurnal</a>
          @else
          <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="btn-hero-pri"><i class="bi bi-grid"></i> Ke Dashboard</a>
          <a href="{{ route('public.journals.index') }}" class="btn-hero-out"><i class="bi bi-journals"></i> Jelajahi Jurnal</a>
          @endguest
        </div>
        <div class="stats-bar fu fd5">
          <div class="stat-item">
            <div class="stat-num">{{ $totalPublished }}</div>
            <div class="stat-txt">Artikel Terpublish</div>
          </div>
          <div class="stat-item">
            <div class="stat-num">{{ $totalJournals }}</div>
            <div class="stat-txt">Jurnal Aktif</div>
          </div>
          <div class="stat-item">
            <div class="stat-num">95%</div>
            <div class="stat-txt">Tingkat Terima</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 d-none d-lg-flex fu fd3" style="justify-content:center;position:relative;min-height:400px;">
        {{-- Floating UI Preview --}}
        <div style="position:relative;width:320px;height:380px;">
          {{-- Main card --}}
          <div class="hero-card-float" style="position:absolute;top:40px;left:0;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:10px;">Artikel Terbaru</div>
            @foreach($latestArticles->take(3) as $art)
            <div style="display:flex;gap:10px;padding:8px 0;border-bottom:1px solid #f1f5f9;align-items:center;">
              <div style="width:7px;height:7px;border-radius:50%;background:#2563eb;flex-shrink:0;margin-top:4px;"></div>
              <div>
                <div style="font-size:12px;font-weight:600;color:#0f172a;line-height:1.3;">{{ Str::limit($art->title, 40) }}</div>
                <div style="font-size:11px;color:#94a3b8;">{{ $art->author->name }}</div>
              </div>
            </div>
            @endforeach
          </div>
          {{-- Floating badge --}}
          <div class="hero-card-sm" style="right:-20px;top:10px;">
            <div style="font-size:11px;font-weight:700;color:#15803d;display:flex;align-items:center;gap:5px;">
              <i class="bi bi-check-circle-fill"></i> Artikel Dipublish
            </div>
            <div style="font-size:10px;color:#94a3b8;margin-top:2px;">2 menit yang lalu</div>
          </div>
          {{-- Floating review card --}}
          <div class="hero-card-sm" style="right:10px;bottom:60px;">
            <div style="font-size:11px;font-weight:700;color:#a16207;display:flex;align-items:center;gap:5px;">
              <i class="bi bi-hourglass-split"></i> Under Review
            </div>
            <div style="font-size:10px;color:#94a3b8;margin-top:2px;">3 artikel sedang diproses</div>
          </div>
          {{-- Stats mini --}}
          <div class="hero-card-sm" style="left:-30px;bottom:40px;background:linear-gradient(135deg,#1e40af,#2563eb);">
            <div style="font-size:20px;font-weight:800;color:#fff;letter-spacing:-.04em;">98%</div>
            <div style="font-size:10px;color:#93bbfc;margin-top:2px;">Review accuracy</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── HOW IT WORKS ── --}}
<section class="section" style="background:#fff;">
  <div class="container" style="max-width:1200px;">
    <div class="text-center mb-5 fu">
      <div class="section-tag">Cara Kerja</div>
      <h2 class="section-title" style="max-width:500px;margin:0 auto 12px;">Dari Submit hingga Publish dalam 4 Langkah</h2>
      <p class="section-desc" style="margin:0 auto;text-align:center;">Proses yang transparan, efisien, dan terpercaya untuk setiap peneliti.</p>
    </div>
    <div class="row g-4 fu fd2">
      @php
      $steps=[
        ['num'=>'01','icon'=>'bi-file-earmark-arrow-up','color'=>'#eff6ff','ic'=>'#2563eb','title'=>'Submit Artikel','desc'=>'Upload manuskrip beserta abstrak, kata kunci, dan cover letter melalui portal author.'],
        ['num'=>'02','icon'=>'bi-clipboard2-check','color'=>'#f5f3ff','ic'=>'#7c3aed','title'=>'Proses Review','desc'=>'Editor assign reviewer ahli di bidangnya. Reviewer memberikan feedback konstruktif.'],
        ['num'=>'03','icon'=>'bi-credit-card','color'=>'#f0fdf4','ic'=>'#16a34a','title'=>'Pembayaran APC','desc'=>'Setelah diterima, lakukan pembayaran Article Processing Charge dan upload bukti transfer.'],
        ['num'=>'04','icon'=>'bi-globe2','color'=>'#fff7ed','ic'=>'#ea580c','title'=>'Dipublish Online','desc'=>'Artikel Anda tampil di portal publik, dapat diakses peneliti di seluruh dunia.'],
      ];
      @endphp
      @foreach($steps as $i=>$s)
      <div class="col-6 col-lg-3">
        <div style="position:relative;padding:28px;background:#f8f9fb;border:1px solid #e2e8f0;border-radius:14px;height:100%;">
          <div style="position:absolute;top:20px;right:20px;font-size:36px;font-weight:800;color:#f1f5f9;letter-spacing:-.06em;line-height:1;">{{ $s['num'] }}</div>
          <div style="width:44px;height:44px;border-radius:11px;background:{{ $s['color'] }};display:flex;align-items:center;justify-content:center;font-size:20px;color:{{ $s['ic'] }};margin-bottom:16px;position:relative;z-index:1;">
            <i class="{{ $s['icon'] }}"></i>
          </div>
          <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:6px;position:relative;z-index:1;">{{ $s['title'] }}</h3>
          <p style="font-size:12px;color:#64748b;margin:0;line-height:1.65;position:relative;z-index:1;">{{ $s['desc'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── JOURNALS ── --}}
<section class="section" style="background:#f4f6f9;padding-top:60px;padding-bottom:60px;">
  <div class="container" style="max-width:1200px;">
    <div class="d-flex align-items-end justify-content-between mb-5 fu">
      <div>
        <div class="section-tag">Jurnal Kami</div>
        <h2 class="section-title" style="margin-bottom:0;">Temukan Jurnal yang Tepat</h2>
      </div>
      <a href="{{ route('public.journals.index') }}" class="btn-hero-out" style="padding:8px 16px;font-size:13px;white-space:nowrap;">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-3 fu fd2">
      @forelse($journals as $journal)
      <div class="col-12 col-md-6 col-lg-4">
        <a href="{{ route('public.journals.show', $journal->slug) }}" class="journal-card">
          <div class="j-icon" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-journal-text"></i></div>
          <div style="margin-bottom:6px;">
            @if($journal->abbreviation)
            <span style="font-size:10px;font-family:'Courier New',monospace;background:#f1f5f9;color:#64748b;padding:2px 7px;border-radius:4px;font-weight:600;">{{ $journal->abbreviation }}</span>
            @endif
          </div>
          <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:8px 0 6px;line-height:1.4;">{{ $journal->title }}</h3>
          <p style="font-size:12px;color:#64748b;margin-bottom:14px;line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $journal->description }}</p>
          <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;">
            <span style="color:#94a3b8;capitalize:true;">{{ ucfirst($journal->frequency) }}</span>
            <span style="color:#2563eb;font-weight:600;">{{ $journal->published_articles_count }} artikel</span>
          </div>
        </a>
      </div>
      @empty
      <div class="col-12 text-center py-5" style="color:#94a3b8;">Belum ada jurnal aktif.</div>
      @endforelse
    </div>
  </div>
</section>

{{-- ── LATEST ARTICLES ── --}}
<section class="section" style="background:#fff;padding-top:60px;padding-bottom:60px;">
  <div class="container" style="max-width:1200px;">
    <div class="d-flex align-items-end justify-content-between mb-5 fu">
      <div>
        <div class="section-tag">Terbaru</div>
        <h2 class="section-title" style="margin-bottom:0;">Artikel Terpublish Terbaru</h2>
      </div>
      <a href="{{ route('public.articles.index') }}" class="btn-hero-out" style="padding:8px 16px;font-size:13px;white-space:nowrap;">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-3 fu fd2">
      @forelse($latestArticles as $article)
      <div class="col-12 col-lg-6">
        <div class="article-card">
          <div style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
            <span style="font-size:11px;font-weight:600;color:#2563eb;background:#eff6ff;padding:3px 10px;border-radius:20px;">{{ $article->journal->abbreviation ?? $article->journal->title }}</span>
            @if($article->published_at)
            <span style="font-size:11px;color:#94a3b8;">{{ $article->published_at->format('d M Y') }}</span>
            @endif
          </div>
          <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:8px;line-height:1.45;">
            <a href="{{ route('public.articles.show', $article->slug) }}" style="color:inherit;text-decoration:none;">{{ $article->title }}</a>
          </h3>
          <p style="font-size:12px;color:#64748b;margin-bottom:12px;line-height:1.65;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $article->abstract }}</p>
          <div style="display:flex;align-items:center;gap:8px;">
            <div style="width:22px;height:22px;border-radius:6px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">
              {{ strtoupper(substr($article->author->name,0,1)) }}
            </div>
            <span style="font-size:12px;color:#64748b;font-weight:500;">{{ $article->author->name }}</span>
            @if($article->author->affiliation)
            <span style="font-size:12px;color:#94a3b8;">· {{ Str::limit($article->author->affiliation, 30) }}</span>
            @endif
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center py-5" style="color:#94a3b8;">Belum ada artikel terpublish.</div>
      @endforelse
    </div>
  </div>
</section>

{{-- ── FEATURES ── --}}
<section class="section" style="background:#f4f6f9;padding-top:60px;padding-bottom:60px;">
  <div class="container" style="max-width:1200px;">
    <div class="row g-4 align-items-center">
      <div class="col-12 col-lg-5 fu">
        <div class="section-tag">Fitur Unggulan</div>
        <h2 class="section-title">Sistem OJS yang Lengkap & Terpercaya</h2>
        <p class="section-desc">Dari submission hingga publikasi, semua dalam satu platform yang aman dan transparan.</p>
      </div>
      <div class="col-12 col-lg-7 fu fd2">
        <div class="row g-2">
          @php $features=[
            ['icon'=>'bi-shield-check','color'=>'#eff6ff','ic'=>'#2563eb','title'=>'Aman & Terpercaya','desc'=>'CSRF protection, rate limiting, dan enkripsi data.'],
            ['icon'=>'bi-people','color'=>'#f5f3ff','ic'=>'#7c3aed','title'=>'Multi Role','desc'=>'Admin, Editor, Reviewer, Author — akses terpisah.'],
            ['icon'=>'bi-journal-check','color'=>'#f0fdf4','ic'=>'#16a34a','title'=>'Peer Review','desc'=>'Double-blind review oleh reviewer berpengalaman.'],
            ['icon'=>'bi-credit-card','color'=>'#fff7ed','ic'=>'#ea580c','title'=>'Pembayaran Manual','desc'=>'Upload bukti transfer, verifikasi admin.'],
            ['icon'=>'bi-search','color'=>'#ecfdf5','ic'=>'#047857','title'=>'Akses Publik','desc'=>'Artikel terpublish dapat diakses siapa saja.'],
            ['icon'=>'bi-graph-up','color'=>'#fefce8','ic'=>'#ca8a04','title'=>'Statistik Real-time','desc'=>'Dashboard statistik untuk semua stakeholder.'],
          ]; @endphp
          @foreach($features as $f)
          <div class="col-12 col-sm-6">
            <div class="feature-item">
              <div class="feature-icon" style="background:{{ $f['color'] }};color:{{ $f['ic'] }};"><i class="{{ $f['icon'] }}"></i></div>
              <div>
                <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:3px;">{{ $f['title'] }}</div>
                <div style="font-size:12px;color:#64748b;line-height:1.5;">{{ $f['desc'] }}</div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── CTA ── --}}
@guest
<section class="cta-section">
  <div class="container" style="max-width:1200px;position:relative;z-index:1;">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-8 text-center fu">
        <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(37,99,235,.2);border:1px solid rgba(37,99,235,.3);padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;color:#93bbfc;margin-bottom:24px;">
          <i class="bi bi-lightning-fill"></i> Mulai Gratis Sekarang
        </div>
        <h2 class="cta-title">Siap Publikasikan Penelitian Anda?</h2>
        <p class="cta-desc" style="max-width:480px;margin:0 auto 36px;">Bergabung dengan ratusan peneliti yang sudah mempercayakan publikasi ilmiah mereka di platform kami.</p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
          <a href="{{ route('register') }}" class="btn-hero-pri" style="background:#2563eb;font-size:14px;padding:12px 28px;">
            <i class="bi bi-person-plus"></i> Daftar Sebagai Author
          </a>
          <a href="{{ route('public.articles.index') }}" class="btn-hero-out" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.15);color:#e6edf3;font-size:14px;padding:12px 28px;">
            <i class="bi bi-eye"></i> Lihat Artikel
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
@endguest

@endsection
