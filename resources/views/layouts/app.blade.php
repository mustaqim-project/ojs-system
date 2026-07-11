<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>{{ $title ?? config('app.name') }} — {{ \App\Models\Setting::get('site_name','OJS') }}</title>
  <meta name="description" content="{{ $description ?? \App\Models\Setting::get('site_description','') }}"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
  <link rel="stylesheet" href="{{ asset('css/ojs.css') }}"/>
  <style>
    /* ── Public-specific tokens ──────────────────────── */
    :root {
      --nav-h: 64px;
    }
    body { font-family: 'Inter', system-ui, sans-serif; color: var(--text-main); background: #fff; }

    /* ── Navigation ──────────────────────────────────── */
    .pub-nav {
      background: #fff;
      border-bottom: 1px solid var(--border);
      position: sticky; top: 0; z-index: 100;
      height: var(--nav-h);
    }
    .pub-nav-inner {
      max-width: 1200px; margin: 0 auto; padding: 0 32px;
      height: 100%; display: flex; align-items: center; gap: 32px;
    }
    .pub-brand {
      display: flex; align-items: center; gap: 12px;
      text-decoration: none; flex-shrink: 0;
    }
    .pub-brand-icon {
      width: 34px; height: 34px;
      background: var(--primary); border-radius: 6px;
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 17px;
    }
    .pub-brand-name {
      font-size: 16px; font-weight: 700;
      color: var(--text-main); letter-spacing: -0.01em;
    }
    .pub-nav-links { display: flex; gap: 4px; flex: 1; }
    .pub-nav-link {
      padding: 6px 12px; border-radius: 6px;
      font-size: 14px; font-weight: 500; color: var(--text-muted);
      text-decoration: none; transition: all 0.15s;
    }
    .pub-nav-link:hover { background: var(--bg-app); color: var(--text-main); }
    .pub-nav-link.active { background: var(--primary-light); color: var(--primary); font-weight: 600; }
    .pub-nav-actions { display: flex; align-items: center; gap: 10px; margin-left: auto; }
    .pub-btn-ghost {
      padding: 7px 16px; border-radius: 6px;
      font-size: 14px; font-weight: 500; color: var(--text-main);
      border: 1px solid var(--border); background: transparent;
      text-decoration: none; transition: all 0.15s;
    }
    .pub-btn-ghost:hover { background: var(--bg-app); color: var(--text-main); }
    .pub-btn-primary {
      padding: 7px 18px; border-radius: 6px;
      font-size: 14px; font-weight: 600; color: #fff;
      background: var(--primary); border: none;
      text-decoration: none; transition: all 0.15s;
    }
    .pub-btn-primary:hover { background: var(--primary-hover); color: #fff; }

    /* ── Flash Banners ───────────────────────────────── */
    .pub-flash {
      padding: 12px 32px; font-size: 14px; font-weight: 500;
      display: flex; align-items: center; gap: 10px;
    }
    .pub-flash-suc { background: var(--success-bg); color: var(--success); border-bottom: 1px solid #C6F6D5; }
    .pub-flash-err { background: var(--danger-bg); color: var(--danger); border-bottom: 1px solid #FEB2B2; }

    /* ── Content Wrapper ─────────────────────────────── */
    .pub-wrap { max-width: 1200px; margin: 0 auto; padding: 48px 32px; }
    .pub-wrap-narrow { max-width: 800px; margin: 0 auto; padding: 48px 32px; }

    /* ── Article Reading Mode ─────────────────────────── */
    .article-body {
      font-family: 'Merriweather', Georgia, serif;
      font-size: 18px; line-height: 1.8;
      color: var(--text-main);
    }
    .article-body h2 { font-size: 22px; font-weight: 700; margin: 36px 0 16px; }
    .article-body h3 { font-size: 18px; font-weight: 700; margin: 28px 0 12px; }
    .article-body p  { margin: 0 0 20px; }
    .article-abstract {
      font-family: 'Inter', sans-serif;
      font-size: 15px; line-height: 1.7;
      color: var(--text-muted);
      border-left: 3px solid var(--primary);
      padding: 16px 20px;
      background: var(--primary-light);
      border-radius: 0 6px 6px 0;
      margin: 24px 0;
    }

    /* ── Journal Cards ───────────────────────────────── */
    .pub-article-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 24px;
      transition: box-shadow 0.2s, border-color 0.2s;
    }
    .pub-article-card:hover {
      box-shadow: var(--shadow-md);
      border-color: #CBD5E1;
    }
    .pub-article-title {
      font-size: 17px; font-weight: 700; color: var(--text-main);
      line-height: 1.4; text-decoration: none;
      display: block; margin-bottom: 8px;
    }
    .pub-article-title:hover { color: var(--primary); }
    .pub-article-meta {
      font-size: 13px; color: var(--text-muted);
      display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 12px;
    }
    .pub-article-abstract {
      font-size: 14px; color: var(--text-muted);
      line-height: 1.6;
      display: -webkit-box; -webkit-box-orient: vertical;
      -webkit-line-clamp: 3; overflow: hidden;
    }
    .pub-keyword-pill {
      display: inline-block;
      padding: 3px 10px; border-radius: 20px;
      background: var(--bg-app); border: 1px solid var(--border);
      font-size: 12px; color: var(--text-muted);
      text-decoration: none; transition: all 0.15s;
      margin: 2px 2px 2px 0;
    }
    .pub-keyword-pill:hover { background: var(--primary-light); border-color: var(--primary); color: var(--primary); }

    /* ── Footer ──────────────────────────────────────── */
    .pub-footer {
      background: #111827; color: #9CA3AF; margin-top: 96px;
      border-top: 1px solid #1F2937;
    }
    .pub-footer-inner { max-width: 1200px; margin: 0 auto; padding: 64px 32px 40px; }
    .pub-footer-brand { font-size: 16px; font-weight: 700; color: #F3F4F6; letter-spacing: -0.01em; margin-bottom: 8px; }
    .pub-footer-desc { font-size: 14px; color: #9CA3AF; line-height: 1.6; max-width: 320px; }
    .pub-footer-hdr { font-size: 12px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #F3F4F6; margin-bottom: 16px; }
    .pub-footer-link { display: block; font-size: 14px; color: #9CA3AF; text-decoration: none; padding: 4px 0; transition: color 0.15s; }
    .pub-footer-link:hover { color: #fff; }
    .pub-footer-divider { border-color: #1F2937; margin: 48px 0 24px; }
    .pub-footer-copy { font-size: 13px; color: #6B7280; }

    /* ── ISSN / DOI Tags ──────────────────────────────── */
    .pub-meta-tag {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 4px 10px; border-radius: 4px;
      font-size: 12px; font-weight: 600; font-family: 'Inter', monospace;
      background: var(--bg-app); border: 1px solid var(--border);
      color: var(--text-muted);
    }

    /* ── Responsive ──────────────────────────────────── */
    @media (max-width: 768px) {
      .pub-nav-links { display: none; }
      .pub-nav-inner { padding: 0 16px; gap: 12px; }
      .pub-wrap { padding: 24px 16px; }
      .pub-wrap-narrow { padding: 24px 16px; }
      .pub-footer-inner { padding: 48px 16px 32px; }
    }
  </style>
  @stack('styles')
</head>
<body>

{{-- PUBLIC NAVIGATION --}}
<nav class="pub-nav" role="navigation" aria-label="Main navigation">
  <div class="pub-nav-inner">
    <a href="{{ route('public.home') }}" class="pub-brand">
      <div class="pub-brand-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
      <div class="pub-brand-name">{{ \App\Models\Setting::get('site_name','OJS System') }}</div>
    </a>
    <div class="pub-nav-links">
      <a href="{{ route('public.home') }}" class="pub-nav-link {{ request()->routeIs('public.home') ? 'active':'' }}">Home</a>
      <a href="{{ route('public.journals.index') }}" class="pub-nav-link {{ request()->routeIs('public.journals*') ? 'active':'' }}">Journals</a>
      <a href="{{ route('public.articles.index') }}" class="pub-nav-link {{ request()->routeIs('public.articles*') ? 'active':'' }}">Articles</a>
      <a href="{{ route('public.search') }}" class="pub-nav-link {{ request()->routeIs('public.search') ? 'active':'' }}">Search</a>
    </div>
    <div class="pub-nav-actions">
      @auth
        <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="pub-btn-ghost"><i class="bi bi-grid me-1"></i>Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
          @csrf
          <button type="submit" class="pub-btn-ghost" style="cursor:pointer;font-family:inherit;">Sign Out</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="pub-btn-ghost">Sign In</a>
        <a href="{{ route('register') }}" class="pub-btn-primary">Submit Manuscript</a>
      @endauth
    </div>
  </div>
</nav>

{{-- FLASH MESSAGES --}}
@if(session('success'))
  <div class="pub-flash pub-flash-suc">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;font-size:16px;">✕</button>
  </div>
@endif
@if(session('error'))
  <div class="pub-flash pub-flash-err">
    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;font-size:16px;">✕</button>
  </div>
@endif

@yield('content')

{{-- FOOTER --}}
<footer class="pub-footer" role="contentinfo">
  <div class="pub-footer-inner">
    <div class="row g-4">
      <div class="col-12 col-md-4">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
          <div style="width:30px;height:30px;background:var(--primary);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;">
            <i class="bi bi-journal-bookmark-fill"></i>
          </div>
          <div class="pub-footer-brand">{{ \App\Models\Setting::get('site_name','OJS System') }}</div>
        </div>
        <p class="pub-footer-desc">{{ \App\Models\Setting::get('site_description','An internationally recognized open-access scholarly publishing platform.') }}</p>
      </div>
      <div class="col-6 col-md-2">
        <div class="pub-footer-hdr">Navigate</div>
        <a href="{{ route('public.home') }}" class="pub-footer-link">Home</a>
        <a href="{{ route('public.journals.index') }}" class="pub-footer-link">Journals</a>
        <a href="{{ route('public.articles.index') }}" class="pub-footer-link">Articles</a>
        <a href="{{ route('public.search') }}" class="pub-footer-link">Search</a>
      </div>
      <div class="col-6 col-md-2">
        <div class="pub-footer-hdr">Authors</div>
        <a href="{{ route('register') }}" class="pub-footer-link">Submit Manuscript</a>
        <a href="{{ route('login') }}" class="pub-footer-link">Author Portal</a>
        <a href="#" class="pub-footer-link">Submission Guide</a>
        <a href="#" class="pub-footer-link">Publication Ethics</a>
      </div>
      <div class="col-12 col-md-4">
        <div class="pub-footer-hdr">Contact</div>
        <p style="font-size:14px;color:#9CA3AF;margin:0;">{{ \App\Models\Setting::get('site_email','editorial@journal.ac.id') }}</p>
        <p style="font-size:14px;color:#9CA3AF;margin:6px 0;">{{ \App\Models\Setting::get('contact_phone','') }}</p>
        <p style="font-size:14px;color:#9CA3AF;margin:6px 0;">{{ \App\Models\Setting::get('contact_address','') }}</p>
      </div>
    </div>
    <hr class="pub-footer-divider"/>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <span class="pub-footer-copy">&copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name','OJS System') }}. All rights reserved.</span>
      <span class="pub-footer-copy">Built with Laravel · Open Access Publishing</span>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
